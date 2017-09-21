<?php

namespace App\Http\Services;

/*
 * anhth1990 
 */

use App\Http\Forms\ProIdentityForm;
use App\Http\Models\ProIdentityDAO;
use App\Http\Services\LibService;
use Exception;

class ProIdentityService extends BaseService {

    private $libService;
    private $identityDao;

    public function __construct() {
        $this->libService = new LibService();
        $this->identityDao = new ProIdentityDAO();
    }

    public function insert(ProIdentityForm $addForm) {
        $objDao = new ProIdentityDAO();
        $objDao->identity = $addForm->getIndentity();
        $objDao->last_login = $addForm->getLastLogin();
        if ($addForm->getStatus() != null)
            $objDao->status = $addForm->getStatus();
        else
            $objDao->status = env('COMMON_STATUS_ACTIVE');
        return $this->identityDao->saveResultId($objDao);
    }

    public function update(ProIdentityForm $addForm) {
        if ($addForm->getId() != null) {
            $objDao = $this->identityDao->findById($addForm->getId());
            if($addForm->getStatus()!=null){
                $objDao->status = $addForm->getStatus();
            }
            if($addForm->getName()!=null){
                $objDao->name = $addForm->getName();
            }
            if($addForm->getLastLogin()!=null){
                $objDao->last_login = $addForm->getLastLogin();
            }
            return $this->identityDao->saveResultId($objDao);
        }
        return null;
    }

    public function searchListData(ProIdentityForm $searchForm) {
        return $this->identityDao->getList($searchForm);
    }

    public function countList(ProIdentityForm $searchForm) {
        $searchForm->setPageSize(null);
        return count($this->identityDao->getList($searchForm));
    }

    public function getDataByHashcode($hashcode) {
        return $this->identityDao->findByHashcode($hashcode);
    }

    public function getDataByIdenty($identity) {
        return $this->identityDao->getDataByIdentity($identity);
    }

}
