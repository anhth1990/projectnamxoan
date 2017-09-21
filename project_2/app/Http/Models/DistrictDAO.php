<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;

class DistrictDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_district");
    }
    
    public function getDistrict(){
        try {
            $data = DistrictDAO::select('*');
            $data = $data->get();
            return $data;
        } catch (Exception $ex) {
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function getDistrictById($id) {
        try {
            return DistrictDAO::select("*")->where("districtid", "=", $id)->first();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    
    public function getDistrictByProvinceId($provinceId) {
        try {
            return DistrictDAO::select("*")->where("provinceid", "=", $provinceId)->get();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}

?>