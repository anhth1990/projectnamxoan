<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\StoreCategoryForm;
use App\Http\Models\StoreDAO;

class StoreCategoryDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_store_category");
    }
    
    public function store()
    {
        return $this->hasMany(new StoreDAO(),'categoryId');
    }
    
    public function getList(StoreCategoryForm $searchForm){
        try {
            $data = StoreCategoryDAO::select('*');
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->orderBy('id', 'asc');
            $data = $data->get();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }

}

?>