<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Services\LibService;
use App\Http\Services\StoreCategoryService;
use App\Http\Forms\StoreCategoryForm;

class ApiStoreCategoryService extends BaseService {
    private $libService;
    private $storeCategoryService;
    public function __construct() {
        $this->libService = new LibService();
        $this->storeCategoryService = new StoreCategoryService();
    }
    
    public function getListStoreCategory($request){
        $language = $request['language'];
        $response = array();
        $storeCategoryForm = new StoreCategoryForm();
        $listStoreCategoryResponse = array();
        DB::beginTransaction();
        try {
            $listStoreCategory = $this->storeCategoryService->searchListData($storeCategoryForm);
            foreach ($listStoreCategory as $key=>$value){
                $listStoreCategoryResponse[$key]['id']=$value->id;
                $listStoreCategoryResponse[$key]['name']=$value->name_vi;
            }
            DB::commit();
            $response['listCategory'] = $listStoreCategoryResponse;
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    
}

