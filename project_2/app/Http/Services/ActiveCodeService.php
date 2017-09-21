<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\ActiveCodeForm;
use App\Http\Models\ActiveCodeDAO;

class ActiveCodeService extends BaseService {
    private $activeCodeDao;
    public function __construct() {
        $this->activeCodeDao = new ActiveCodeDAO();
    }
    
    /*
     * insert active code
     */
    public function insertActiveCode(ActiveCodeForm $activeCodeForm){
        $activeCode = new ActiveCodeDAO();
        $activeCode->type = $activeCodeForm->getType();
        $activeCode->idRef = $activeCodeForm->getIdRef();
        $activeCode->activeCode = $this->getActiveCode();
        $activeCode->createdDate = date(env('DATE_FORMAT_Y_M_D'),time());
        $activeCode->status = $activeCodeForm->getStatus();
        return $this->activeCodeDao->saveResultId($activeCode);
    }
    /*
     * update active code
     */
    public function updateActiveCode(ActiveCodeForm $activeCodeForm){
        $activeCode = $this->activeCodeDao->findById($activeCodeForm->getId());
        if($activeCodeForm->getStatus()!=null){
            $activeCode->status = $activeCodeForm->getStatus();
        }
        if($activeCodeForm->getCreatedDate()!=null){
            $activeCode->createdDate = $activeCodeForm->getCreatedDate();
        }
        return $this->activeCodeDao->saveResultId($activeCode);
    }
    /*
     * search data first
     */
    public function searchDataFirst(ActiveCodeForm $activeCodeForm){
        return $this->activeCodeDao->searchDataFirst($activeCodeForm);
    }
    
    /*
     * get active code
     */
    public function getActiveCode(){
        $code = "";
        $code = substr( rand(1000,999999), 0, 4);
        return $code;
    }
    
}

