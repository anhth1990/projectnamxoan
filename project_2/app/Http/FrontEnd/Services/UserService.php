<?php
namespace App\Http\FrontEnd\Services;
/* 
 * anhth1990 
 */
use Session;
use Exception;
use DB;
use App\Http\Services\BaseService;
use App\Http\Models\UserDAO;
use App\Http\Models\UserDetailDAO;
use App\Http\Models\ActiveCodeDAO;
use App\Http\Models\MerchantDAO;
use App\Http\Forms\ActiveCodeForm;
use App\Http\Forms\UserDetailForm;
class UserService extends BaseService {
    private $userDao;
    private $userDetailDao;
    private $activeCodeDao;
    private $merchantDao;
    public function __construct() {
        $this->userDao = new UserDAO();
        $this->userDetailDao = new UserDetailDAO();
        $this->activeCodeDao = new ActiveCodeDAO();
        $this->merchantDao = new MerchantDAO();
    }
    
    public function activeUserWeb($userHashcode,$merchantHashcode,$activeCode){
        $userObj = $this->userDao->findByHashcode($userHashcode, env('COMMON_STATUS_INACTIVE'));
        $merchantObj = $this->merchantDao->findByHashcode($merchantHashcode, env('COMMON_STATUS_ACTIVE'));
        if($userObj==null){
            throw new Exception(trans('error.user_not_found'));
        }
        if($merchantObj==null){
            throw new Exception(trans('error.merchant_not_found'));
        }
        // kiểm tra mã kích hoạt
        $activeCodeForm = new ActiveCodeForm();
        $activeCodeForm->setActiveCode($activeCode);
        $activeCodeForm->setType(env('TYPE_USER'));
        $activeCodeForm->setIdRef($userObj->id);
        $activeCodeForm->setStatus(env('COMMON_STATUS_ACTIVE'));
        $activeCodeObj = $this->activeCodeDao->checkActiveCode($activeCodeForm);
        if($activeCodeObj==null){
            throw new Exception(trans('error.code_active_not_found'));
        }
        /*
         * Kiểm tra user detail
         */
        $userDetailForm = new UserDetailForm();
        $userDetailForm->setUserId($userObj->id);
        $userDetailForm->setMerchantId($merchantObj->id);
        $userDetailForm->setStatus(env('COMMON_STATUS_INACTIVE'));
        $userDetailObj = $this->userDetailDao->checkUserDetail($userDetailForm);
        if($userDetailObj==null){
            throw new Exception(trans('error.user_detail_not_found'));
        }
        // set lại status
        $activeCodeObj->status = env('COMMON_STATUS_INACTIVE');
        $userObj->status = env('COMMON_STATUS_ACTIVE');
        $userDetailObj->status = env('COMMON_STATUS_ACTIVE');
        DB::beginTransaction();
        try {
            $this->activeCodeDao->saveResultId($activeCodeObj);
            $this->userDao->saveResultId($userObj);
            $this->userDetailDao->saveResultId($userDetailObj);
            DB::commit(); 
        } catch (Exception $ex) {
            DB::rollback(); 
            throw new Exception($ex->getMessage());
        }
    }
    
    
}

