<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Services\LibService;
use App\Http\Models\UserDAO;
use App\Http\Models\UserDetailDAO;
use App\Http\Forms\UserDetailForm;
use App\Http\Services\NotiEmailService;
use App\Http\Models\NotiEmailDAO;
use App\Http\Forms\NotiEmailForm;
use App\Http\Services\NotiMobileService;
use App\Http\Models\NotiMobileDAO;
use App\Http\Forms\NotiMobileForm;
use App\Http\Forms\ConfigRatingForm;
use App\Http\Models\ConfigRatingDAO;
use App\Http\Models\TransactionDAO;
use App\Http\Forms\TransactionForm;
use App\Http\Models\ConfigRatingGroupDAO;
use App\Http\Forms\ConfigRatingGroupForm;
use App\Http\Forms\UserInfoForm;
use App\Http\Models\RatingDAO;
use App\Http\Forms\RatingForm;
use App\Http\Models\ActiveCodeDAO;
use App\Http\Api\Services\ActiveCodeService;


class UserService extends BaseService {
    private $libService;
    private $userDao;
    private $userDetailDao;
    private $notiEmailService;
    private $configRatingDao;
    private $transactionDao;
    private $configRatingGroupDao;
    private $ratingDao;
    private $activeCodeService;
    private $activeCodeDao;
    private $notiMobileService;
    private $notiMobileDao;
    public function __construct() {
        $this->libService = new LibService();
        $this->userDao = new UserDAO();
        $this->userDetailDao = new UserDetailDAO();
        $this->notiEmailService = new NotiEmailService();
        $this->configRatingDao = new ConfigRatingDAO();
        $this->transactionDao = new TransactionDAO();
        $this->configRatingGroupDao = new ConfigRatingGroupDAO();
        $this->ratingDao = new RatingDAO();
        $this->activeCodeService = new ActiveCodeService();
        $this->activeCodeDao = new ActiveCodeDAO();
        $this->notiMobileService = new NotiMobileService();
        $this->notiMobileDao = new NotiMobileDAO();
    }
    
