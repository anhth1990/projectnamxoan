<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\ProIdentityForm;
use App\Http\Models\ProIdentityDetailDAO;
use App\Http\Models\ProIdentityLogDAO;

class ProIdentityDAO extends BaseDAO {
    
    //public $timestamps = false;
    
    public function __construct() {
        parent::__construct("pro_identity");
    }
    
    public function identityDetail()
    {
        return $this->hasMany(new ProIdentityDetailDAO(),'identity_id')->where('status', '!=' , env('COMMON_STATUS_DELETED'));
    }
    
    public function identityLog()
    {
        return $this->hasMany(new ProIdentityLogDAO(),'identity_id')->where('status', '!=' , env('COMMON_STATUS_DELETED'));
    }
    
    public function getList(ProIdentityForm $searchForm){
        try {
            $data = ProIdentityDAO::select('*');
            if($searchForm->getName()!=null){
                $data = $data->where('name', 'like' ,'%'.$searchForm->getName().'%');
            }
            if($searchForm->getIndentity()!=null){
                $data = $data->where('identity', 'like' ,'%'.$searchForm->getIndentity().'%');
            }
            if($searchForm->getStatus()!=null){
                $data = $data->where('status', $searchForm->getStatus());
            }
            
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            if($searchForm->getOrderName()!=null){
                $data = $data->orderBy('name', 'asc');
            }else{
                $data = $data->orderBy('id', 'asc');
            }
            
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
    
    public function getDataByIdentity($identity) {
        try {
            $data = ProIdentityDAO::select("*")->where("identity", "=", $identity);
            $data = $data->where("status", "!=", env('COMMON_STATUS_DELETED'));
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }

}

?>