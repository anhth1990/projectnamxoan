<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\StoreCategoryForm;
use App\Http\Models\StoreCategoryDAO;
use Exception;
use App\Http\Services\LibService;

class StoreCategoryService extends BaseService {
    private $libService;
    private $storeCategoryDao;
    public function __construct() {
        $this->libService = new LibService();
        $this->storeCategoryDao = new StoreCategoryDAO();
        
    }
    
    public function searchListData(StoreCategoryForm $searchForm){
        return $this->storeCategoryDao->getList($searchForm);
    }
}