    /*
     * login user
     */
    public function loginUser($request){
        $language = $request['language'];
        $partnerId = $request['partnerId'];
        $merchantId = $request['merchantId'];
        $response = array();
        if(!isset($request["userInfo"])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $userInfo= $request["userInfo"];
        if(!isset($userInfo['username']) || is_null($userInfo['username'])){
            throw new Exception(trans('exception.USERNAME_REQUIRED'),$this->libService->setError("USERNAME_REQUIRED"));
        }
        if(!isset($userInfo['password']) || is_null($userInfo['password'])){
            throw new Exception(trans('exception.PASSWORD_REQUIRED'),$this->libService->setError("PASSWORD_REQUIRED"));
        }
        $username = $userInfo['username'];
        $password = md5($userInfo['password']);
        $user = $this->userDao->getLoginByUser($username, $password);
        if($user==null){
            throw new Exception(trans('exception.USERNAME_OR_PASSWORD_WRONG'),$this->libService->setError("USERNAME_OR_PASSWORD_WRONG"));
        }
        if($user->status == env('COMMON_STATUS_INACTIVE')){
            throw new Exception(trans('exception.USER_INACTIVE'),$this->libService->setError("USER_INACTIVE"));
        }
        /*
         * check login
         */
        $token = $this->getToken($user->hashcode);
        $user->token = $token;
        $user->lastLogin = date(env('DATE_FORMAT_Y_M_D'),time());
        /*
         * user detail theo merchant
         */
        $userDetailForm = new UserDetailForm();
        $userDetailForm->setMerchantId($merchantId);
        $userDetailForm->setUserId($user->id);
        $userDetailForm->setStatus(env('COMMON_STATUS_ACTIVE'));
        $userDetail = $this->userDetailDao->checkUserDetail($userDetailForm);
        /*
         * user rating
         */
        //$userInfoForm = new UserInfoForm();
        //$userInfoForm = $this->getRatingUser($user->id,$merchantId,$partnerId);
        //$response['userInfo']['rating'] = trans('config.rating_'.$userInfoForm->getRatingName());
        //$response['userInfo']['ratingCode'] = $userInfoForm->getRatingName();
        //$response['userInfo']['groupRating'] = $userInfoForm->getRatingGroupName();
        //$response['userInfo']['recensyName'] = $userInfoForm->getRencensyName();
        //$response['userInfo']['frequencyName'] = $userInfoForm->getFrequencyName();
        //$response['userInfo']['monetaryName'] = $userInfoForm->getMonetaryName();
        DB::beginTransaction();
        try {
            $this->userDao->saveResultId($user);
            /*
             * set response user info
             */
            $response['userInfo']['token'] = $token;
            $response['userInfo']['username'] = $user->username;
            if($userDetail!=null){
                $response['userInfo']['firstName']=$userDetail->firstName;
                $response['userInfo']['lastName']=$userDetail->lastName;
                $response['userInfo']['email']=$userDetail->email;
                $response['userInfo']['mobile']=$userDetail->mobile;
                $response['userInfo']['status']=$user->status;
            }
            DB::commit();
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
        
        
    }
    
    /*
     * resend password
     */
    public function resendPassword($request){
        $language = $request['language'];
        $partnerId = $request['partnerId'];
        $merchantId = $request['merchantId'];
        $response = array();
        if(!isset($request["userInfo"])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $userInfo= $request["userInfo"];
        if(!isset($userInfo['username']) || is_null($userInfo['username'])){
            throw new Exception(trans('exception.USERNAME_REQUIRED'),$this->libService->setError("USERNAME_REQUIRED"));
        }
        $username = $userInfo['username'];
        $userDetailDao = $this->userDetailDao->getUserDetailByUsername($username, $merchantId);
        if($userDetailDao==null){
            throw new Exception(trans('exception.USER_NOT_FOUND'),$this->libService->setError("USER_NOT_FOUND"));
        }
        if($userDetailDao->status == env('COMMON_STATUS_INACTIVE')){
            throw new Exception(trans('exception.USER_INACTIVE'),$this->libService->setError("USER_INACTIVE"));
        }
        
        try {
            $user = $this->userDao->findById($userDetailDao->userId);
            $password= $this->libService->getPasswordRandom();
            $user->password = md5($password);
            $this->userDao->saveResultId($user);
            $email = isset($userDetailDao->email)?$userDetailDao->email:"";
            $mobile = isset($userDetailDao->mobile)?$userDetailDao->mobile:"";
            // noti email
            DB::beginTransaction();
            if($email!=""){
                $notiEmailForm = new NotiEmailForm();
                $notiEmailForm->setType(env('NOTI_TYPE_RESEND_PASSWORD'));
                // set content
                $content = array(
                    'templateName'=>'resendPassword',
                    'lang'=>'vi',
                    'title'=>trans('noti.resend_password'),
                    'sendTo'=>$email,
                    'username'=>$username,
                    'password'=>$password
                );
                $notiEmailForm->setContent(json_encode($content));
                $notiEmailForm->setMerchantId($merchantId);
                $notiEmailForm->setStatus(env('COMMON_STATUS_PENDING'));
                $notiEmailDao=$this->notiEmailService->insert($notiEmailForm);
                // send mail 
                $this->notiEmailService->sendMail($notiEmailDao->hashcode);
                $response['userInfo']['email'] = $this->libService->hideInfo($email);
            }
            DB::commit();
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    /*
     * get User by username
     */
    public function checkToken($token){
        try {
            $user = $this->userDao->getUserByToken($token);
            if($user==null){
                throw new Exception(trans('exception.TONKEN_NOT_FOUND'),$this->libService->setError("TONKEN_NOT_FOUND"));
            }
            ($user->lastLogin!=null)?$lastLong = strtotime($user->lastLogin):$lastLong=0;
            if(time()-$lastLong>1800){
                throw new Exception(trans('exception.TOKEN_NOT_FOUND'),$this->libService->setError("TOKEN_NOT_FOUND"));
            }
            /*
             * nếu token phù hợp sẽ cập nhật lại token
             */
            $user->lastLogin = date(env('DATE_FORMAT_Y_M_D'), time());
            $this->userDao->saveResultId($user);
            return $user;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(),$ex->getCode());
        }
    }
    
    public function getUserDetailByUsername($username,$merchantId,$partnerId){
        return $this->userDetailDao->getUserDetailByUsername($username, $merchantId, $partnerId);
    }
    
    /*
     * get rating user
     */
    public function getRatingUser($userId,$merchantId,$partnerId){
        $userInfoForm = new UserInfoForm();
        try {
            /*
            * list recensy
            */
           $recensyForm = new ConfigRatingForm();
           $recensyForm->setType(env('RATING_TYPE_RECENSY'));
           $recensyForm->setStatus(env('COMMON_STATUS_ACTIVE'));
           $recensyForm->setPageSize(null);
           $recensyForm->setPartnerId($partnerId);
           $recensyForm->setMerchantId($merchantId);
           $listRecensy = $this->configRatingDao->getList($recensyForm);
           /*
            * list frequency
            */
           $frequencyForm = new ConfigRatingForm();
           $frequencyForm->setType(env('RATING_TYPE_FREQUENCY'));
           $frequencyForm->setStatus(env('COMMON_STATUS_ACTIVE'));
           $frequencyForm->setPageSize(null);
           $frequencyForm->setPartnerId($partnerId);
           $frequencyForm->setMerchantId($merchantId);
           $listFrequency = $this->configRatingDao->getList($frequencyForm);
           /*
            * list monetary value
            */
           $monetaryForm = new ConfigRatingForm();
           $monetaryForm->setType(env('RATING_TYPE_MONETARY_VALUE'));
           $monetaryForm->setStatus(env('COMMON_STATUS_ACTIVE'));
           $monetaryForm->setPageSize(null);
           $monetaryForm->setPartnerId($partnerId);
           $monetaryForm->setMerchantId($merchantId);
           $listMonetary = $this->configRatingDao->getList($monetaryForm);
           /*
            * list transaction user
            */
            $transactionForm = new TransactionForm();
            $transactionForm->setType(env('TYPE_TRANS_IMPORT_CSV'));
            $transactionForm->setPageSize(null);
            $transactionForm->setPartnerId($partnerId);
            $transactionForm->setMerchantId($merchantId);
            $transactionForm->setUserDetailId($userId);
            $listTransUser = $this->transactionDao->getList($transactionForm);
            /*
             * check R - F - M
             */
            $R = 0;
            $F = 0;
            $M = 0;
            $RDao = $this->getRecensyUser($listTransUser, $listRecensy);
            if($RDao!=null){
               $R = $RDao->id; 
               $userInfoForm->setRencensyName($RDao->name);
            }
            $FDao = $this->getFrequencyUser($listTransUser, $listFrequency);
            if($FDao!=null){
               $F = $FDao->id; 
               $userInfoForm->setFrequencyName($FDao->name);
            }
            $MDao = $this->getMonetaryUser($listTransUser, $listMonetary);
            if($MDao!=null){
               $M = $MDao->id; 
               $userInfoForm->setMonetaryName($MDao->name);
            }
            
            /*
             * Tìm group rating hợp lệ
             */
            $configRatingGroupForm = new ConfigRatingGroupForm();
            $configRatingGroupForm->setPartnerId($partnerId);
            $configRatingGroupForm->setMerchantId($merchantId);
            $configRatingGroupForm->setRecensyId($R);
            $configRatingGroupForm->setFrequencyId($F);
            $configRatingGroupForm->setMonetaryId($M);
            $configRatingGroupForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            $configRatingGroupDao = $this->configRatingGroupDao->getConfigRatingGroup($configRatingGroupForm);
            if($configRatingGroupDao==null){
                /*
                 * không có rating group
                 */
                $userInfoForm->setRatingName(env('RATING_NULL'));
                $userInfoForm->setRatingGroupName('');
            }else{
                $userInfoForm->setRatingGroupName($configRatingGroupDao->name);
                $ratingForm = new RatingForm();
                $ratingForm->setPartnerId($partnerId);
                $ratingForm->setMerchantId($merchantId);
                $ratingForm->setRatingGroupId($configRatingGroupDao->id);
                $ratingForm->setStatus(env('COMMON_STATUS_ACTIVE'));
                $ratingDao= $this->ratingDao->getRatingByRatingGroupId($ratingForm);
                if($ratingDao==null){
                    $userInfoForm->setRatingName(env('RATING_NULL'));
                }else{
                    $userInfoForm->setRatingName($ratingDao->code);
                }
                $userInfoForm->setRatingGroupName($configRatingGroupDao->name);
            }
            return $userInfoForm;
            
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(),$ex->getCode());
        }
    }
    
    
    /*
     * phân tích Recency của user
     */
    public function getRecensyUser($listTransUser,$listRecensy){
        /*
         * user chưa có giao dịch
         * hoặc merchant chưa cấu hình recensy thì 
         * return giá trị 0
         */
        if(count($listRecensy)<=0 || count($listTransUser)<= 0){
            return null;
        }else{
            /*
             * user có giao dịch và merchant có cấu hình reecensy
             * lấy ra ngày hiện tại cách ngày giao dịch gần nhất là bao lâu
             * vòng lặp recenensy lấy ra giá trị R phù hợp
             */
            $dateNow = date('Y/m/d');
            $secondsNow = strtotime($dateNow);
            $timestamp= strtotime($listTransUser[0]->createdTime);
            $dateTrans = date('Y/m/d', $timestamp);
            $secondsTrans = strtotime($dateTrans);
            $diffSecond = $secondsNow-$secondsTrans;
            $date = $diffSecond/86400;
            /*
             * tạo mảng phù hợp
             */
            $listRecensyPerfect = array();
            $i=0;
            foreach ($listRecensy as $key=>$value){
                if($value->minValue <= $date && $value->maxValue >= $date){
                    $listRecensyPerfect[$i] = $value;
                    $i++;
                }
            }
            /*
             * tìm được mảng hợp lệ
             * chọn giá trị R tốt nhất
             */
            if(count($listRecensyPerfect)<=0){
                return null;
            }else{
                /*
                 * tìm giá trị ngày gần nhất hợp lý nhất
                 * ví dụ 0-3 2-7 thì chọn giá trị hợp lệ 0-3
                 */
                $min = $listRecensyPerfect[0]->valueMax;
                $rUser = $listRecensyPerfect[0];
                foreach ($listRecensyPerfect as $key=>$value){
                    if($value->maxValue<$min){
                        $min = $value->maxValue;
                        $rUser = $value;
                    }
                }
                return $rUser;
            }
        }
        
    }
    
    // phân tích frequency của user
    public function getFrequencyUser($listTransUser,$listFrequency){
        /*
         * user chưa có giao dịch
         * hoặc merchant chưa cấu hình frequency thì 
         * return giá trị 0
         */
        if(count($listFrequency)<=0 || count($listTransUser)<= 0){
            return null;
        }else{
            /*
             * user có giao dịch và merchant có cấu hình frequency
             * lấy ra chu kỳ của từng frequency
             * lấy tất cả danh sách giao dịch trong chu kỳ từ thời gian hiện tại - chu kỳ
             */
            $dateNow = date('Y/m/d');
            $secondsNow = strtotime($dateNow);
            /*
             * tạo mảng phù hợp
             */
            $listFrequencyPerfect = array();
            $i=0;
            foreach ($listFrequency as $key=>$value){
                /*
                 * tim date cách chu kỳ
                 * để lấy làm mốc
                 */
                $periodic = $value->periodic;
                $date = $secondsNow-($periodic*86400);
                /*
                 * lấy tất cả giao dịch trong chu kỳ
                 */
                $countTrans = 0;
                foreach ($listTransUser as $keyTrans=>$valueTrans){
                    if(strtotime($valueTrans->createdTime)>=$date && strtotime($valueTrans->createdTime)<=$secondsNow){
                        $countTrans++;
                    }
                }
                /*
                 * kiểm tra tính phù hợp với frequency 
                 */
                if($value->minValue <=$countTrans && $countTrans<= $value->maxValue){
                    $listFrequencyPerfect[$i] = $value;
                    $i++;
                }
            }
            /*
             * Lấy ra giá trị frequency hợp lệ nhất
             */
            if(count($listFrequencyPerfect)<=0){
                return null;
            }else{
                $max = ($listFrequencyPerfect[0]->maxValue-$listFrequencyPerfect[0]->minValue)/$listFrequencyPerfect[0]->periodic;
                $fUser = $listFrequencyPerfect[0];
                foreach ($listFrequencyPerfect as $key=>$value){
                    $average = ($value->maxValue-$value->minValue)/$value->periodic;
                    if($average>$max){
                        $max = $average;
                        $fUser = $value;
                    }
                }
                return $fUser;
            }
        }
    }
    
    // phân tích monetary value của user
    public function getMonetaryUser($listTransUser,$listMonetary){
        /*
         * user chưa có giao dịch
         * hoặc merchant chưa cấu hình frequency thì 
         * return giá trị 0
         */
        if(count($listMonetary)<=0 || count($listTransUser)<= 0){
            return null;
        }else{
            /*
             * user có giao dịch và merchant có cấu hình frequency
             * lấy ra chu kỳ của từng frequency
             * lấy tất cả danh sách giao dịch trong chu kỳ từ thời gian hiện tại - chu kỳ
             */
            $dateNow = date('Y/m/d');
            $secondsNow = strtotime($dateNow);
            /*
             * tạo mảng phù hợp
             */
            $listMonetaryPerfect = array();
            $i=0;
            foreach ($listMonetary as $key=>$value){
                /*
                 * tim date cách chu kỳ
                 * để lấy làm mốc
                 */
                $periodic = $value->periodic;
                $date = $secondsNow-($periodic*86400);
                /*
                 * lấy tất cả giao dịch trong chu kỳ
                 */
                $totalMoney = 0;
                foreach ($listTransUser as $keyTrans=>$valueTrans){
                    if(strtotime($valueTrans->createdTime)>=$date && strtotime($valueTrans->createdTime)<=$secondsNow){
                        $totalMoney+= $valueTrans->amount;
                    }
                }
                /*
                 * kiểm tra tính phù hợp với monetary 
                 */
                if($value->minValue <=$totalMoney && $totalMoney<= $value->maxValue){
                    $listMonetaryPerfect[$i] = $value;
                    $i++;
                }
            }
            /*
             * Lấy ra giá trị frequency hợp lệ nhất
             */
            if(count($listMonetaryPerfect)<=0){
                return null;
            }else{
                $max = ($listMonetaryPerfect[0]->maxValue-$listMonetaryPerfect[0]->minValue)/$listMonetaryPerfect[0]->periodic;
                $mUser = $listMonetaryPerfect[0];
                foreach ($listMonetaryPerfect as $key=>$value){
                    $average = ($value->maxValue-$value->minValue)/$value->periodic;
                    if($average>$max){
                        $max = $average;
                        $mUser = $value;
                    }
                }
                return $mUser;
            }
        }
    }
    
    /*
     * resend active code
     */
    public function resendActiveCode($request){
        $language = $request['language'];
        $partnerId = $request['partnerId'];
        $merchantId = $request['merchantId'];
        $response = array();
        if(!isset($request["userInfo"])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $userInfo= $request["userInfo"];
        if(!isset($userInfo['username']) || is_null($userInfo['username'])){
            throw new Exception(trans('exception.USERNAME_REQUIRED'),$this->libService->setError("USERNAME_REQUIRED"));
        }
        $username = $userInfo['username'];
        $userDetailDao = $this->userDetailDao->getUserDetailByUsername($username, $merchantId , $partnerId);
        if($userDetailDao==null){
            throw new Exception(trans('exception.USER_NOT_FOUND'),$this->libService->setError("USER_NOT_FOUND"));
        }
        
        try {
            $user = $this->userDao->findById($userDetailDao->userId);
            $activeCode = $this->libService->getActiveCode();
            DB::beginTransaction();
            $activeCodeObj = $this->activeCodeService->getActiveCodeByIdRef(env('TYPE_USER'), $user->id);
            if($activeCodeObj==null){
                /*
                 * Nếu chưa có bản ghi sẽ thêm bản ghi cho active code cho user
                 */
                $activeCodeObj->type = env('TYPE_USER');
                $activeCodeObj->idRef = $user->id;
                $activeCodeObj->activeCode = $activeCode;
                $activeCodeObj->status = env('COMMON_STATUS_ACTIVE');
                $activeCodeObj->createdDate = date(env('DATE_FORMAT_Y_M_D'),time());
            }else{
                /*
                 * Đã có bản ghi mới lưu code mới
                 */
                $activeCodeObj->activeCode = $activeCode;
                $activeCodeObj->status = env('COMMON_STATUS_ACTIVE');
                $activeCodeObj->createdDate = date(env('DATE_FORMAT_Y_M_D'),time());
            }
            $this->activeCodeDao->saveResultId($activeCodeObj);
           
            ($userDetailDao->email!=null)?$email=$userDetailDao->email:$email="";
            ($userDetailDao->mobile!=null)?$mobile=$userDetailDao->mobile:$mobile="";
             
            // noti email
            if($email!=""){
                $notiEmailForm = new NotiEmailForm();
                $notiEmailForm->setType(env('NOTI_TYPE_RESEND_ACTIVE_CODE'));
                // set content
                $content = array(
                    'templateName'=>'resendActiveCode',
                    'lang'=>'vi',
                    'title'=>trans('noti.resend_active_code'),
                    'sendTo'=>$email,
                    'username'=>$username,
                    'activeCode'=>$activeCode
                );
                $notiEmailForm->setContent(json_encode($content));
                $notiEmailForm->setMerchantId($merchantId);
                $notiEmailForm->setStatus(env('COMMON_STATUS_PENDING'));
                $notiEmailDao=$this->notiEmailService->insert($notiEmailForm);
                
                // send mail 
                $this->notiEmailService->sendMail($notiEmailDao->hashcode);
                $response['userInfo']['email'] = $this->libService->hideInfo($email);
            }
            // noti mobile
            if($mobile!=""){
                $notiMobileForm = new NotiMobileForm();
                $notiMobileForm->setType(env('NOTI_TYPE_RESEND_ACTIVE_CODE'));
                // set content
                $content = array(
                    'templateName'=>'resendActiveCode',
                    'lang'=>'vi',
                    'title'=>trans('noti.resend_active_code'),
                    'sendTo'=>$mobile,
                    'username'=>$username,
                    'activeCode'=>$activeCode
                );
                $notiMobileForm->setContent(json_encode($content));
                $notiMobileForm->setMerchantId($merchantId);
                $notiMobileForm->setStatus(env('COMMON_STATUS_PENDING'));
                $notiMobileDao=$this->notiMobileService->insert($notiMobileForm);
                $response['userInfo']['mobile'] = $this->libService->hideInfo($mobile);
            }
            DB::commit();
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom($ex->getMessage()."-".$ex->getFile()."-".$ex->getLine());
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    /*
     * active code
     */
    public function activeCode($request){
        $language = $request['language'];
        $partnerId = $request['partnerId'];
        $merchantId = $request['merchantId'];
        $response = array();
        if(!isset($request['activeCode']) || is_null($request['activeCode'])){
            throw new Exception(trans('exception.ACTIVE_CODE_REQUIRED'),$this->libService->setError("ACTIVE_CODE_REQUIRED"));
        }
        $activeCode = $request['activeCode'];
        if(!isset($request["userInfo"])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $userInfo= $request["userInfo"];
        if(!isset($userInfo['username']) || is_null($userInfo['username'])){
            throw new Exception(trans('exception.USERNAME_REQUIRED'),$this->libService->setError("USERNAME_REQUIRED"));
        }
        
        $username = $userInfo['username'];
        $userDetailDao = $this->userDetailDao->getUserDetailByUsername($username, $merchantId , $partnerId);
        if($userDetailDao==null){
            throw new Exception(trans('exception.USER_NOT_FOUND'),$this->libService->setError("USER_NOT_FOUND"));
        }
        $userDetailObj = $this->userDetailDao->findById($userDetailDao->id);
        if($userDetailObj==null){
            throw new Exception(trans('exception.USER_NOT_FOUND'),$this->libService->setError("USER_NOT_FOUND"));
        }
        $user = $this->userDao->findById($userDetailDao->userId);
        $activeCodeObj = $this->activeCodeService->getActiveCodeByCode(env('TYPE_USER'), $user->id, $activeCode);
        if($activeCodeObj==null){
            throw new Exception(trans('exception.ACTIVE_CODE_NOT_FOUND'),$this->libService->setError("ACTIVE_CODE_NOT_FOUND"));
        }
        if($activeCodeObj->status !=  env('COMMON_STATUS_ACTIVE')){
            throw new Exception(trans('exception.ACTIVE_CODE_NOT_FOUND'),$this->libService->setError("ACTIVE_CODE_NOT_FOUND"));
        }
        try {
            DB::beginTransaction();
                /*
                 * active user - active user detail - inactive code
                 */
                $token = $this->getToken($user->hashcode);
                $user->status = env('COMMON_STATUS_ACTIVE');
                $user->token = $token;
                $user->lastLogin = date(env('DATE_FORMAT_Y_M_D'),time());
                $this->userDao->saveResultId($user);
                $userDetailObj->status = env('COMMON_STATUS_ACTIVE');
                $this->userDetailDao->saveResultId($userDetailObj);
                $activeCodeObj->status = env('COMMON_STATUS_INACTIVE');
                $this->activeCodeDao->saveResultId($activeCodeObj);
                 /*
                  * login user
                  */
                //$userInfoForm = new UserInfoForm();
                //$userInfoForm = $this->getRatingUser($user->id,$merchantId,$partnerId);
                //$response['userInfo']['rating'] = trans('config.rating_'.$userInfoForm->getRatingName());
                //$response['userInfo']['ratingCode'] = $userInfoForm->getRatingName();
                //$response['userInfo']['groupRating'] = $userInfoForm->getRatingGroupName();
                //$response['userInfo']['recensyName'] = $userInfoForm->getRencensyName();
                //$response['userInfo']['frequencyName'] = $userInfoForm->getFrequencyName();
                //$response['userInfo']['monetaryName'] = $userInfoForm->getMonetaryName();
                $response['userInfo']['token'] = $token;
                $response['userInfo']['username'] = $user->username;
                if($userDetailObj!=null){
                    $response['userInfo']['firstName']=$userDetailObj->firstName;
                    $response['userInfo']['lastName']=$userDetailObj->lastName;
                    $response['userInfo']['email']=$userDetailObj->email;
                    $response['userInfo']['mobile']=$userDetailObj->mobile;
                    $response['userInfo']['status']=$userDetailObj->status;
                }
            DB::commit();
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom($ex->getMessage()."-".$ex->getFile()."-".$ex->getLine());
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    /*
     * thay đổi tên đăng nhập
     */
    public function changeUsername($request){
        $language = $request['language'];
        $partnerId = $request['partnerId'];
        $merchantId = $request['merchantId'];
        $userId = $request['userId'];
        $userDetailId = $request['userDetailId'];
        $response = array();
        $userInfo= $request["userInfo"];
        /*
         * xác nhận mật khẩu
         */
        if(!isset($userInfo['password']) || is_null($userInfo['password'])){
            throw new Exception(trans('exception.PASSWORD_REQUIRED'),$this->libService->setError("PASSWORD_REQUIRED"));
        }
        $password = $userInfo['password'];
        $user = $this->userDao->findById($userId);
        if($user->password != md5($password)){
            throw new Exception(trans('exception.CONFIRM_PASSWORD_WRONG'),$this->libService->setError("CONFIRM_PASSWORD_WRONG"));
        }
        // end xác nhận mật khẩu
        
        if(!isset($userInfo['usernameNew']) || is_null($userInfo['usernameNew'])){
            throw new Exception(trans('exception.USERNAME_NEW_REQUIRED'),$this->libService->setError("USERNAME_NEW_REQUIRED"));
        }
        $usernameNew = $userInfo['usernameNew'];
        if(!preg_match($this->PATTERN_USERNAME, $usernameNew)){
            throw new Exception(trans('exception.USERNAME_PATTERN'),$this->libService->setError("USERNAME_PATTERN"));
        }
        /*
         * check tồn tại user name
         */
        $checkUsername = $this->userDao->checkExistUsername($usernameNew);
        if(count($checkUsername)>0){
            throw new Exception(trans('exception.USERNAME_NEW_EXIST'),$this->libService->setError("USERNAME_NEW_EXIST"));
        }
        try {
            $user->username = $usernameNew;
            DB::beginTransaction();
            $this->userDao->saveResultId($user);
            DB::commit();
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom($ex->getMessage()."-".$ex->getFile()."-".$ex->getLine());
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    /*
     * thay đổi mật khẩu
     */
    public function changePassword($request){
        $language = $request['language'];
        $partnerId = $request['partnerId'];
        $merchantId = $request['merchantId'];
        $userId = $request['userId'];
        $userDetailId = $request['userDetailId'];
        $response = array();
        $userInfo= $request["userInfo"];
        /*
         * xác nhận mật khẩu
         */
        if(!isset($userInfo['password']) || is_null($userInfo['password'])){
            throw new Exception(trans('exception.PASSWORD_REQUIRED'),$this->libService->setError("PASSWORD_REQUIRED"));
        }
        $password = $userInfo['password'];
        $user = $this->userDao->findById($userId);
        if($user->password != md5($password)){
            throw new Exception(trans('exception.CONFIRM_PASSWORD_WRONG'),$this->libService->setError("CONFIRM_PASSWORD_WRONG"));
        }
        // end xác nhận mật khẩu
        /*
         * New password
         */
        if(!isset($userInfo['passwordNew']) || is_null($userInfo['passwordNew'])){
            throw new Exception(trans('exception.PASSWORD_NEW_REQUIRED'),$this->libService->setError("PASSWORD_NEW_REQUIRED"));
        }
        $passwordNew = $userInfo['passwordNew'];
        if(!preg_match($this->PATTERN_PASSWORD, $passwordNew)){
            throw new Exception(trans('exception.PASSWORD_PATTERN'),$this->libService->setError("PASSWORD_PATTERN"));
        }
        if(!isset($userInfo['passwordNewConfirm']) || is_null($userInfo['passwordNewConfirm'])){
            throw new Exception(trans('exception.PASSWORD_NEW_CONFIRM_REQUIRED'),$this->libService->setError("PASSWORD_NEW_CONFIRM_REQUIRED"));
        }
        $passwordNewConfirm = $userInfo['passwordNewConfirm'];
        
        if($passwordNew==$password){
            throw new Exception(trans('exception.PASSWORD_NEW_SHOULD_DIFFERENT_PASSWORD_NOW'),$this->libService->setError("PASSWORD_NEW_SHOULD_DIFFERENT_PASSWORD_NOW"));
        }
        if($passwordNew!=$passwordNewConfirm){
            throw new Exception(trans('exception.PASSWORD_NEW_CONFIRM_WRONG'),$this->libService->setError("PASSWORD_NEW_CONFIRM_WRONG"));
        }   
        
        try {
            $user->password = md5($passwordNew);
            DB::beginTransaction();
            $this->userDao->saveResultId($user);
            DB::commit();
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom($ex->getMessage()."-".$ex->getFile()."-".$ex->getLine());
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    /*
     * User register
     */
    public function userRegister($request){
        $language = $request['language'];
        $merchantId = $request['merchantId'];
        $response = array();
        if(!isset($request["userInfo"])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $userInfo= $request["userInfo"];
        if(!isset($userInfo['username']) || is_null($userInfo['username'])){
            throw new Exception(trans('exception.USERNAME_REQUIRED'),$this->libService->setError("USERNAME_REQUIRED"));
        }
        if($this->userDao->checkExistUsername($userInfo['username'])!=null){
            throw new Exception(trans('exception.USERNAME_EXIST'),$this->libService->setError("USERNAME_EXIST"));
        }
        if(!isset($userInfo['password']) || is_null($userInfo['password'])){
            throw new Exception(trans('exception.PASSWORD_REQUIRED'),$this->libService->setError("PASSWORD_REQUIRED"));
        }
        if(!isset($userInfo['passwordConfirm']) || is_null($userInfo['passwordConfirm'])){
            throw new Exception(trans('exception.PASSWORD_CONFIRM_REQUIRED'),$this->libService->setError("PASSWORD_CONFIRM_REQUIRED"));
        }
        if($userInfo['passwordConfirm']!=$userInfo['password']){
            throw new Exception(trans('exception.PASSWORD_CONFIRM_WRONG'),$this->libService->setError("PASSWORD_CONFIRM_WRONG"));
        }
        if($userInfo['passwordConfirm']!=$userInfo['password']){
            throw new Exception(trans('exception.PASSWORD_CONFIRM_WRONG'),$this->libService->setError("PASSWORD_CONFIRM_WRONG"));
        }
        if(!isset($userInfo['contact']) || is_null($userInfo['contact'])){
            throw new Exception(trans('exception.EMAIL_OR_MOBILE_REQUIRED'),$this->libService->setError("EMAIL_OR_MOBILE_REQUIRED"));
        }
        $this->checkExistContact($userInfo['contact'], $merchantId);
        
    }
    
    public function checkExistContact($contact,$merchantId){
        
        $email = (preg_match($this->PATTERN_FORMAT_EMAIL, $contact))?$contact:"";
        $mobile = (preg_match($this->PATTERN_IS_MOBILE, $contact))?$contact:"";
        if($email==""&&$mobile==""){
            
        }
        echo $email .'----'.$mobile;
    }
}

