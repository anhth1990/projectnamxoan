<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use DB;
use App\Http\Models\LevelUserDAO;
use App\Http\Forms\LevelUserForm;

class LevelUserService extends BaseService {
    private $levelUserDao;
    
    public function __construct() {
        $this->levelUserDao = new LevelUserDAO();
        
    }
    
    public function searchMultiData(LevelUserForm $searchForm){
        return $this->levelUserDao->searchMultiData($searchForm);
    }
    
}

