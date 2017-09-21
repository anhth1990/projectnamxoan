<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\LevelUserForm;

class LevelUserDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_level_user");
    }
    
    public function searchMultiData(LevelUserForm $searchForm){
        try {
            $data = LevelUserDAO::select('*');
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->orderBy('id', 'desc');
            if($searchForm->getPageSize() != null){
                $data = $data->paginate($searchForm->getPageSize());;
            }else{
                $data = $data->get();
            }
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }
}

?>