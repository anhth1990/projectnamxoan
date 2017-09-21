<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Models\ProvinceDAO;
use App\Http\Models\DistrictDAO;
use App\Http\Models\WardDAO;
use Session;
class AddressService extends BaseService {
    public function __construct() {
        $this->provinceDao = new ProvinceDAO();
        $this->districtDao = new DistrictDAO();
        $this->wardDao = new WardDAO();
    }
    
    public function getProvince(){
        return $this->provinceDao->getProvince();
    }
    
    public function getDistrict(){
        return $this->districtDao->getDistrict();
    }
    
    public function getWard(){
        return $this->wardDao->getWard();
    }
    
    public function getProvinceById($id){
        return $this->provinceDao->getProvinceById($id);
    }
    
    public function getDistrictById($id){
        return $this->districtDao->getDistrictById($id);
    }
    
    public function getWardById($id){
        return $this->wardDao->getWardById($id);
    }
    
    public function getDistrictIdByProvinceId($provinceId){
        return $this->districtDao->getDistrictByProvinceId($provinceId);
    }
    
}

