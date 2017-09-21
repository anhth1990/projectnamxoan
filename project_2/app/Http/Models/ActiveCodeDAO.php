<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\ActiveCodeForm;

class ActiveCodeDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_active_code");
    }
    
    public function checkActiveCode(ActiveCodeForm $activeCodeForm){
        try {
            $data = ActiveCodeDAO::select('*');
            $data = $data->where('type',  $activeCodeForm->getType());
            $data = $data->where('idRef',  $activeCodeForm->getIdRef());
            $data = $data->where('activeCode',  $activeCodeForm->getActiveCode());
            if($activeCodeForm->getStatus()!=null){
                $data = $data->where('status', $activeCodeForm->getStatus());
            }
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom($ex->getMessage(). " - ".$ex->getFile() . "- ".$ex->getLine());
            throw new Exception($ex->getMessage());
        }
    }
    
    public function getActiveCodeByIdref(ActiveCodeForm $activeCodeForm){
        try {
            $data = ActiveCodeDAO::select('*');
            $data = $data->where('type',  $activeCodeForm->getType());
            $data = $data->where('idRef',  $activeCodeForm->getIdRef());
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom($ex->getMessage(). " - ".$ex->getFile() . "- ".$ex->getLine());
            throw new Exception(trans("error.error_system"));
        }
    }
    
    public function searchDataFirst(ActiveCodeForm $searchForm){
        try {
            $data = ActiveCodeDAO::select('*');
            if($searchForm->getType() != null){
                $data = $data->where('type', $searchForm->getType());
            }
            if($searchForm->getIdRef() != null){
                $data = $data->where('idRef', $searchForm->getIdRef());
            }
            if($searchForm->getActiveCode() != null){
                $data = $data->where('activeCode', $searchForm->getActiveCode());
            }
            if($searchForm->getStatus() != null){
                $data = $data->where('status', $searchForm->getStatus());
            }
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->orderBy('id', 'desc');
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }

}

?>