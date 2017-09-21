<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\PromotionForm;
use App\Http\Models\PromotionDAO;
use App\Http\Services\LibService;
use Exception;

class PromotionService extends BaseService {
    
    private $libService;
    private $promotionDao;
    public function __construct() {
        $this->libService = new LibService();
        $this->promotionDao = new PromotionDAO();
    }
    
    public function insert(PromotionForm $addForm){
        $objDao = new PromotionDAO();
        $objDao->type = $addForm->getType();
        $objDao->idRef = $addForm->getIdRef();
        $objDao->name = $addForm->getName();
        $objDao->dateFrom = $addForm->getDateFrom();
        $objDao->dateTo = $addForm->getDateTo();
        $objDao->description = $addForm->getDescription();
        $objDao->images = $addForm->getLinkImage();
        $objDao->merchantId = $addForm->getMerchantId();
        $objDao->status = $addForm->getStatus();
        return $this->promotionDao->saveResultId($objDao);
    }
    /*
    public function editStore(StoreForm $editForm){
        $storeDao = $this->getDataByHashcode($editForm->getHashcode());
        $storeDao->name = $editForm->getName();
        $storeDao->logo = $editForm->getLinkLogo();
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
    */
    public function searchListData(PromotionForm $searchForm){
        return $this->promotionDao->getList($searchForm);
    }
    
    public function countList(PromotionForm $searchForm){
        $searchForm->setPageSize(null);
        return count($this->promotionDao->getList($searchForm));
    }
    
    public function getDataByHashcode($hashcode){
        return $this->promotionDao->findByHashcode($hashcode);
    }
     
}

