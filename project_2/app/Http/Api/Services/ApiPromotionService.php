<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Services\LibService;
use App\Http\Services\PromotionService;
use App\Http\Forms\PromotionForm;

class ApiPromotionService extends BaseService  {
    private $libService;
    private $promotionService;
    public function __construct() {
        $this->libService = new LibService();
        $this->promotionService = new PromotionService();
    }
    
    /*
     * list promotion
     */
    public function listPromotion($request){
        $language = $request['language'];
        $userId = $request['userId'];
        $merchantId = $request['merchantId'];
        $userDetailId = $request['userDetailId'];
        isset($request['pageIndex'])?$page = $request['pageIndex']:$page=1;
        isset($request['pageSize'])?$pageSize = $request['pageSize']:$pageSize=env('PAGE_SIZE');
        $response = array();
        $listPromotionResponse = array();
        DB::beginTransaction();
        try {
            $promotionForm = new PromotionForm();
            $promotionForm->setPageSize($pageSize);
            $promotionForm->setPageIndex($page);
            $promotionForm->setMerchantId($merchantId);
            $promotionForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            $listPromotion = $this->promotionService->searchListData($promotionForm);
            DB::commit();
            foreach ($listPromotion as $key=>$value){
                $listPromotionResponse[$key]['id']=$value->id;
                $listPromotionResponse[$key]['hashcode']=$value->hashcode;
                $listPromotionResponse[$key]['images']=asset($value->images);
                $listPromotionResponse[$key]['name']=$value->name;
                $listPromotionResponse[$key]['type']=$value->type;
                $listPromotionResponse[$key]['idRef']=$value->idRef;
                $listPromotionResponse[$key]['dateFrom']=$this->formatDate($value->dateFrom);
                if($value->dateTo!=null){
                    $listPromotionResponse[$key]['dateTo']=$this->formatDate($value->dateTo);
                }
                $listPromotionResponse[$key]['description']=$value->description;
                if($value->dateTo==null || $value->dateFrom==$value->dateTo){
                    $listPromotionResponse[$key]['dateTo']=null;
                }
            }
            $response['listPromotion'] = $listPromotionResponse;
            $response['pageSize'] = $pageSize;
            $response['pageIndex'] = $page;
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    /*
     * detail store branch
     */
    public function detailPromotion($request){
        $language = $request['language'];
        $userId = $request['userId'];
        $merchantId = $request['merchantId'];
        $userDetailId = $request['userDetailId'];
        /*
         * validate
         */
        if(!isset($request['promotionHashcode']) || $request['promotionHashcode']==null){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $promotionHashcode = $request['promotionHashcode'];
        $promotion = $this->promotionService->getDataByHashcode($promotionHashcode);
        if($promotion==null){
            throw new Exception(trans('exception.DATA_NOT_FOUND'),$this->libService->setError("DATA_NOT_FOUND"));
        }
        /*
         * end validate
         */
        $response = array();
        $promotionDetail = array();
        try {
            $response['promotionInfo']['promotionHashcode']=$promotion->hashcode;
            $response['promotionInfo']['name']=$promotion->name;
            if($promotion->images==null || $promotion->images==""){
                $response['promotionInfo']['images']=asset(env('IMAGE_DEFAULT'));
            }else{
                $response['promotionInfo']['images']=asset($promotion->images);
            }
            $response['promotionInfo']['description']=$promotion->description;
            $response['promotionInfo']['dateFrom']=$this->formatDate($promotion->dateFrom);
            if($promotion->dateTo!=null){
                $response['promotionInfo']['dateTo']=$this->formatDate($promotion->dateTo);
            }
            if($promotion->dateTo==null || $promotion->dateFrom==$promotion->dateTo){
                $response['promotionInfo']['dateTo']=null;
            }
            return $response;
        } catch (Exception $ex) {
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    public function searchListData(PromotionForm $promotionForm){
        return $this->promotionService->searchListData($promotionForm);
    }
    
}

