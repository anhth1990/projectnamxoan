<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\TransactionForm;
use App\Http\Models\TransactionDAO;
use Session;
use Exception;
use DB;
class TransactionService extends BaseService {
    private $transactionDao;
    public function __construct() {
        $this->transactionDao = new TransactionDAO();
    }
    
    public function createTransaction(TransactionForm $transactionForm){
        $idRef = $transactionForm->getIdRef();
        $transactionDao = new TransactionDAO();
        $transactionDao = $this->transactionDao->getTransactionByIdRef($idRef);
        if($transactionDao==null){
            /*
             * Mã tham chiếu không tồn tại
             * có thể tiến hành thêm
             */
            $transactionForm->setCreatedTime(date(env('DATE_FORMAT_Y_M_D'), $transactionForm->getCreatedTime()));
            $transactionDao = $this->insertTransaction($transactionForm);
        }else{
            /*
             * mã tham chiếu đã tồn tại
             */
            throw new Exception("Tồn tại mã tham chiếu bên thứ 3");
        }
        
    }
    
    public function insertTransaction(TransactionForm $transactionForm){
        $transactionDao = new TransactionDAO();
        $transactionDao->userDetailID = $transactionForm->getUserDetailId();
        $transactionDao->merchantId = $transactionForm->getMerchantId();
        $transactionDao->amount = $transactionForm->getAmount();
        $transactionDao->type = $transactionForm->getType();
        $transactionDao->idRef = $transactionForm->getIdRef();
        $transactionDao->status = $transactionForm->getStatus();
        $transactionDao->createdTime = $transactionForm->getCreatedTime();
        return $this->transactionDao->saveResultId($transactionDao);
    }
    
    public function getList(TransactionForm $transactionForm){
        if($transactionForm->getUserDetailId()==null){
            throw new Exception("Demo");
        }
        return $this->transactionDao->getList($transactionForm);
    }
    
}

