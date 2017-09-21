<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Services\LibService;
use App\Http\Forms\TransactionForm;
use App\Http\Models\TransactionDAO;

class TransactionService extends BaseService {
    private $libService;
    private $transactionDao;


    public function __construct() {
        $this->libService = new LibService();
        $this->transactionDao = new TransactionDAO();
    }
    
    /*
     * get list Transaction
     */
    public function getListTransaction($request){
        $language = $request['language'];
        $merchantId = $request['merchantId'];
        $userId = $request['userId'];
        $userDetailId = $request['userDetailId'];
        $response = array();
        isset($request['pageIndex'])?$page = $request['pageIndex']:$page=1;
        isset($request['pageSize'])?$pageSize = $request['pageSize']:$pageSize=env('PAGE_SIZE');
        DB::beginTransaction();
        try {
            $transactionForm = new TransactionForm();
            $transactionForm->setUserDetailId($userId);
            $transactionForm->setMerchantId($merchantId);
            $transactionForm->setPageSize($pageSize);
            $transactionForm->setPageIndex($page);
            $listTransaction = $this->transactionDao->getList($transactionForm);
            $listTransactionResponse = array();
            if(count($listTransaction)>0){
                foreach ($listTransaction as $key=>$value){
                    $listTransactionResponse[$key]['hashcode'] = $value->hashcode;
                    $listTransactionResponse[$key]['amount'] = $value->amount;
                    $listTransactionResponse[$key]['createdTime'] = strtotime($value->createdTime);
                }
            }
            DB::commit();
            $response['listTransaction'] = $listTransactionResponse;
            $response['pageSize'] = $pageSize;
            $response['pageIndex'] = $page;
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
        
        
    }
    
}

