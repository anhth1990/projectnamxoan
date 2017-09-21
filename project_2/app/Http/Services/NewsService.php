<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\NewsForm;
use App\Http\Models\NewsDAO;
use App\Http\Services\LibService;
use Exception;

class NewsService extends BaseService {
    
    private $libService;
    private $newsDao;
    public function __construct() {
        $this->libService = new LibService();
        $this->newsDao = new NewsDAO();
    }
    
    public function insert(NewsForm $addForm){
        $objDao = new NewsDAO();
        $objDao->name = $addForm->getName();
        $objDao->images = $addForm->getLinkImage();
        $objDao->description = $addForm->getDescription();
        $objDao->merchantId = $addForm->getMerchantId();
        $objDao->status = $addForm->getStatus();
        return $this->newsDao->saveResultId($objDao);
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
    public function searchListData(NewsForm $searchForm){
        return $this->newsDao->getList($searchForm);
    }
    
    public function countList(NewsForm $searchForm){
        $searchForm->setPageSize(null);
        return count($this->newsDao->getList($searchForm));
    }
    
    public function getDataByHashcode($hashcode){
        return $this->newsDao->findByHashcode($hashcode);
    }
     
}

