<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Services\LibService;
use App\Http\Forms\UserDetailForm;
use App\Http\Services\UserDetailService;
class ApiUserDetailService extends BaseService {
    private $libService;
    private $userDetailService;
    public function __construct() {
        $this->libService = new LibService();
        $this->userDetailService = new UserDetailService();
    }
    
    public function getUserDetailByMerchant($userId,$merchantId){
        $userDetailForm = new UserDetailForm();
        $userDetailForm->setUserId($userId);
        $userDetailForm->setMerchantId($merchantId);
        $userDetailForm->setStatus(env('COMMON_STATUS_ACTIVE'));
        return $this->userDetailService->searchFirstData($userDetailForm);
    }
    
    
}

