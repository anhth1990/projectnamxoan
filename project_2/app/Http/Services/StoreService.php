<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\StoreForm;
use App\Http\Models\StoreDAO;
use App\Http\Services\LibService;
use App\Http\Services\StoreBranchService;
use Exception;

class StoreService extends BaseService {
    
    private $libService;
    private $storeDao;
    private $storeBranchService;
    public function __construct() {
        $this->libService = new LibService();
        $this->storeDao = new StoreDAO();
        $this->storeBranchService = new StoreBranchService();
    }
    
    public function insertStore(StoreForm $addForm){
        $storeDao = new StoreDAO();
        $storeDao->name = $addForm->getName();
        $storeDao->categoryId = 1;
        $storeDao->logo = $addForm->getLinkLogo();
        $storeDao->description = $addForm->getDescription();
        $storeDao->merchantId = $addForm->getMerchantId();
        $storeDao->status = $addForm->getStatus();
        return $this->storeDao->saveResultId($storeDao);
    }
    
    public function editStore(StoreForm $editForm){
        $storeDao = $this->getDataByHashcode($editForm->getHashcode());
        $storeDao->name = $editForm->getName();
        $storeDao->logo = $editForm->getLinkLogo();
        $storeDao->description = $editForm->getDescription();
        $storeDao->status = $editForm->getStatus();
        return $this->storeDao->saveResultId($storeDao);
    }
    
    public function deleteData($hashcode){
        $storeDao = $this->getDataByHashcode($hashcode);
        if($storeDao!=null){
            $storeDao->status = env('COMMON_STATUS_DELETED');
            $this->storeDao->saveResultId($storeDao);
        }
        $storeForm = new StoreForm();
        $storeForm->setStoreId($storeDao->id);
        $listStoreBranch = $this->storeBranchService->searchListData($storeForm);
        foreach ($listStoreBranch as $key=>$value){
            $this->storeBranchService->deleteData($value->hashcode);
        }
    }
    
    public function searchListData(StoreForm $searchForm){
        return $this->storeDao->getList($searchForm);
    }
    
    public function countList(StoreForm $searchForm){
        $searchForm->setPageSize(null);
        return count($this->storeDao->getList($searchForm));
    }
    
    public function getDataByHashcode($hashcode){
        return $this->storeDao->findByHashcode($hashcode);
    }
}

