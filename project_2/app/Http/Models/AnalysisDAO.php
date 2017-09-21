<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\AnalysisForm;

class AnalysisDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_analysis");
    }
    
    public function getList(AnalysisForm $searchForm){
        try {
            $data = AnalysisDAO::select('*');
            if($searchForm->getMerchantId()!=null)
                $data = $data->where('merchantId',  $searchForm->getMerchantId());
            if($searchForm->getCode()!=null){
                $data = $data->where('code' ,$searchForm->getCode());
            }
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->orderBy('id', 'desc');
            return $data->get();
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }

}

?>