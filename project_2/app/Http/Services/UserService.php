<?php
namespace App\Http\Services;
use Exception;
use DB;
use App\Http\Models\UserDAO;
use App\Http\Forms\UserForm;
use App\Http\Services\FileService;
use App\Http\Forms\FileForm;
use App\Http\Forms\ImportDataCsvForm;

use App\Http\Forms\UserDetailForm;
use App\Http\Forms\AccountForm;
use App\Http\Forms\ActiveCodeForm;
use App\Http\Forms\NotiEmailForm;
use App\Http\Forms\NotiMobileForm;
use App\Http\Services\UserDetailService;
use App\Http\Services\AccountService;
use App\Http\Services\ActiveCodeService;
use App\Http\Services\NotiEmailService;
use App\Http\Services\NotiMobileService;
use App\Http\Services\MerchantService;

use App\Http\Services\LibService;

class UserService extends BaseService {
    private $userDao;
    private $fileService;
    private $userDetailService;
    private $accountService;
    private $activeCodeService;
    private $notiEmailService;
    private $notiMobileService;
    private $merchantService;
    private $libService;
    public function __construct() {
        $this->userDao = new UserDAO();
        $this->fileService = new FileService();
        
        $this->userDetailService = new UserDetailService();
        $this->accountService = new AccountService();
        $this->activeCodeService = new ActiveCodeService();
        $this->notiEmailService = new NotiEmailService();
        $this->notiMobileService = new NotiMobileService();
        $this->merchantService = new MerchantService();
        
        $this->libService = new LibService();
    }
    
    
    /*
     * insert user
     */
    public function insertUser(UserForm $userForm){
        $user = new UserDAO();
        $user->email = $userForm->getEmail();
        $user->mobile = $userForm->getMobile();
        $user->password = md5($userForm->getPassword());
        $user->source = $userForm->getSource();
        $user->status = $userForm->getStatus();
        return $this->userDao->saveResultId($user);
    }
    /*
     * update user
     */
    public function updateUser(UserForm $userForm){
        $user = $this->userDao->findById($userForm->getId());
        if($userForm->getStatus()!=null){
            $user->status = $userForm->getStatus();
        }
        if($userForm->getToken()!=null){
            $user->token = $userForm->getToken();
        }
        if($userForm->getLastLogin()!=null){
            $user->lastLogin = $userForm->getLastLogin();
        }
        if($userForm->getPassword()!=null){
            $user->password = md5($userForm->getPassword());
        }
        return $this->userDao->saveResultId($user);
    }
    /*
     * update user
     */
    public function resetToken(UserForm $userForm){
        $user = $this->userDao->findById($userForm->getId());
        $user->token = null;
        return $this->userDao->saveResultId($user);
    }
    /*
     * check exist user
     */
    public function checkExistUser(UserForm $userForm){
        if(count($this->userDao->searchData($userForm))>0){
            return true;
        }else
            return false;
    }
    /*
     * search data first
     */
    public function searchDataFirst(UserForm $userForm){
        return $this->userDao->searchDataFirst($userForm);
    }
    /*
     * search list data
     */
    public function searchData(UserForm $searchForm){
        return $this->userDao->searchData($searchForm);
    }
    public function countListData(UserForm $searchForm){
        $searchForm->setPageSize(null);
        return count($this->userDao->searchData($searchForm));
    }
    /*
     * get Data By Id
     */
    public function getDataById($id){
        return $this->userDao->findById($id);
    }
    
    
    /*
     * thực thi file csv
     */
    public function processFileCsv(FileForm $fileForm){
        //echo $fileForm->getHashcode();die();
        $date = date(env('DATE_FORMAT_Y_M_D'));
        $contentLog = $date."\n";
        $folder = $fileForm->getMerchantHashcode();
        $pathLog = '/public/uploads/merchant/'.$folder.'/logs';
        $name = strtotime($date).'_'.str_replace(".", "", $fileForm->getName()).'.txt';
        try {
            $fileDao = $this->fileService->findDataByHashcode($fileForm->getHashcode());
            //var_dump($fileDao);die();
            //$fileDao->status = env('COMMON_STATUS_SUCCESS');
            $path = $fileDao->file;
        } catch (Exception $ex) {
            throw Exception($ex->getMessage());
        }
        $row = 1;
        $handle = fopen(base_path().$path, "r");
        if (($handle) !== FALSE) {
            $key = 0;
            //var_dump(fgetcsv($handle, 1000, ","));die();
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $key++;
                $num = count($data);
               // echo "<p> $num fields in line $row: <br /></p>\n"; die();     
                $row++;
                for ($c=0; $c < $num; $c++) {
                    $blackpowder = $data;
                    $dynamit = implode(";", $blackpowder);
                    $pieces = explode(";", $dynamit);
                    $col1 = $pieces[0];
                    $col2 = $pieces[1];
                    $col3 = $pieces[2];
                    $col4 = $pieces[3];
                }
                if(substr( $col3, 0, 1)!="0")$col3="0".$col3;
                
                //idRef,email,mobile,amount,createdTime,hashcodeMerchant,hashcodePartner
                $importDataCsvForm = new ImportDataCsvForm();
                $importDataCsvForm->setFullname($col1);
                $importDataCsvForm->setEmail($col2);
                $importDataCsvForm->setMobile($col3);
                $importDataCsvForm->setAddress($col4);
                
                $userForm = new UserForm();
                //$userForm->setPassword($userInfo['password']);
                $userForm->setEmail($col2);
                $userForm->setMobile($col3);
                /*
                 * Kiểm tra tồn tại email/mobile
                 */
                if($this->checkExistUser($userForm)){
                    $contentLog .= $key.' : Da ton tai '.$userForm->getEmail().'-'.$userForm->getMobile()."\n";
                    //throw new Exception($key.' : Da ton tai '.$userForm->getEmail().'-'.$userForm->getMobile());
                    continue;
                }
                $password = $this->libService->getPasswordRandom();
                DB::beginTransaction();
                try {
                    /*
                    * insert user
                    */
                   $userForm->setStatus(env('COMMON_STATUS_INACTIVE'));
                   $userForm->setPassword($password);
                   $userForm->setSource("IMPORT_CSV");
                   $user = $this->insertUser($userForm);
                   /*
                    * insert user detail default
                    */
                   $userDetailForm = new UserDetailForm();
                   $userDetailForm->setUserId($user->id);
                   $userDetailForm->setFirstName($col1);
                   $userDetailForm->setMobile($userForm->getMobile());
                   $userDetailForm->setEmail($userForm->getEmail());
                   if(isset($userInfo['lat'])&&$userInfo['lat']!=null){
                       $userDetailForm->setLat($userInfo['lat']);
                   }
                   if(isset($userInfo['long'])&&$userInfo['long']!=null){
                       $userDetailForm->setLong($userInfo['long']);
                   }
                   $userDetailForm->setType("DEFAULT");
                   $userDetailForm->setStatus(env('COMMON_STATUS_ACTIVE'));
                   $userDetail = $this->userDetailService->insertUserDetail($userDetailForm);
                   /*
                    * insert Account
                    */
                   $accountForm = new AccountForm();
                   $accountForm->setType(env('ACCOUNT_TYPE_MAIN'));
                   $accountForm->setIdRef($userDetail->id);
                   $accountForm->setStatus(env('COMMON_STATUS_ACTIVE'));
                   $accountForm->setGroup(env('TYPE_USER'));
                   $accountForm->setBalance($this->libService->getCointRandom());
                   $account = $this->accountService->insertAccount($accountForm);
                   /*
                    * insert active code
                    */
                   $activeCodeForm = new ActiveCodeForm();
                   $activeCodeForm->setType(env('TYPE_USER'));
                   $activeCodeForm->setIdRef($user->id);
                   $activeCodeForm->setStatus(env('COMMON_STATUS_ACTIVE'));
                   $activeCode = $this->activeCodeService->insertActiveCode($activeCodeForm);
                   /*
                    * insert noti email
                    */
                   if($userForm->getEmail()!=null){
                       $notiEmailForm = new NotiEmailForm();
                       $notiEmailForm->setType("CREATE_USER");
                       $content = array(
                           'templateName'=>'createUser',
                           'title'=>trans('noti.create_user'),
                           'lang'=>'vi',
                           'sendTo'=>$userForm->getEmail(),
                           'fullName'=>$userForm->getEmail(),
                           'activeCode'=>$activeCode->activeCode,
                           'username'=>$userForm->getEmail()
                       );
                       $notiEmailForm->setStatus(env('COMMON_STATUS_PENDING'));
                       $notiEmailForm->setContent(json_encode($content));
                       $notiEmail = $this->notiEmailService->insertNotiEmail($notiEmailForm);
                   }
                   /*
                    * insert noti mobile
                    */
                   if($userForm->getMobile()!=null){
                       $notiMobileForm = new NotiMobileForm();
                       $notiMobileForm->setType("CREATE_USER");
                       $content = array(
                           'templateName'=>'createUser',
                           'lang'=>'vi',
                           'sendTo'=>$userForm->getMobile(),
                           'activeCode'=>$activeCode->activeCode,
                           'password'=>$password
                       );
                       $notiMobileForm->setStatus(env('COMMON_STATUS_PENDING'));
                       $notiMobileForm->setContent(json_encode($content));
                       $notiMobile = $this->notiMobileService->insertNotiMobile($notiMobileForm);
                   }
                   // cap nhat lai file vua thuc thi
                   DB::commit();
                   $contentLog .= $key.' : thanh cong '.$userForm->getEmail().'-'.$userForm->getMobile()."\n";
                } catch (Exception $ex) {
                    DB::rollback();
                    $contentLog .= $key.' : error : '.$ex->getMessage().' '.$userForm->getEmail().'-'.$userForm->getMobile()."\n";
                }
                
            }
            $updateFileForm = new FileForm();
            $updateFileForm->setHashcode($fileDao->hashcode);
            $updateFileForm->setFileLog($pathLog.'/'.$name);
            $updateFileForm->setStatus(env('COMMON_STATUS_SUCCESS'));
            $this->fileService->updateData($updateFileForm);
            $this->libService->writeLog($contentLog, $pathLog, $name);
        }
        
    }
    
    
}

