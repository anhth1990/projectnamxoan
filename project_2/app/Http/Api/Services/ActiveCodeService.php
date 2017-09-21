<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Services\LibService;
use App\Http\Models\ActiveCodeDAO;
use App\Http\Forms\ActiveCodeForm;

class ActiveCodeService extends BaseService  {
    private $activeCodeDao;
    public function __construct() {
        $this->activeCodeDao = new ActiveCodeDAO();
    }
    
    /*
     * get active code by user
     */
    public function getActiveCodeByIdRef($type,$idRef){
        $activeCodeForm = new ActiveCodeForm();
        $activeCodeForm->setType($type);
        $activeCodeForm->setIdRef($idRef);
        return $this->activeCodeDao->getActiveCodeByIdref($activeCodeForm);
    }
    
    /*
     * get active code by user
     */
    public function getActiveCodeByCode($type,$idRef,$code){
        $activeCodeForm = new ActiveCodeForm();
        $activeCodeForm->setType($type);
        $activeCodeForm->setIdRef($idRef);
        $activeCodeForm->setActiveCode($code);
        return $this->activeCodeDao->checkActiveCode($activeCodeForm);
    }
    
    
}

