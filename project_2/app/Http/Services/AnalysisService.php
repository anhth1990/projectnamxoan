<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use DB;
use Exception;
use App\Http\Models\AnalysisDAO;
use App\Http\Forms\AnalysisForm;

class AnalysisService extends BaseService {
    
    private $analysisDao;
    public function __construct() {
        parent::__construct();
        $this->analysisDao = new AnalysisDAO();
    }
    
    public function searchListData(AnalysisForm $searchForm){
        return $this->analysisDao->getList($searchForm);
    }
    
    public function insert(AnalysisForm $analysisForm){
        $analysis = new AnalysisDAO();
        $analysis->code = $analysisForm->getCode();
        $analysis->minValue = $analysisForm->getMinValue();
        $analysis->maxValue = $analysisForm->getMaxValue();
        $analysis->point = $analysisForm->getPoint();
        $analysis->status = $analysisForm->getStatus();
        $analysis->merchantId = $analysisForm->getMerchantId();
        $this->analysisDao->saveResultId($analysis);
    }
    
    public function delete($id){
        $analysis = $this->analysisDao->findById($id);
        if($analysis!=null){
            $this->analysisDao->deleteData($analysis);
        }
    }
    
    
    
}

