<?php

/* 
 * Api controller
 */
namespace App\Http\Api\Controllers;
use App\Http\Api\Controllers\BaseController;
use Illuminate\Http\Request;
use Session;
use Exception;
use App\Http\Api\Exceptions;
use App;
use App\Http\Services\LibService;
use App\Http\Api\Services\ApiUserService;
use App\Http\Api\Services\ApiMerchantService;
use App\Http\Api\Services\ApiUserDetailService;
use App\Http\Api\Services\ApiStoreService;
use App\Http\Api\Services\ApiStoreCategoryService;
use App\Http\Api\Services\ApiAddressService;
use App\Http\Api\Services\ApiPromotionService;
use App\Http\Api\Services\ApiNewsService;
use App\Http\Api\Services\ApiSurveyService;
class ApiController extends BaseController {
    private $libService;
    private $apiUserService;
    private $apiMerchantService;
    private $apiUserDetailService;
    private $apiStoreService;
    private $apiStoreCategoryService;
    private $apiAddressService;
    private $apiPromotionService;
    private $apiNewsService;
    private $apiSurveyService;
    public function __construct() {
        $this->libService = new LibService();
        $this->apiUserService = new ApiUserService();
        $this->apiMerchantService = new ApiMerchantService();
        $this->apiUserDetailService = new ApiUserDetailService();
        $this->apiStoreService = new ApiStoreService();
        $this->apiStoreCategoryService = new ApiStoreCategoryService();
        $this->apiAddressService = new ApiAddressService();
        $this->apiPromotionService = new ApiPromotionService();
        $this->apiNewsService = new ApiNewsService();
        $this->apiSurveyService = new ApiSurveyService();
    }
    
    public function getTest(){
        echo $this->setError('INVALID_PARAMETER');die();
        echo trans("common.login_admin_page");die();
    }

    public function postAuth(Request $data){
        $request = json_decode($data->getContent(),true);
        $language = isset($request['language'])?$request['language']:"vi";
        App::setLocale($language);
        $respone = array();
        try {
            if(!isset($request['secretKey']) || $request['secretKey'] != "VsecYupax@2017"){
                throw new Exception(trans('exception.SECRET_KEY_WRONG'),$this->libService->setError("SECRET_KEY_WRONG"));
            }
            if(!isset($request['serviceName']) || $request['serviceName'] == null){
                throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
            }
            if(!isset($request['token']) || $request['token'] == null){
                throw new Exception(trans('exception.TOKEN_NOT_FOUND'),$this->libService->setError("TOKEN_NOT_FOUND"));
            }
            $token = $request['token'];
            /*
             * Kiểm tra token
             * token tồn tại và hợp thời gian và user đã kích hoạt
             */
            $user = $this->apiUserService->checkTokenUser($token);
            if($user==null){
                throw new Exception(trans('exception.USER_NOT_FOUND'),$this->libService->setError("USER_NOT_FOUND"));
            }
            ($user->lastLogin!=null)?$lastLong = strtotime($user->lastLogin):$lastLong=0;
            if(time()-$lastLong>1800){
                throw new Exception(trans('exception.TOKEN_NOT_FOUND'),$this->libService->setError("TOKEN_NOT_FOUND"));
            }
            /*
             * nếu token phù hợp sẽ cập nhật lại thời gian login
             */
            $timeLogin = date(env('DATE_FORMAT_Y_M_D'), time());
            $this->apiUserService->updateTimeLogin($timeLogin,$user->id);
            $request['userId'] = $user->id;
            /*
             * --- end kiểm tra user
             */
            if(!isset($request['deviceId']) || $request['deviceId'] == null){
                throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
            }
            if(!isset($request['deviceType']) || $request['deviceType'] == null){
                throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
            }
            $serviceName = $request['serviceName'];
            
            switch ($serviceName){
                /*
                 * Danh sách các merchant 
                 */
                case "LIST_MERCHANT":
                    $respone = $this->apiMerchantService->listMerchant($request);
                    break;
                /*
                 * Đăng ký người dùng tại merchant
                 */
                case "CREATE_USER_DETAIL":
                    $respone = $this->apiUserService->createUserDetail($request);
                    break;
                default :
                    throw new Exception(trans('exception.SERVICE_NOT_FOUND'),$this->libService->setError("SERVICE_NOT_FOUND"));
            }
            
        } catch (Exception $ex) {
            $this->libService->logs_custom($ex->getMessage()." --File : ".$ex->getFile()."--Line : ".$ex->getLine());
            /*
             * xảy ra lỗi
             */
            $respone['error']= array(
                    'code'=>$ex->getCode(),
                    'message'=>$ex->getMessage()
                );
        }
        /*
         * thành công
         */
        if(!isset($respone['error'])){
            $respone['error']= array(
                'code'=>'200',
                'message'=>  trans('exception.SUCCESS')
            );
        }
        $respone['serviceName']=$request['serviceName'];
        return json_encode($respone);
    }
    
