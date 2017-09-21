<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\MerchantForm;
use App\Http\Models\MerchantDAO;
use App\Http\Services\AccountService;
use App\Http\Models\AccountDAO;
use App\Http\Forms\AccountForm;
use App\Http\Services\ActiveCodeService;
use App\Http\Models\ActiveCodeDAO;
use App\Http\Forms\ActiveCodeForm;
use App\Http\Services\NotiEmailService;
use App\Http\Models\NotiEmailDAO;
use App\Http\Forms\NotiEmailForm;
use App\Http\Services\NotiMobileService;
use App\Http\Models\NotiMobileDAO;
use App\Http\Forms\NotiMobileForm;
use App\Http\Services\LibService;

use Session;
use Exception;
use DB;
class MerchantService extends BaseService {
    private $partnerService;
    private $libService;
    private $merchantDao;
    public function __construct() {
        $this->merchantDao = new MerchantDAO();
        $this->accountService = new AccountService();
        $this->activeCodeService = new ActiveCodeService();
        $this->notiEmailService = new NotiEmailService();
        $this->notiMobileService = new NotiMobileService();
        $this->libService = new LibService();
    }
    
    public function checkExistEmail($email){
        return $this->merchantDao->checkExistEmail($email);
    }
    
    public function checkExistMobile($mobile){
        return $this->merchantDao->checkExistMobile($mobile);
    }
    
    /*
     * create merchant 
     * Hàm dùng chung
     */
    public function createMerchant(MerchantForm $merchantForm){
        $merchantDao = new MerchantDAO();
        $merchantDao->email = $merchantForm->getEmail();
        $merchantDao->mobile = $merchantForm->getMobile();
        $merchantDao->firstName = $merchantForm->getFirstName();
        $merchantDao->lastName = $merchantForm->getLastName();
        $merchantDao->name = $merchantForm->getName();
        $merchantDao->address = $merchantForm->getAddress();
        $merchantDao->provinceId = $merchantForm->getProvinceId();
        $merchantDao->districtId = $merchantForm->getDistrictId();
        $merchantDao->wardId = $merchantForm->getWardId();
        $merchantDao->lat = $merchantForm->getLat();
        $merchantDao->long = $merchantForm->getLong();
        $merchantDao->status = $merchantForm->getStatus();
        $merchantDao->password = md5($merchantForm->getPassword());
        return $this->merchantDao->saveResultId($merchantDao);
    }
    
    /*
     * Insert Merchant
     */
    public function insertMerchant(MerchantForm $merchantForm){
        $merchantDao = new MerchantDAO();
        DB::beginTransaction();
        try {
            $passwordRandom = $this->libService->getPasswordRandom();
            $merchantForm->setPassword($passwordRandom);
            $merchantDao = $this->createMerchant($merchantForm);
            if($merchantDao!=null){
                $merchantForm->setHashcode($merchantDao->hashcode);
                /*
                * tạo folder images
                */
                if($merchantForm->getImages()!=null)
                {
                    
                    $images = $this->libService->uploadFile($merchantForm->getImages(),'merchant/'.$merchantDao->hashcode);
                    $merchantDao->images = $images;
                    $this->merchantDao->saveResultId($merchantDao);
                }
                /*
                 * insert Account
                 */
                $accountDao = new AccountDAO();
                $accountForm = new AccountForm();
                $accountForm->setType(env('ACCOUNT_TYPE_MAIN'));
                $accountForm->setStatus($merchantForm->getStatus());
                $accountForm->setIdRef($merchantDao->id);
                $accountForm->setGroup(env('TYPE_MERCHANT'));
                $accountDao = $this->accountService->insertAccount($accountForm);
                /*
                 * insert ActiveCode
                 */
                $activeCodeDao = new ActiveCodeDAO();
                $activeCodeForm = new ActiveCodeForm();
                $activeCodeForm->setType(env('TYPE_MERCHANT'));
                $activeCodeForm->setIdRef($merchantDao->id);
                $activeCodeForm->setStatus(env('COMMON_STATUS_INACTIVE'));   
                $activeCodeDao=$this->activeCodeService->insertActiveCode($activeCodeForm);
                /*
                 * insert noti
                 */
                if($merchantForm->getEmail()!=null){
                    $notiEmailDao = new NotiEmailDAO();
                    $notiEmailForm = new NotiEmailForm();
                    $notiEmailForm->setType(env('NOTI_TYPE_CREATE_MERCHANT'));
                    // set content
                    $content = array(
                        'templateName'=>'createMerchant',
                        'title'=>trans('noti.create_merchant'),
                        'lang'=>'vi',
                        'sendTo'=>$merchantForm->getEmail(),
                        'fullName'=>$merchantForm->getFullName(),
                        //'activeCode'=>$activeCodeDao->activeCode,
                        'username'=>$merchantForm->getEmail(),
                        'password'=>$passwordRandom,
                        'linkActive'=>Asset('/'.env('PREFIX_ADMIN_MERCHANT'))
                        //'linkActive'=>Asset('/'.env('PREFIX_ADMIN_PORTAL').'/activeCode/'.$merchantDao->hashcode)
                    );
                    $notiEmailForm->setContent(json_encode($content));
                    $notiEmailForm->setMerchantId($merchantDao->id);
                    $notiEmailForm->setStatus(env('COMMON_STATUS_PENDING'));
                    $notiEmailDao=$this->notiEmailService->insertNotiEmail($notiEmailForm);
                    // thu nghiem email
                    $this->notiEmailService->sendMail($notiEmailDao->hashcode);
                }
                if($merchantForm->getMobile()!=null){
                    $notiMobileDao = new NotiMobileDAO();
                    $notiMobileForm = new NotiMobileForm();
                    $notiMobileForm->setType(env('NOTI_TYPE_CREATE_MERCHANT'));
                    // set content
                    $content = array(
                        'templateName'=>'createMerchant',
                        'title'=>trans('noti.create_merchant'),
                        'lang'=>'vi',
                        'sendTo'=>$merchantForm->getMobile(),
                        'fullName'=>$merchantForm->getFullName(),
                        //'activeCode'=>$activeCodeDao->activeCode,
                        'username'=>$merchantForm->getEmail(),
                        'password'=>$passwordRandom,
                        'linkActive'=>Asset('/'.env('PREFIX_ADMIN_MERCHANT'))
                        //'linkActive'=>Asset('/'.env('PREFIX_ADMIN_PORTAL').'/activeCode/'.$merchantDao->hashcode)
                    );
                    $notiMobileForm->setContent(json_encode($content));
                    $notiMobileForm->setMerchantId($merchantDao->id);
                    $notiMobileForm->setStatus(env('COMMON_STATUS_PENDING'));
                    $notiMobileDao=$this->notiMobileService->insertNotiMobile($notiMobileForm);
                }
            }
              
            DB::commit();
            
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception($ex->getMessage());
        }
        
        return $merchantDao;
    }
    
