<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\ProIdentityDetailForm;
use App\Http\Models\ProIdentityDAO;

class ProIdentityDetailDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("pro_identity_detail");
    }
    
    public function identity()
    {
        return $this->belongsTo(new ProIdentityDAO(),'identity_id','id');
    }
    
    public function getList(ProIdentityDetailForm $searchForm){
        try {
            $data = ProIdentityDetailDAO::select('*');
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

}

?>