    public function postUnauth(Request $data){
        $request = json_decode($data->getContent(),true);
        $language = isset($request['language'])?$request['language']:"vi";
        App::setLocale($language);
        $respone = array();
        try {
            if(!isset($request['secretKey']) || $request['secretKey'] != "VsecYupax@2017"){
                throw new Exception(trans('exception.SECRET_KEY_WRONG'),$this->libService->setError("SECRET_KEY_WRONG"));
            }
            if(!isset($request['serviceName']) || $request['serviceName'] == null){
                throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
            }
            if(!isset($request['deviceId']) || $request['deviceId'] == null){
                throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
            }
            if(!isset($request['deviceType']) || $request['deviceType'] == null){
                throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
            }
            
            $serviceName = $request['serviceName'];
            switch ($serviceName){
                /*
                 * Đăng ký tài khoản
                 */
                case "CREATE_USER":
                    $respone = $this->apiUserService->createUser($request);
                    break;
                /*
                 * Kích hoạt tài khoản
                 */
                case "ACTIVE_USER":
                    $respone = $this->apiUserService->activeUser($request);
                    break;
                /*
                 * Đăng nhập
                 */
                case "LOGIN_USER":
                    $respone = $this->apiUserService->loginUser($request);
                    break;
                /*
                 * QUên mật khẩu
                 */
                case "FORGOT_PASSWORD":
                    $respone = $this->apiUserService->forgotPassword($request);
                    break;
                /*
                 * Gửi lại mã kích hoạt
                 */
                case "RESEND_ACTIVE_CODE":
                    $respone = $this->apiUserService->resendActiveCode($request);
                    break;
                default :
                    throw new Exception(trans('exception.SERVICE_NOT_FOUND'),$this->libService->setError("SERVICE_NOT_FOUND"));
            }
            
        } catch (Exception $ex) {
            $this->libService->logs_custom("\nController ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            /*
             * xảy ra lỗi
             */
            $respone['error']= array(
                    'code'=>$ex->getCode(),
                    'message'=>$ex->getMessage()
                );
        }
        /*
         * thành công
         */
        if(!isset($respone['error'])){
            $respone['error']= array(
                'code'=>'200',
                'message'=>  trans('exception.SUCCESS')
            );
        }
        $respone['serviceName']=$request['serviceName'];
        return json_encode($respone);
    }
    /*
     * post auth merrchant
     */
    public function postAuthMerchant(Request $data){
        $request = json_decode($data->getContent(),true);
        $language = isset($request['language'])?$request['language']:"vi";
        App::setLocale($language);
        $respone = array();
        try {
            if(!isset($request['secretKey']) || $request['secretKey'] != "VsecYupax@2017"){
                throw new Exception(trans('exception.SECRET_KEY_WRONG'),$this->libService->setError("SECRET_KEY_WRONG"));
            }
            if(!isset($request['serviceName']) || $request['serviceName'] == null){
                throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
            }
            if(!isset($request['token']) || $request['token'] == null){
                throw new Exception(trans('exception.TOKEN_NOT_FOUND'),$this->libService->setError("TOKEN_NOT_FOUND"));
            }
            $token = $request['token'];
            /*
             * Kiểm tra token
             * token tồn tại và hợp thời gian và user đã kích hoạt
             */
            $user = $this->apiUserService->checkTokenUser($token);
            if($user==null){
                throw new Exception(trans('exception.USER_NOT_FOUND'),$this->libService->setError("USER_NOT_FOUND"));
            }
            ($user->lastLogin!=null)?$lastLong = strtotime($user->lastLogin):$lastLong=0;
            if(time()-$lastLong>1800){
                throw new Exception(trans('exception.TOKEN_NOT_FOUND'),$this->libService->setError("TOKEN_NOT_FOUND"));
            }
            /*
             * nếu token phù hợp sẽ cập nhật lại thời gian login
             */
            $timeLogin = date(env('DATE_FORMAT_Y_M_D'), time());
            $this->apiUserService->updateTimeLogin($timeLogin,$user->id);
            $request['userId'] = $user->id;
            /*
             * --- end kiểm tra user
             */
            
            /*
             * kiểm tra mã merchant
             */
            if(!isset($request['merchantCode']) || $request['merchantCode'] == null){
                throw new Exception(trans('exception.INVALID_PARAMETER')."_merchantCode",$this->libService->setError("TOKEN_NOT_FOUND"));
            }
            $merchantCode = $request['merchantCode'];
            $merchant = $this->apiMerchantService->getDataByHashcode($merchantCode);
            if($merchant==null){
                throw new Exception(trans('exception.MERCHANT_NOT_FOUND'),$this->libService->setError("MERCHANT_NOT_FOUND"));
            }
            $request['merchantId'] = $merchant->id;
            /*
             * --- end kiểm tra merchant
             */
            /*
             * tìm kiếm user detail của merchant
             */
            $userDetail = $this->apiUserDetailService->getUserDetailByMerchant($user->id,$merchant->id);
            if($userDetail==null){
                throw new Exception(trans('exception.MERCHANT_HAVE_NOT_USER'),$this->libService->setError("MERCHANT_HAVE_NOT_USER"));
            }
            $request['userDetailId'] = $userDetail->id;
            /*
             * --- end tìm kiếm user detail của merchant
             */
            if(!isset($request['deviceId']) || $request['deviceId'] == null){
                throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
            }
            if(!isset($request['deviceType']) || $request['deviceType'] == null){
                throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
            }
            /*
             * cập nhật lại thời gian token đăng nhập
             */
            $this->apiUserService->updateLastLogin($user->id);
            
            $serviceName = $request['serviceName'];
            
            switch ($serviceName){
                /*
                 * Danh sách chi nhánh cửa hàng - home
                 */
                case "LIST_STORE_BRANCH":
                    $respone = $this->apiStoreService->listStore($request);
                    break;
                /*
                 * Danh sách chi tiết chi nhánh cửa hàng - home
                 */
                case "DETAIL_STORE_BRANCH":
                    $respone = $this->apiStoreService->detailStoreBranch($request);
                    break;
                /*
                 * Danh sách khuyến mại
                 */
                case "LIST_PROMOTION":
                    $respone = $this->apiPromotionService->listPromotion($request);
                    break;
                /*
                 * CHi tiết khuyến mại
                 */
                case "PROMOTION_DETAIL":
                    $respone = $this->apiPromotionService->detailPromotion($request);
                    break;
                /*
                 * Danh sách Tin tức
                 */
                case "LIST_NEWS":
                    $respone = $this->apiNewsService->listNews($request);
                    break;
                /*
                 * Chi tiết tin tức
                 */
                case "NEWS_DETAIL":
                    $respone = $this->apiNewsService->detailNews($request);
                    break;
                /*
                 * Thay đổi mật khẩu
                 */
                case "CHANGE_PASSWORD":
                    $respone = $this->apiUserService->changePassword($request);
                    break;
                /*
                 * Cập nhật thông tin tài khoản
                 */
                case "UPDATE_INFO_USER":
                    $respone = $this->apiUserService->updateInfoUser($request);
                    break;
                /*
                 * Cập nhật thông tin tài khoản
                 */
                case "GET_USER_INFO":
                    $respone = $this->apiUserService->getUserInfo($request);
                    break;
                /*
                 * Khảo sát
                 */
                case "SURVEY_LIST":
                    $respone = $this->apiSurveyService->listSurvey($request);
                    break;
                /*
                 * Trả lời khảo sát
                 */
                case "SURVEY_QUESTION":
                    $respone = $this->apiSurveyService->surveyQuestion($request);
                    break;
                default :
                    throw new Exception(trans('exception.SERVICE_NOT_FOUND'),$this->libService->setError("SERVICE_NOT_FOUND"));
            }
            
        } catch (Exception $ex) {
            $this->libService->logs_custom($ex->getMessage()." --File : ".$ex->getFile()."--Line : ".$ex->getLine());
            /*
             * xảy ra lỗi
             */
            $respone['error']= array(
                    'code'=>$ex->getCode(),
                    'message'=>$ex->getMessage()
                );
        }
        /*
         * thành công
         */
        if(!isset($respone['error'])){
            $respone['error']= array(
                'code'=>'200',
                'message'=>  trans('exception.SUCCESS')
            );
        }
        $respone['serviceName']=$request['serviceName'];
        return json_encode($respone);
    }
    
    public function postService(Request $data){
        $request = json_decode($data->getContent(),true);
        $language = isset($request['language'])?$request['language']:"vi";
        App::setLocale($language);
        $respone = array();
        try {
            if(!isset($request['secretKey']) || $request['secretKey'] != "VsecYupax@2017"){
                throw new Exception(trans('exception.SECRET_KEY_WRONG'),$this->libService->setError("SECRET_KEY_WRONG"));
            }
            if(!isset($request['serviceName']) || $request['serviceName'] == null){
                throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
            }
            if(!isset($request['deviceId']) || $request['deviceId'] == null){
                throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
            }
            if(!isset($request['deviceType']) || $request['deviceType'] == null){
                throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
            }
            
            $serviceName = $request['serviceName'];
            switch ($serviceName){
                /*
                 * danh sách danh mục cửa hàng
                 */
                case "LIST_CATEGORY":
                    $respone = $this->apiStoreCategoryService->getListStoreCategory($request);
                    break;
                /*
                 * Danh sách thành phố
                 */
                case "LIST_PROVINCE":
                    $respone = $this->apiAddressService->getListProvince($request);
                    break;
                /*
                 * Danh sách thành phố
                 */
                case "LIST_DISTRICT":
                    $respone = $this->apiAddressService->getListDistrict($request);
                    break;
                default :
                    throw new Exception(trans('exception.SERVICE_NOT_FOUND'),$this->libService->setError("SERVICE_NOT_FOUND"));
            }
            
        } catch (Exception $ex) {
            $this->libService->logs_custom("\nController ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            /*
             * xảy ra lỗi
             */
            $respone['error']= array(
                    'code'=>$ex->getCode(),
                    'message'=>$ex->getMessage()
                );
        }
        /*
         * thành công
         */
        if(!isset($respone['error'])){
            $respone['error']= array(
                'code'=>'200',
                'message'=>  trans('exception.SUCCESS')
            );
        }
        $respone['serviceName']=$request['serviceName'];
        return json_encode($respone);
    }
}

