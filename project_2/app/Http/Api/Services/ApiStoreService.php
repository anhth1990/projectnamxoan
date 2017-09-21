<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Services\LibService;
use App\Http\Api\Services\ApiStoreBranchService;
use App\Http\Forms\StoreForm;
use App\Http\Api\Services\ApiAddressService;
use App\Http\Api\Services\ApiPromotionService;
use App\Http\Forms\PromotionForm;

class ApiStoreService extends BaseService {
    private $libService;
    private $apiStoreBranchService;
    private $apiAddressService;
    private $apiPromotionService;
    public function __construct() {
        $this->libService = new LibService();
        $this->apiStoreBranchService = new ApiStoreBranchService();
        $this->apiAddressService = new ApiAddressService();
        $this->apiPromotionService = new ApiPromotionService();
    }
    
    /*
     * list store branch
     */
    public function listStore($request){
        $language = $request['language'];
        $userId = $request['userId'];
        $merchantId = $request['merchantId'];
        $userDetailId = $request['userDetailId'];
        isset($request['pageIndex'])?$page = $request['pageIndex']:$page=1;
        isset($request['pageSize'])?$pageSize = $request['pageSize']:$pageSize=env('PAGE_SIZE');
        isset($request['categoryId'])?$categoryId = $request['categoryId']:$categoryId=0;
        isset($request['keySearch'])?$keySearch = $request['keySearch']:$keySearch="";
        isset($request['provinceId'])?$provinceId = $request['provinceId']:$provinceId="";
        isset($request['distance'])?$distance = $request['distance']:$distance=10;
        $response = array();
        $listStoreBranchResponse = array();
        DB::beginTransaction();
        try {
            $storeForm = new StoreForm();
            //$storeForm->setPageSize($pageSize);
            //$storeForm->setPageIndex($page);
            $storeForm->setMerchantId($merchantId);
            if($keySearch!=null && $keySearch!=""){
                $storeForm->setKeySearch($keySearch);
            }
            if($categoryId!=null && $categoryId!=0){
                $storeForm->setCategoryId($categoryId);
            }
            if($provinceId!=null && $provinceId!=""){
                $storeForm->setProvinceId($provinceId);
            }else{
                $storeForm->setProvinceId('01');
            }
            $province = $this->apiAddressService->getProvinceById($provinceId);
            if(isset($request['latitude'])&&$request['latitude']!=""){
                $lat = $request['latitude'];
            }else{
                $lat=$province->latitude;
            }
            if(isset($request['longitude'])&&$request['longitude']!=""){
                $long = $request['longitude'];
            }else{
                $long=$province->longitude;
            }
            $listStoreBranch = $this->apiStoreBranchService->searchListData($storeForm);
            DB::commit();
            /*
             * promotion
             */
            $promotionForm = new PromotionForm();
            $promotionForm->setMerchantId($merchantId);
            $promotionForm->setDateNow(date('Y-m-d H:i:s'));
            $promotionForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            $listPromotion = $this->apiPromotionService->searchListData($promotionForm);
            /*/
             * end promotion
             */
            $k = 0;
            foreach ($listStoreBranch as $key=>$value){
                // tính khoảng cách
                $d = $this->libService->distance($lat, $long, $value->lat, $value->long, "K");
                if($d<=$distance){
                    $listStoreBranchResponse[$k]['categoryId']=$value->store->categoryId;
                    $listStoreBranchResponse[$k]['storeId']=$value->store->id;
                    $listStoreBranchResponse[$k]['storeBranchId']=$value->id;
                    $listStoreBranchResponse[$k]['storeBranchHashcode']=$value->hashcode;
                    $listStoreBranchResponse[$k]['storeName']=$value->store->name;
                    $listStoreBranchResponse[$k]['storeBranchName']=$value->name;
                    $listStoreBranchResponse[$k]['mobile']=$value->mobile;
                    $listStoreBranchResponse[$k]['email']=$value->email;
                    $listStoreBranchResponse[$k]['lat']=$value->lat;
                    $listStoreBranchResponse[$k]['long']=$value->long;
                    if($value->openTime==null || $value->closeTime==null){
                        $listStoreBranchResponse[$k]['openTime']="Luôn mở cửa";
                    }else{
                        $listStoreBranchResponse[$k]['openTime']=$value->openTime."-".$value->closeTime;
                    }
                    $listStoreBranchResponse[$k]['address']=$value->address;
                    $listStoreBranchResponse[$k]['provinceId']=$value->provinceId;
                    $listStoreBranchResponse[$k]['districtId']=$value->districtId;
                    $listStoreBranchResponse[$k]['wardId']=$value->wardId;
                    $listStoreBranchResponse[$k]['logo']= asset($value->store->logo);
                    //$listStoreBranchResponse[$key]['promotion']="Giảm giá 20% từ ngày 25/7-1/8/2017";
                    $listStoreBranchResponse[$k]['description']=strip_tags($value->store->description);
                    $listStoreBranchResponse[$k]['distance']=$d;
                    // promotion
                    $keyPromotionArr=0;
                    $listStoreBranchResponse[$k]['listPromotion'] = array();
                    foreach ($listPromotion as $keyPromotion=>$valuePromotion){
                        if($valuePromotion->idRef==$value->store->id || $valuePromotion->idRef==$value->id){
                            $listStoreBranchResponse[$k]['listPromotion'][$keyPromotionArr]['name']=$valuePromotion->name;
                            $listStoreBranchResponse[$k]['listPromotion'][$keyPromotionArr]['description']=$valuePromotion->description;
                            $listStoreBranchResponse[$k]['listPromotion'][$keyPromotionArr]['images']=asset($valuePromotion->images);
                            $listStoreBranchResponse[$k]['listPromotion'][$keyPromotionArr]['promotionId']=$valuePromotion->id;
                            $listStoreBranchResponse[$k]['listPromotion'][$keyPromotionArr]['promotionHascode']=$valuePromotion->hashcode;
                            
                            $keyPromotionArr++;
                        }
                    }
                    $k++;
                }
            }
            $response['listStoreBranch'] = $listStoreBranchResponse;
            //$response['pageSize'] = $pageSize;
            //$response['pageIndex'] = $page;
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    /*
     * detail store branch
     */
    public function detailStoreBranch($request){
        $language = $request['language'];
        $userId = $request['userId'];
        $merchantId = $request['merchantId'];
        $userDetailId = $request['userDetailId'];
        /*
         * validate
         */
        if(!isset($request['storeBranchHashcode']) || $request['storeBranchHashcode']==null){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $branchHashcode = $request['storeBranchHashcode'];
        $storeBranch = $this->apiStoreBranchService->getDataByHashcode($branchHashcode);
        if($storeBranch==null){
            throw new Exception(trans('exception.DATA_NOT_FOUND'),$this->libService->setError("DATA_NOT_FOUND"));
        }
        /*
         * end validate
         */
        $response = array();
        $listStoreBranchResponse = array();
        // promotion
        $promotionForm = new PromotionForm();
        $promotionForm->setMerchantId($merchantId);
        $promotionForm->setDateNow(date('Y-m-d H:i:s'));
        $promotionForm->setStoreId($storeBranch->store->id);
        $promotionForm->setBranchId($storeBranch->id);
        $promotionForm->setStatus(env('COMMON_STATUS_ACTIVE'));
        $listPromotion = $this->apiPromotionService->searchListData($promotionForm);
        // end promotion
        try {
            $response['storeBranchInfo']['storeBranchHashcode']=$storeBranch->hashcode;
            $response['storeBranchInfo']['name']=$storeBranch->name;
            $response['storeBranchInfo']['storeName']=$storeBranch->store->name;
            if($storeBranch->images==null || $storeBranch->images==""){
                $response['storeBranchInfo']['images']=asset(env('IMAGE_DEFAULT'));
            }else{
                $response['storeBranchInfo']['images']=asset($storeBranch->images);
            }
            $response['storeBranchInfo']['email']=$storeBranch->email;
            $response['storeBranchInfo']['mobile']=$storeBranch->mobile;
            $response['storeBranchInfo']['address']=$storeBranch->address;
            $response['storeBranchInfo']['provinceId']=$storeBranch->provinceId;
            $response['storeBranchInfo']['districtId']=$storeBranch->districtId;
            $response['storeBranchInfo']['wardId']=$storeBranch->wardId;
            $response['storeBranchInfo']['lat']=$storeBranch->lat;
            $response['storeBranchInfo']['long']=$storeBranch->long;
            $response['storeBranchInfo']['description']=$storeBranch->store->description;
            if($storeBranch->openTime==null || $storeBranch->closeTime==null){
                $response['storeBranchInfo']['openTime']="Luôn mở cửa";
            }else{
                $response['storeBranchInfo']['openTime']=$storeBranch->openTime."-".$storeBranch->closeTime;
            }
            //promotion
            $keyPromotionArr=0;
            $response['storeBranchInfo']['listPromotion']=array();
            foreach ($listPromotion as $keyPromotion=>$valuePromotion){
                if($valuePromotion->idRef==$storeBranch->store->id || $valuePromotion->idRef==$storeBranch->id){
                    $response['storeBranchInfo']['listPromotion'][$keyPromotionArr]['name']=$valuePromotion->name;
                    $response['storeBranchInfo']['listPromotion'][$keyPromotionArr]['description']=$valuePromotion->description;
                    $response['storeBranchInfo']['listPromotion'][$keyPromotionArr]['images']=asset($valuePromotion->images);
                    $response['storeBranchInfo']['listPromotion'][$keyPromotionArr]['promotionId']=$valuePromotion->id;
                    $response['storeBranchInfo']['listPromotion'][$keyPromotionArr]['promotionHascode']=$valuePromotion->hashcode;
                    $keyPromotionArr++;
                }
            }
            return $response;
        } catch (Exception $ex) {
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    
}