    /*
     * get list merchant
     */
    public function getList(MerchantForm $searchForm){
        $searchForm->setPageSize(env('PAGE_SIZE'));
        return $this->merchantDao->getList($searchForm);
    }
    
    /*
     * get list merchant
     */
    public function getCount(MerchantForm $searchForm){
        $searchForm->setPageSize(null);
        return count($this->merchantDao->getList($searchForm));
    }
    
    /*
     * check login
     */
    public function checkLogin($email,$password){
        return $this->merchantDao->checkLogin($email,$password);
    }
    
    /*
     * Login
     */
    public function login(MerchantForm $merchantForm){
        $merchantDao = $this->merchantDao->checkLogin($merchantForm->getEmail(),$merchantForm->getPassword());
        /*
         * Bản ghi hợp lệ
         */
        if($merchantDao!=null){
            /*
             * get partner default
             */
            //$partnerDao = new PartnerDAO();
            //$partnerDao = $this->partnerService->getPartnerByType($merchantDao->id, env('PARTNER_DEFAULT'));
            // set last login
            $merchantDao->lastLogin = date(env('DATE_FORMAT_Y_M_D'));
            $this->merchantDao->saveResultId($merchantDao);
            // set session
            $merchantForm = new MerchantForm();
            $merchantForm->setId($merchantDao->id);
            $merchantForm->setEmail($merchantDao->email);
            $merchantForm->setMobile($merchantDao->mobile);
            $merchantForm->setLastLogin($merchantDao->lastLogin);
            $merchantForm->setStatus($merchantDao->status);
            $merchantForm->setFirstName($merchantDao->firstName);
            $merchantForm->setLastName($merchantDao->lastName);
            $merchantForm->setAddress($merchantDao->address);
            $merchantForm->setHashcode($merchantDao->hashcode);
            //if($partnerDao!=null)
            //    $merchantForm->setPartnerDefault($partnerDao->id);
            /*
             * set session
             */
            Session::put("login_admin_merchant", $merchantForm);
        }
        return $merchantDao;
    }
    
    /*
     * get merchant by hashcode
     */
    public function getMerchantByHashcode($hashcode){
        return $this->merchantDao->getObjByHashcode($hashcode);
    }
    
    /*
     * get first merchant
     */
    public function getFirstData(MerchantForm $searchForm){
        return $this->merchantDao->getFirstData($searchForm);
    }
    
    /*
     * get list merchant
     */
    public function searchListData(MerchantForm $searchForm){
        return $this->merchantDao->searchListData($searchForm);
    }
}

