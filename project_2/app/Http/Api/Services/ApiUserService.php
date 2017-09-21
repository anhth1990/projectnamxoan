<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Services\LibService;
use App\Http\Forms\UserForm;
use App\Http\Forms\UserDetailForm;
use App\Http\Forms\AccountForm;
use App\Http\Forms\ActiveCodeForm;
use App\Http\Forms\NotiEmailForm;
use App\Http\Forms\NotiMobileForm;
use App\Http\Services\UserService;
use App\Http\Services\UserDetailService;
use App\Http\Services\AccountService;
use App\Http\Services\ActiveCodeService;
use App\Http\Services\NotiEmailService;
use App\Http\Services\NotiMobileService;
use App\Http\Services\MerchantService;

class ApiUserService extends BaseService {
    private $libService;
    private $userService;
    private $userDetailService;
    private $accountService;
    private $activeCodeService;
    private $notiEmailService;
    private $notiMobileService;
    private $merchantService;
    public function __construct() {
        $this->libService = new LibService();
        $this->userService = new UserService();
        $this->userDetailService = new UserDetailService();
        $this->accountService = new AccountService();
        $this->activeCodeService = new ActiveCodeService();
        $this->notiEmailService = new NotiEmailService();
        $this->notiMobileService = new NotiMobileService();
        $this->merchantService = new MerchantService();
    }
    
