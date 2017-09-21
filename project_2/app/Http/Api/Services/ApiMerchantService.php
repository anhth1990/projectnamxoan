<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Services\LibService;
use App\Http\Forms\MerchantForm;
use App\Http\Services\MerchantService;
class ApiMerchantService extends BaseService {
    private $libService;
    private $merchantService;
    public function __construct() {
        $this->libService = new LibService();
        $this->merchantService = new MerchantService();
    }
    
    /*
     * list merchant
     */
    public function listMerchant($request){
        $userId = $request['userId'];
        $response = array();
        isset($request['pageIndex'])?$page = $request['pageIndex']:$page=1;
        isset($request['pageSize'])?$pageSize = $request['pageSize']:$pageSize=env('PAGE_SIZE');
        DB::beginTransaction();
        try {
            $merchantForm = new MerchantForm();
            $merchantForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            $merchantForm->setPageSize($pageSize);
            $merchantForm->setPageIndex($page);
            $listMerchant = $this->merchantService->searchListData($merchantForm);
            $listMerchantResponse = array();
            if(count($listMerchant)>0){
                foreach ($listMerchant as $key=>$value){
                    $listMerchantResponse[$key]['hashcode'] = $value->hashcode;
                    $listMerchantResponse[$key]['name'] = $value->name;
                }
            }
            DB::commit();
            $response['listMerchant'] = $listMerchantResponse;
            $response['pageSize'] = $pageSize;
            $response['pageIndex'] = $page;
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    /*
     * get data by hashcode
     */
    public function getDataByHashcode($hashcode){
        return $this->merchantService->getMerchantByHashcode($hashcode);
    }
    
    
}

