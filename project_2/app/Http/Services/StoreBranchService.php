<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\StoreForm;
use App\Http\Models\StoreBranchDAO;
use Exception;
use App\Http\Services\LibService;

class StoreBranchService extends BaseService {
    private $libService;
    private $storeBranchDao;
    public function __construct() {
        $this->libService = new LibService();
        $this->storeBranchDao = new StoreBranchDAO();
        
    }
    
    public function insertStoreBranch(StoreForm $addForm){
        $storeBranchDao = new StoreBranchDAO();
        $storeBranchDao->name = $addForm->getNameBranch();
        $storeBranchDao->images = $addForm->getLinkImages();
        $storeBranchDao->address = $addForm->getAddress();
        $storeBranchDao->provinceId = $addForm->getProvinceId();
        $storeBranchDao->districtId = $addForm->getDistrictId();
        $storeBranchDao->wardId = $addForm->getWardId();
        $storeBranchDao->email = $addForm->getEmail();
        $storeBranchDao->mobile = $addForm->getMobile();
        $storeBranchDao->lat = $addForm->getLat();
        $storeBranchDao->long = $addForm->getLong();
        $storeBranchDao->openTime = $addForm->getOpenTime();
        $storeBranchDao->closeTime = $addForm->getCloseTime();
        $storeBranchDao->merchantId = $addForm->getMerchantId();
        $storeBranchDao->storeId = $addForm->getStoreId();
        $storeBranchDao->status = $addForm->getStatus();
        return $this->storeBranchDao->saveResultId($storeBranchDao);
    }
    
    public function editStoreBranch(StoreForm $editForm){
        $storeBranchDao = $this->getDataByHashcode($editForm->getHashcode());
        $storeBranchDao->name = $editForm->getNameBranch();
        $storeBranchDao->images = $editForm->getLinkImages();
        $storeBranchDao->address = $editForm->getAddress();
        $storeBranchDao->provinceId = $editForm->getProvinceId();
        $storeBranchDao->districtId = $editForm->getDistrictId();
        $storeBranchDao->wardId = $editForm->getWardId();
        $storeBranchDao->email = $editForm->getEmail();
        $storeBranchDao->mobile = $editForm->getMobile();
        $storeBranchDao->lat = $editForm->getLat();
        $storeBranchDao->long = $editForm->getLong();
        $storeBranchDao->openTime = $editForm->getOpenTime();
        $storeBranchDao->closeTime = $editForm->getCloseTime();
        $storeBranchDao->status = $editForm->getStatus();
        return $this->storeBranchDao->saveResultId($storeBranchDao);
    }
    
    public function deleteData($hashcode){
        $storeBranchDao = $this->getDataByHashcode($hashcode);
        if($storeBranchDao!=null){
            $storeBranchDao->status = env('COMMON_STATUS_DELETED');
            $this->storeBranchDao->saveResultId($storeBranchDao);
        }
    }
    
    public function getDataByHashcode($hashcode){
        return $this->storeBranchDao->getDataByHashcode($hashcode);
    }
    
    public function searchListData(StoreForm $searchForm){
        return $this->storeBranchDao->searchData($searchForm);
    }
    
    public function countList(StoreForm $searchForm){
        $searchForm->setPageSize(null);
        return count($this->storeBranchDao->searchData($searchForm));
    }
}

