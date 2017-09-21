<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;

class WardDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_ward");
    }
    
    public function getWard(){
        try {
            $data = WardDAO::select('*');
            $data = $data->get();
            return $data;
        } catch (Exception $ex) {
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function getWardById($id) {
        try {
            return WardDAO::select("*")->where("wardid", "=", $id)->first();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}

?>