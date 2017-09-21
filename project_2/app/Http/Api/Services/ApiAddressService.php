<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Services\LibService;
use App\Http\Services\AddressService;

class ApiAddressService extends BaseService {
    private $libService;
    private $addressService;
    public function __construct() {
        $this->libService = new LibService();
        $this->addressService = new AddressService();
    }
    
    public function getListProvince($request){
        $language = $request['language'];
        $response = array();
        $listProvinceResponse = array();
        DB::beginTransaction();
        $k = 0;
        try {
            $listProvince = $this->addressService->getProvince(); 
            foreach ($listProvince as $key=>$value){
                if($value->provinceid=="01"||$value->provinceid=="79"){
                    $listProvinceResponse[$k]['id']=$value->provinceid;
                    $listProvinceResponse[$k]['name']=$value->name;
                    $k++;
                }
            }
            DB::commit();
            $response['listProvince'] = $listProvinceResponse;
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    public function getListDistrict($request){
        $language = $request['language'];
        $response = array();
        if(!isset($request['provinceId'])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $listDistrictResponse = array();
        DB::beginTransaction();
        try {
            $listDistrict = $this->addressService->getDistrictIdByProvinceId($request['provinceId']); 
            foreach ($listDistrict as $key=>$value){
                $listDistrictResponse[$key]['id']=$value->districtid;
                $listDistrictResponse[$key]['name']=$value->name;
            }
            DB::commit();
            $response['listDistrict'] = $listDistrictResponse;
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    public function getProvinceById($provinceID){
        return $this->addressService->getProvinceById($provinceID);
    }
    
    
}

