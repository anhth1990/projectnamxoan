<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\ConfigForm;

class ConfigDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_config");
    }
    
    public function getList(ConfigForm $searchForm){
        try {
            $data = ConfigDAO::select('*');
            if($searchForm->getMerchantId()!=null)
                $data = $data->where('merchantId',  $searchForm->getMerchantId());
            if($searchForm->getKey()!=null){
                $data = $data->where('key' ,$searchForm->getKey());
            }
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->orderBy('id', 'desc');
            if($searchForm->getPageSize()!=null)
                $data = $data->paginate($searchForm->getPageSize(), ['*'], 'page', $searchForm->getPageIndex());
            else
                $data = $data->get();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }
    
    public function getDataByKey($key,$merchantId){
        try {
            $data = ConfigDAO::select('*');
            $data = $data->where('merchantId',  $merchantId);
            $data = $data->where('key' ,$key); 
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            return $data->first();
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }

}

?>