<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Models\StoreBranchDAO;
use App\Http\Forms\StoreForm;
use App\Http\Services\StoreBranchService;

class ApiStoreBranchService extends BaseService {
    
    private $storeBranchDao;
    private $storeBranchService;
    public function __construct() {
        $this->storeBranchDao = new StoreBranchDAO();
        $this->storeBranchService = new StoreBranchService();
    }
    
    /*
     * list store
     */
    public function searchListData(StoreForm $searchForm){
        return $this->storeBranchDao->searchData($searchForm);
    }
    
    /*
     * get data by id
     */
    public function getDataByHashcode($hashcode){
        return $this->storeBranchService->getDataByHashcode($hashcode);
    }
    
}

