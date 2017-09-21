<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use Session;
use Exception;
use DB;
use App\Http\Forms\ImportDataCsvForm;
use App\Http\Forms\UserInfoForm;
use App\Http\Services\UserService;
use App\Http\Models\MerchantDAO;
use App\Http\Services\MerchantService;
use App\Http\Models\PartnerDAO;
use App\Http\Services\PartnerService;
use App\Http\Forms\UserForm;
use App\Http\Forms\UserDetailForm;
use App\Http\Forms\TransactionForm;
use App\Http\Services\TransactionService;

class ImportCsvService extends BaseService {
    private $userService;
    private $merchantService;
    private $partnerService;
    private $transactionService;
    public function __construct() {
        $this->merchantService = new MerchantService();
        $this->partnerService = new PartnerService();
        $this->transactionService = new TransactionService();
    }
    
    public function importDataCsv(ImportDataCsvForm $importDataCsvForm){
        $this->userService = new UserService();
        $userForm = new UserForm();
        $userDetailForm = new UserDetailForm();
        $userInfoForm = new UserInfoForm();
        $transactionForm = new TransactionForm();
        /*
         * validate chung
         */
        /*
         * 1 bản ghi hợp lê bao gồm
         * hascode merchant
         * trans id ref
         * amount tổng tiền thanh toán
         * ngày tạo thanh toán
         */
        $checkinfo = true;
        if($importDataCsvForm->getEmail()==null && $importDataCsvForm->getMobile()==null)
            $checkinfo = false;
        if($importDataCsvForm->getHashcodeMerchant() != null &&
                $importDataCsvForm->getIdRef()!=null  && 
                $importDataCsvForm->getAmount()!=null &&
                $importDataCsvForm->getCreatedTime()!=null &&
                $checkinfo
                ){
            /*
             * set giá trị cho transaction form
             */
            $transactionForm->setIdRef($importDataCsvForm->getIdRef());
            $transactionForm->setAmount($importDataCsvForm->getAmount());
            $transactionForm->setCreatedTime($importDataCsvForm->getCreatedTime());

            /*
             * start 
             */
            $merchantDao = new MerchantDAO();
            $merchantDao = $this->merchantService->getMerchantByHashcode($importDataCsvForm->getHashcodeMerchant());
            if($merchantDao!=null){
                /*
                 * merchant tồn tại
                 * kiểm tra partner chuyền vào có phải của 
                 * merchant không nêu không thì lấy partner mặc định của merchant
                 */
                $partnerDao = new PartnerDAO();
                
                $userDetailForm->setMerchantId($merchantDao->id);
                $userDetailForm->setMerchantHashcode($merchantDao->hashcode);
                $userDetailForm->setEmail($importDataCsvForm->getEmail());
                $userDetailForm->setMobile($importDataCsvForm->getMobile());
                $userInfoForm = new UserInfoForm();
                DB::beginTransaction();
                try {
                    $userInfoForm = $this->userService->createUser($userForm,$userDetailForm);
                    if($userInfoForm->getUserId()!=null){
                        $transactionForm->setUserDetailId($userInfoForm->getUserId());
                        $transactionForm->setMerchantId($userInfoForm->getMerchantId());
                        $transactionForm->setType("IMPORT_CSV");
                        $transactionForm->setStatus(env('COMMON_STATUS_SUCCESS'));
                        $transactionForm->setUserDetailId($userInfoForm->getUserId());
                        $transactionForm = $this->transactionService->createTransaction($transactionForm);
                    }
                    DB::commit(); 
                } catch (Exception $ex) {
                    DB::rollback();
                    throw new Exception($ex->getMessage() );
                }
            }else{
                throw new Exception("import_data -> merchant không tồn tại");
            }
        }else{
            throw new Exception("import_data -> thieu_du_lieu_merchant_code");
        }
    }
    
}