    /*
     * Create user
     */
    public function createUser($request){
        $language = $request['language'];
        $response = array();
        if(!isset($request["userInfo"])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $userInfo= $request["userInfo"];
        if(!isset($userInfo['username']) || $userInfo['username']==null){
            throw new Exception(trans('exception.USERNAME_REQUIRED'),$this->libService->setError("USERNAME_REQUIRED"));
        }
        if(!isset($userInfo['password']) || $userInfo['password']==null){
            throw new Exception(trans('exception.PASSWORD_REQUIRED'),$this->libService->setError("PASSWORD_REQUIRED"));
        }
        if(!preg_match($this->PATTERN_PASSWORD, $userInfo['password'])){
            throw new Exception(trans('exception.PASSWORD_PATTERN'),$this->libService->setError("PASSWORD_PATTERN"));
        }
        if(!isset($userInfo['passwordConfirm']) || $userInfo['passwordConfirm']==null){
            throw new Exception(trans('exception.PASSWORD_CONFIRM_REQUIRED'),$this->libService->setError("PASSWORD_CONFIRM_REQUIRED"));
        }
        if($userInfo['passwordConfirm']!=$userInfo['password']){
            throw new Exception(trans('exception.PASSWORD_CONFIRM_WRONG'),$this->libService->setError("PASSWORD_CONFIRM_WRONG"));
        }
        if(!$this->emailOrMobile($userInfo['username'])){
            throw new Exception(trans('exception.USERNAME_INVALID'),$this->libService->setError("USERNAME_INVALID"));
        }
        $userForm = new UserForm();
        $userForm->setPassword($userInfo['password']);
        if(preg_match($this->PATTERN_FORMAT_EMAIL, $userInfo['username'])){
            $userForm->setEmail($userInfo['username']);
        }
        if(preg_match($this->PATTERN_IS_MOBILE, $userInfo['username'])){
            $userForm->setMobile($userInfo['username']);
        }
        /*
         * Kiểm tra tồn tại email/mobile
         */
        if($this->userService->checkExistUser($userForm)){
            throw new Exception(trans('exception.USER_EXIST'),$this->libService->setError("USER_EXIST"));
        }
        
        DB::beginTransaction();
        try {
            /*
             * insert user
             */
            $userForm->setStatus(env('COMMON_STATUS_INACTIVE'));
            $userForm->setPassword($userForm->getPassword());
            $userForm->setSource("APPLICATION");
            $user = $this->userService->insertUser($userForm);
            /*
             * insert user detail default
             */
            $userDetailForm = new UserDetailForm();
            $userDetailForm->setUserId($user->id);
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
                    'activeCode'=>$activeCode->activeCode
                );
                $notiMobileForm->setStatus(env('COMMON_STATUS_PENDING'));
                $notiMobileForm->setContent(json_encode($content));
                $notiMobile = $this->notiMobileService->insertNotiMobile($notiMobileForm);
            }
            DB::commit();
            /*
             * send mail active
             */
            if($userForm->getEmail()!=null){
                $this->notiEmailService->sendMail($notiEmail->hashcode);
            }
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }  
        
        
    }
    
    /*
     * active user
     */
    public function activeUser($request){
        $language = $request['language'];
        $response = array();
        if(!isset($request["userInfo"])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $userInfo= $request["userInfo"];
        if(!isset($userInfo['username']) || $userInfo['username']==null){
            throw new Exception(trans('exception.USERNAME_REQUIRED'),$this->libService->setError("USERNAME_REQUIRED"));
        }
        if(!isset($userInfo['activeCode']) || $userInfo['activeCode']==null){
            throw new Exception(trans('exception.ACTIVE_CODE_REQUIRED'),$this->libService->setError("ACTIVE_CODE_REQUIRED"));
        }
        /* định danh user */
        $userForm = new UserForm();
        if(preg_match($this->PATTERN_FORMAT_EMAIL, $userInfo['username'])){
            $userForm->setEmail($userInfo['username']);
        }
        if(preg_match($this->PATTERN_IS_MOBILE, $userInfo['username'])){
            $userForm->setMobile($userInfo['username']);
        }
        $user = $this->userService->searchDataFirst($userForm);
        if($user==null){
            throw new Exception(trans('exception.USER_NOT_FOUND'),$this->libService->setError("USER_NOT_FOUND"));
        }
        /*
         * định danh tồn tại active code tương ứng với người dùng
         */
        $activeCodeForm = new ActiveCodeForm();
        $activeCodeForm->setActiveCode($userInfo['activeCode']);
        $activeCodeForm->setType(env('TYPE_USER'));
        $activeCodeForm->setIdRef($user->id);
        $activeCodeForm->setStatus(env('COMMON_STATUS_ACTIVE'));
        $activeCode = $this->activeCodeService->searchDataFirst($activeCodeForm);
        if($activeCode==null){
            throw new Exception(trans('exception.ACTIVE_CODE_NOT_FOUND'),$this->libService->setError("ACTIVE_CODE_NOT_FOUND"));
        }
        DB::beginTransaction();
        try {
            /*
             * cập nhật trạng thái người dùng
             */
            $token = $this->getToken($user->hashcode);
            $userForm->setId($user->id);
            $userForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            $userForm->setToken($token);
            $userForm->setLastLogin(date(env('DATE_FORMAT_Y_M_D'),time()));
            $this->userService->updateUser($userForm);
            /*
             * cập nhật trạng thái mã active code 
             * từ active -> inactive
             */
            $activeCodeForm->setId($activeCode->id);
            $activeCodeForm->setStatus(env('COMMON_STATUS_INACTIVE'));
            $this->activeCodeService->updateActiveCode($activeCodeForm);
            /*
             * cập nhật trạng thái user detail default
             * với type là default sẽ luôn luôn chỉ có 1 bản ghi
             */
            $userDetailForm = new UserDetailForm();
            $userDetailForm->setType("DEFAULT");
            $userDetailForm->setUserId($user->id);
            $userDetailForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            $this->userDetailService->updateUserDetailActive($userDetailForm);
            DB::commit();
            /*
             * thực hiện đăng nhập luôn
             * nếu kích hoạt thành công
             */
            $response['userInfo']['token'] = $token;
            $response['userInfo']['email'] = $user->email;
            $response['userInfo']['mobile'] = $user->mobile;
            $response['userInfo']['status'] = $user->status;
            $response['userInfo']['userId'] = $user->id;
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
        
    }
    /*
     * login user
     */
    public function loginUser($request){
        $language = $request['language'];
        $response = array();
        if(!isset($request["userInfo"])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $userInfo= $request["userInfo"];
        if(!isset($userInfo['username']) || $userInfo['username']==null){
            throw new Exception(trans('exception.USERNAME_REQUIRED'),$this->libService->setError("USERNAME_REQUIRED"));
        }
        if(!isset($userInfo['password']) || $userInfo['password']==null){
            throw new Exception(trans('exception.PASSWORD_REQUIRED'),$this->libService->setError("PASSWORD_REQUIRED"));
        }
        /* định danh user */
        $userForm = new UserForm();
        if(preg_match($this->PATTERN_FORMAT_EMAIL, $userInfo['username'])){
            $userForm->setEmail($userInfo['username']);
        }
        if(preg_match($this->PATTERN_IS_MOBILE, $userInfo['username'])){
            $userForm->setMobile($userInfo['username']);
        }
        $userForm->setPassword(md5($userInfo['password']));
        $user = $this->userService->searchDataFirst($userForm);
        if($user==null){
            throw new Exception(trans('exception.USERNAME_OR_PASSWORD_WRONG'),$this->libService->setError("USERNAME_OR_PASSWORD_WRONG"));
        }
        DB::beginTransaction();
        try {
            /*
             * cập nhật trạng thái người dùng
             */
            $token = $this->getToken($user->hashcode);
            $userForm->setId($user->id);
            $userForm->setToken($token);
            $userForm->setLastLogin(date(env('DATE_FORMAT_Y_M_D'),time()));
            $userForm->setPassword(null);
            $this->userService->updateUser($userForm);
            DB::commit();
            /*
             * thực hiện đăng nhập luôn
             * nếu kích hoạt thành công
             */
            $response['userInfo']['token'] = $token;
            $response['userInfo']['email'] = $user->email;
            $response['userInfo']['mobile'] = $user->mobile;
            $response['userInfo']['status'] = $user->status;
            $response['userInfo']['userId'] = $user->id;
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    /*
     * forgot password
     */
    public function forgotPassword($request){
        $language = $request['language'];
        $response = array();
        if(!isset($request["userInfo"])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $userInfo= $request["userInfo"];
        if(!isset($userInfo['username']) || $userInfo['username']==null){
            throw new Exception(trans('exception.USERNAME_REQUIRED'),$this->libService->setError("USERNAME_REQUIRED"));
        }
        /* định danh user */
        $userForm = new UserForm();
        if(preg_match($this->PATTERN_FORMAT_EMAIL, $userInfo['username'])){
            $userForm->setEmail($userInfo['username']);
        }
        if(preg_match($this->PATTERN_IS_MOBILE, $userInfo['username'])){
            $userForm->setMobile($userInfo['username']);
        }
        $user = $this->userService->searchDataFirst($userForm);
        if($user==null){
            throw new Exception(trans('exception.USER_NOT_FOUND'),$this->libService->setError("USER_NOT_FOUND"));
        }
        DB::beginTransaction();
        try {
            /*
             * cập nhật lại mật khẩu cho người dùng
             */
            $password = $this->libService->getPasswordRandom();
            $userForm->setId($user->id);
            $userForm->setPassword($password);
            $this->userService->updateUser($userForm);
            $this->userService->resetToken($userForm);
            /*
             * insert noti email
             */
            if($userForm->getEmail()!=null){
                $notiEmailForm = new NotiEmailForm();
                $notiEmailForm->setType("FORGOT_PASSWORD");
                $content = array(
                    'templateName'=>'forgotPassword',
                    'title'=>trans('noti.forgot_password'),
                    'lang'=>'vi',
                    'sendTo'=>$userForm->getEmail(),
                    'fullName'=>$userForm->getEmail(),
                    'password'=>$password,
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
                $notiMobileForm->setType("FORGOT_PASSWORD");
                $content = array(
                    'templateName'=>'forgotPassword',
                    'lang'=>'vi',
                    'sendTo'=>$userForm->getMobile(),
                    'password'=>$password
                );
                $notiMobileForm->setStatus(env('COMMON_STATUS_PENDING'));
                $notiMobileForm->setContent(json_encode($content));
                $notiMobile = $this->notiMobileService->insertNotiMobile($notiMobileForm);
            }
            DB::commit();
            /*
             * gửi mail luôn cho người dùng
             */
            if($userForm->getEmail()!=null){
                $this->notiEmailService->sendMail($notiEmail->hashcode);
            }
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    /*
     * resend ative code
     */
    public function resendActiveCode($request){
        $language = $request['language'];
        $response = array();
        if(!isset($request["userInfo"])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $userInfo= $request["userInfo"];
        if(!isset($userInfo['username']) || $userInfo['username']==null){
            throw new Exception(trans('exception.USERNAME_REQUIRED'),$this->libService->setError("USERNAME_REQUIRED"));
        }
        /* định danh user */
        $userForm = new UserForm();
        if(preg_match($this->PATTERN_FORMAT_EMAIL, $userInfo['username'])){
            $userForm->setEmail($userInfo['username']);
        }
        if(preg_match($this->PATTERN_IS_MOBILE, $userInfo['username'])){
            $userForm->setMobile($userInfo['username']);
        }
        $user = $this->userService->searchDataFirst($userForm);
        if($user==null){
            throw new Exception(trans('exception.USER_NOT_FOUND'),$this->libService->setError("USER_NOT_FOUND"));
        }
        DB::beginTransaction();
        try {
            /*
             * cập nhật mã kích hoạt mới
             */
            $activeCodeForm = new ActiveCodeForm();
            $activeCodeForm->setType(env('TYPE_USER'));
            $activeCodeForm->setIdRef($user->id);
            $activeCode = $this->activeCodeService->searchDataFirst($activeCodeForm);
            $activeCodeForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            if($activeCode==null){
                // nếu chưa có mã thì tạo bản ghi active code mới
                $activeCode = $this->activeCodeService->insertActiveCode($activeCodeForm);
            }else{
                // cập nhật lại status active code
                $activeCodeForm->setId($activeCode->id);
                $activeCodeForm->setCreatedDate(date(env('DATE_FORMAT_Y_M_D'),time()));
                $this->activeCodeService->updateActiveCode($activeCodeForm);
            }
            /*
             * insert noti email
             */
            if($userForm->getEmail()!=null){
                $notiEmailForm = new NotiEmailForm();
                $notiEmailForm->setType("RESEND_ACTIVE_CODE");
                $content = array(
                    'templateName'=>'resendActiveCode',
                    'title'=>trans('noti.resend_active_code'),
                    'lang'=>'vi',
                    'sendTo'=>$userForm->getEmail(),
                    'activeCode'=>$activeCode->activeCode
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
                $notiMobileForm->setType("RESEND_ACTIVE_CODE");
                $content = array(
                    'templateName'=>'resendActiveCode',
                    'lang'=>'vi',
                    'sendTo'=>$userForm->getMobile(),
                    'activeCode'=>$activeCode->activeCode
                );
                $notiMobileForm->setStatus(env('COMMON_STATUS_PENDING'));
                $notiMobileForm->setContent(json_encode($content));
                $notiMobile = $this->notiMobileService->insertNotiMobile($notiMobileForm);
            }
            DB::commit();
            /*
             * gửi mail luôn cho người dùng
             */
            if($userForm->getEmail()!=null){
                $this->notiEmailService->sendMail($notiEmail->hashcode);
            }
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    /*
     * email or mobile
     */
    public function emailOrMobile($username){
        if(preg_match($this->PATTERN_IS_MOBILE, $username) || preg_match($this->PATTERN_FORMAT_EMAIL, $username))
            return true;
        else return false;
    }
    /*
     * check token user
     */
    public function checkTokenUser($token){
        $userForm = new UserForm();
        $userForm->setToken($token);
        $userForm->setStatus(env('COMMON_STATUS_ACTIVE'));
        return $this->userService->searchDataFirst($userForm);
    }
    /*
     * update user
     */
    public function updateTimeLogin($timeLogin,$userId){
        $userForm = new UserForm();
        $userForm->setId($userId);
        $userForm->setLastLogin($timeLogin);
        return $this->userService->updateUser($userForm);
    }
    /*
     * create user detail - account
     */
    public function createUserDetail($request){
        $language = $request['language'];
        $user_id = $request['userId'];
        $response = array();
        /*
        * kiểm tra mã merchant
        */
        if(!isset($request['merchantCode']) || $request['merchantCode'] == null){
            throw new Exception(trans('exception.INVALID_PARAMETER')."_merchantCode",$this->libService->setError("TOKEN_NOT_FOUND"));
        }
        $merchantCode = $request['merchantCode'];
        $merchant = $this->merchantService->getMerchantByHashcode($merchantCode);
        if($merchant==null){
            throw new Exception(trans('exception.MERCHANT_NOT_FOUND'),$this->libService->setError("MERCHANT_NOT_FOUND"));
        }
        $merchantId = $merchant->id;
        /*
        * --- end kiểm tra merchant
        */
        $user = $this->userService->getDataById($user_id);
        
        DB::beginTransaction();
        try {
            /*
            * kiểm tra tồn tại
            * user detail
            */
            $userDetailForm = new UserDetailForm();
            $userDetailForm->setUserId($user->id);
            $userDetailForm->setMerchantId($merchantId);
            $userDetailForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            $userDetail = $this->userDetailService->searchFirstData($userDetailForm );
            /*
             * đã tồn tại user detail
             */
            if($userDetail==null){
                /*
                * insert user detail ( user thuộc merchant )
                */
                $userDetailForm->setMobile($user->mobile);
                $userDetailForm->setEmail($user->email);
                $userDetailForm->setLat($user->lat);
                $userDetailForm->setLong($user->long);
                $userDetailForm->setType("USER");
                $userDetail = $this->userDetailService->insertUserDetail($userDetailForm);
                /*
                 * insert Account
                 */
                $accountForm = new AccountForm();
                $accountForm->setType(env('ACCOUNT_TYPE_MAIN'));
                $accountForm->setIdRef($userDetail->id);
                $accountForm->setStatus(env('COMMON_STATUS_ACTIVE'));
                $accountForm->setGroup(env('TYPE_USER'));
                $account = $this->accountService->insertAccount($accountForm);
            }
            DB::commit();
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    /*
     * change password
     */
    public function changePassword($request){
        $language = $request['language'];
        $userId = $request['userId'];
        $merchantId = $request['merchantId'];
        $userDetailId = $request['userDetailId'];
        $response = array();
        $user = $this->userService->getDataById($userId);
        if(!isset($request["userInfo"])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $userInfo= $request["userInfo"];
        if(!isset($userInfo['passwordOld']) || $userInfo['passwordOld']==null){
            throw new Exception(trans('exception.PASSWORD_OLD_REQUIRED'),$this->libService->setError("PASSWORD_OLD_REQUIRED"));
        }
        if(md5($userInfo['passwordOld'])!=$user->password){
            throw new Exception(trans('exception.PASSWORD_OLD_WRONG'),$this->libService->setError("PASSWORD_OLD_WRONG"));
        }
        if(!isset($userInfo['passwordNew']) || $userInfo['passwordNew']==null){
            throw new Exception(trans('exception.PASSWORD_NEW_REQUIRED'),$this->libService->setError("PASSWORD_NEW_REQUIRED"));
        }
        if(!preg_match($this->PATTERN_PASSWORD, $userInfo['passwordNew'])){
            throw new Exception(trans('exception.PASSWORD_PATTERN'),$this->libService->setError("PASSWORD_PATTERN"));
        }
        if(!isset($userInfo['passwordNewConfirm']) || $userInfo['passwordNewConfirm']==null){
            throw new Exception(trans('exception.PASSWORD_NEW_CONFIRM_REQUIRED'),$this->libService->setError("PASSWORD_NEW_CONFIRM_REQUIRED"));
        }
        if($userInfo['passwordNewConfirm']!=$userInfo['passwordNew']){
            throw new Exception(trans('exception.PASSWORD_CONFIRM_WRONG'),$this->libService->setError("PASSWORD_CONFIRM_WRONG"));
        }
        DB::beginTransaction();
        try {
            $userForm = new UserForm();
            $userForm->setId($userId);
            $userForm->setPassword($userInfo['passwordNew']);
            $this->userService->updateUser($userForm);
            DB::commit();
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    /*
     * update info user
     */
    public function updateInfoUser($request){
        $language = $request['language'];
        $userId = $request['userId'];
        $merchantId = $request['merchantId'];
        $userDetailId = $request['userDetailId'];
        $response = array();
        $user = $this->userService->getDataById($userId);
        if(!isset($request["userInfo"])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $userForm = new UserForm();
        if(isset($request["userInfo"]['lastName']) && $request["userInfo"]['lastName']!=null){
            $userForm->setLastName($request["userInfo"]['lastName']);
        }
        if(isset($request["userInfo"]['firstName']) && $request["userInfo"]['firstName']!=null){
            $userForm->setFirstName($request["userInfo"]['firstName']);
        }
        if(isset($request["userInfo"]['gender']) && $request["userInfo"]['gender']!=null){
            $userForm->setGender($request["userInfo"]['gender']);
        }
        if(isset($request["userInfo"]['provinceId']) && $request["userInfo"]['provinceId']!=null){
            $userForm->setProvinceId($request["userInfo"]['provinceId']);
        }
        if(isset($request["userInfo"]['districtId']) && $request["userInfo"]['districtId']!=null){
            $userForm->setDistrictId($request["userInfo"]['districtId']);
        }
        if(isset($request["userInfo"]['dateOfBirth']) && $request["userInfo"]['dateOfBirth']!=null){
            $userForm->setDateOfBirth($request["userInfo"]['dateOfBirth']);
        }
        if(isset($request["userInfo"]['address']) && $request["userInfo"]['address']!=null){
            $userForm->setAddress($request["userInfo"]['address']);
        }
        $userInfo= $request["userInfo"];
        
        DB::beginTransaction();
        try {
            /*
             * lấy list user detail của user
             */
            $userDetailForm = new UserDetailForm();
            $userDetailForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            $userDetailForm->setUserId($user->id);
            $listUserDetail=$this->userDetailService->searchListData($userDetailForm);
            if(count($listUserDetail)>0){
                foreach ($listUserDetail as $key=>$value){
                    ${'userDetailForm'.$key} = new UserDetailForm();
                    ${'userDetailForm'.$key}->setId($value->id);
                    ${'userDetailForm'.$key}->setFirstName ($userForm->getFirstName());   
                    ${'userDetailForm'.$key}->setLastName ($userForm->getLastName()); 
                    ${'userDetailForm'.$key}->setGender ($userForm->getGender()); 
                    ${'userDetailForm'.$key}->setProvinceId ($userForm->getProvinceId()); 
                    ${'userDetailForm'.$key}->setDistrictId ($userForm->getDistrictId()); 
                    ${'userDetailForm'.$key}->setDateOfBirth (date("Y-m-d", $userForm->getDateOfBirth()));
                    ${'userDetailForm'.$key}->setAddress ($userForm->getAddress());
                    $this->userDetailService->updateUserDetail(${'userDetailForm'.$key});
                }
            }
            DB::commit();
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    /*
     * update last login
     */
    public function updateLastLogin($userId){
        $userForm = new UserForm();
        $userForm->setId($userId);
        $userForm->setLastLogin(date(env('DATE_FORMAT_Y_M_D'),time()));
        $this->userService->updateUser($userForm);
    }
    /*
     * get user info
     */
    public function getUserInfo($request){
        $language = $request['language'];
        $userId = $request['userId'];
        $merchantId = $request['merchantId'];
        $userDetailId = $request['userDetailId'];
        $response = array();
        $user = $this->userService->getDataById($userId);
        $userDetail = $this->userDetailService->getDataById($userDetailId);
        
        DB::beginTransaction();
        try {
            if($user->email!=null)
                $response['userInfo']['email'] = $user->email;
            else
                $response['userInfo']['email'] = "";
            if($user->mobile!=null)
                $response['userInfo']['mobile'] = $user->mobile;
            else
                $response['userInfo']['mobile'] = "";
            if($userDetail->gender!=null)
                $response['userInfo']['gender'] = $userDetail->gender;
            else
                $response['userInfo']['gender'] = "";
            if($userDetail->dateOfBirth!=null)
                $response['userInfo']['dateOfBirth'] = strtotime($userDetail->dateOfBirth);
            else
                $response['userInfo']['dateOfBirth'] = "";
            if($userDetail->provinceId!=null)
                $response['userInfo']['provinceId'] = $userDetail->provinceId;
            else
                $response['userInfo']['provinceId'] = "";
            if($userDetail->districtId!=null)
                $response['userInfo']['districtId'] = $userDetail->districtId;
            else
                $response['userInfo']['districtId'] = "";
            if($userDetail->address!=null)
                $response['userInfo']['address'] = $userDetail->address;
            else 
                $response['userInfo']['address'] ="";
            if($userDetail->firstName!=null)
                $response['userInfo']['firstName'] = $userDetail->firstName;
            else
                 $response['userInfo']['firstName'] ="";
            if($userDetail->lastName!=null)
                $response['userInfo']['lastName'] = $userDetail->lastName;
            else
                $response['userInfo']['lastName']="";
            DB::commit();
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
}

