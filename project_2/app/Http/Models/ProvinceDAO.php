<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;

class ProvinceDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_province");
    }
    
    public function getProvince(){
        try {
            $data = ProvinceDAO::select('*');
            $data = $data->get();
            return $data;
        } catch (Exception $ex) {
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function getProvinceById($id) {
        try {
            return ProvinceDAO::select("*")->where("provinceid", "=", $id)->first();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}

?>