<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Services\LibService;
use App\Http\Services\NewsService;
use App\Http\Forms\NewsForm;

class ApiNewsService extends BaseService {
    private $libService;
    private $newsService;
    public function __construct() {
        $this->libService = new LibService();
        $this->newsService = new NewsService();
    }
    
    /*
     * list promotion
     */
    public function listNews($request){
        $language = $request['language'];
        $userId = $request['userId'];
        $merchantId = $request['merchantId'];
        $userDetailId = $request['userDetailId'];
        isset($request['pageIndex'])?$page = $request['pageIndex']:$page=1;
        isset($request['pageSize'])?$pageSize = $request['pageSize']:$pageSize=env('PAGE_SIZE');
        $response = array();
        $listNewsResponse = array();
        DB::beginTransaction();
        try {
            $newsForm = new NewsForm();
            $newsForm->setPageSize($pageSize);
            $newsForm->setPageIndex($page);
            $newsForm->setMerchantId($merchantId);
            $newsForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            $listNews = $this->newsService->searchListData($newsForm);
            DB::commit();
            foreach ($listNews as $key=>$value){
                $listNewsResponse[$key]['id']=$value->id;
                $listNewsResponse[$key]['hashcode']=$value->hashcode;
                $listNewsResponse[$key]['images']=asset($value->images);
                $listNewsResponse[$key]['name']=$value->name;
                $listNewsResponse[$key]['description']=$value->description;
            }
            $response['listNews'] = $listNewsResponse;
            $response['pageSize'] = $pageSize;
            $response['pageIndex'] = $page;
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    /*
     * detail news
     */
    public function detailNews($request){
        $language = $request['language'];
        $userId = $request['userId'];
        $merchantId = $request['merchantId'];
        $userDetailId = $request['userDetailId'];
        /*
         * validate
         */
        if(!isset($request['newsHashcode']) || $request['newsHashcode']==null){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $newsHashcode = $request['newsHashcode'];
        $news = $this->newsService->getDataByHashcode($newsHashcode);
        if($news==null){
            throw new Exception(trans('exception.DATA_NOT_FOUND'),$this->libService->setError("DATA_NOT_FOUND"));
        }
        /*
         * end validate
         */
        $response = array();
        $newsDetail = array();
        try {
            $response['newsInfo']['newsHashcode']=$news->hashcode;
            $response['newsInfo']['name']=$news->name;
            if($news->images==null || $news->images==""){
                $response['newsInfo']['images']=asset(env('IMAGE_DEFAULT'));
            }else{
                $response['newsInfo']['images']=asset($news->images);
            }
            $response['newsInfo']['description']=$news->description;
            return $response;
        } catch (Exception $ex) {
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    
}

