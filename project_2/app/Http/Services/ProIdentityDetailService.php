<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\ProIdentityDetailForm;
use App\Http\Models\ProIdentityDetailDAO;
use App\Http\Services\LibService;
use Exception;

class ProIdentityDetailService extends BaseService {
    
    private $libService;
    private $identityDetailDao;
    public function __construct() {
        $this->libService = new LibService();
        $this->identityDetailDao = new ProIdentityDetailDAO();
    }
    
    public function insert(ProIdentityDetailForm $addForm){
        $objDao = new ProIdentityDetailDAO();
        $objDao->identity_id = $addForm->getIndentityId();
        $objDao->type = $addForm->getType();
        $objDao->time = $addForm->getTime();
        $objDao->url = $addForm->getUrl();
        $objDao->code = $addForm->getCode();
        if($addForm->getStatus()!=null)
            $objDao->status = $addForm->getStatus();
        else 
            $objDao->status = env ('COMMON_STATUS_ACTIVE');
        return $this->identityDetailDao->saveResultId($objDao);
    }
    
    public function update(ProIdentityDetailForm $addForm) {
        if ($addForm->getHashcode() != null) {
            $objDao = $this->identityDetailDao->findByHashcode($addForm->getHashcode());
            if($addForm->getStatus()!=null){
                $objDao->status = $addForm->getStatus();
            }
            return $this->identityDetailDao->saveResultId($objDao);
        }
        return null;
    }
   
    public function searchListData(ProIdentityDetailForm $searchForm){
        return $this->identityDetailDao->getList($searchForm);
    }
    
    public function countList(NewsForm $searchForm){
        $searchForm->setPageSize(null);
        return count($this->newsDao->getList($searchForm));
    }
    
    public function getDataByHashcode($hashcode){
        return $this->newsDao->findByHashcode($hashcode);
    }
     
}

