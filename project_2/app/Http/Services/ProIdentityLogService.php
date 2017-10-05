<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\ProIdentityLogForm;
use App\Http\Models\ProIdentityLogDAO;
use App\Http\Services\LibService;
use Exception;

class ProIdentityLogService extends BaseService {
    
    private $libService;
    private $identityLogDao;
    public function __construct() {
        $this->libService = new LibService();
        $this->identityLogDao = new ProIdentityLogDAO();
    }
    
    public function insert(ProIdentityLogForm $addForm){
        $objDao = new ProIdentityLogDAO();
        $objDao->identity_id = $addForm->getIndentityId();
        $objDao->ip = $addForm->getIp();
        $objDao->time_log = $addForm->getLogTime();
        if($addForm->getStatus()!=null)
            $objDao->status = $addForm->getStatus();
        else 
            $objDao->status = env ('COMMON_STATUS_ACTIVE');
        return $this->identityLogDao->saveResultId($objDao);
    }
    
    public function searchListData(ProIdentityLogForm $searchForm) {
        return $this->identityLogDao->getList($searchForm);
    }

    public function countList(ProIdentityLogForm $searchForm) {
        $searchForm->setPageSize(null);
        return count($this->identityLogDao->getList($searchForm));
    }
     
}

