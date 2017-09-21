<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Services\LibService;
use App\Http\Services\AnswerQuestionService;
use App\Http\Forms\AnswerQuestionForm;

class ApiSurveyService extends BaseService {
    private $libService;
    private $answerQuestionService;
    public function __construct() {
        $this->libService = new LibService();
        $this->answerQuestionService = new AnswerQuestionService();
        
    }
    
    /*
     * list survey
     */
    public function listSurvey($request){
        $language = $request['language'];
        $userId = $request['userId'];
        $merchantId = $request['merchantId'];
        $userDetailId = $request['userDetailId'];
        isset($request['pageIndex'])?$page = $request['pageIndex']:$page=1;
        isset($request['pageSize'])?$pageSize = $request['pageSize']:$pageSize=env('PAGE_SIZE');
        $response = array();
        $listSurveyResponse = array();
        DB::beginTransaction();
        try {
            $answerQuestionForm = new AnswerQuestionForm();
            $answerQuestionForm->setMerchantId($merchantId);
            $answerQuestionForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            $answerQuestionForm->setType('RATE');
            $answerQuestionForm->setPageSize($pageSize);
            $answerQuestionForm->setPageIndex($page);
            $listAnswer = $this->answerQuestionService->searchListData($answerQuestionForm);
            // list question
            $questionForm = new AnswerQuestionForm();
            $questionForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            $listQuestion = $this->answerQuestionService->searchListQuestion($questionForm);
            // list user question
            $userQuestionForm = new AnswerQuestionForm();
            $userQuestionForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            $userQuestionForm->setUserId($userId);
            $listUserQuestion = $this->answerQuestionService->searchListUserQuestion($userQuestionForm);
            // list response
            $k=0;
            foreach ($listAnswer as $key=>$value){
                $check = true;
                if(count($listUserQuestion)>0){
                    // loại những câu hỏi user đã trả lời
                    foreach ($listUserQuestion as $keyUserQuestion=>$valueUserQuestion){
                        if($valueUserQuestion->question->answerId==$value->id){
                            $check = false;
                        }
                    }
                }
                if($check){
                    $listSurveyResponse[$k]['hashcode']=$value->hashcode;
                    $listSurveyResponse[$k]['answerId']=$value->id;
                    $listSurveyResponse[$k]['answer']=$value->answer;
                    $keyQues = 0;
                    foreach ($listQuestion as $keyQuestion=>$valueQuestion){
                        if($valueQuestion->answerId == $value->id){
                            $listSurveyResponse[$k]['questions'][$keyQues]['hashcode'] = $valueQuestion->hashcode;
                            $listSurveyResponse[$k]['questions'][$keyQues]['questionId'] = $valueQuestion->id;
                            $listSurveyResponse[$k]['questions'][$keyQues]['question'] = $valueQuestion->name;
                            $keyQues++;
                        }
                    }
                    $k++;
                }
            }
            
            $response['listSurvey'] = $listSurveyResponse;
            $response['pageSize'] = $pageSize;
            $response['pageIndex'] = $page;
            DB::commit();
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
    public function surveyQuestion($request){
        $language = $request['language'];
        $userId = $request['userId'];
        $merchantId = $request['merchantId'];
        $userDetailId = $request['userDetailId'];
        isset($request['pageIndex'])?$page = $request['pageIndex']:$page=1;
        isset($request['pageSize'])?$pageSize = $request['pageSize']:$pageSize=env('PAGE_SIZE');
        if(!isset($request['listQuestion'])){
            throw new Exception(trans('exception.INVALID_PARAMETER'),$this->libService->setError("INVALID_PARAMETER"));
        }
        $listQuestion = $request['listQuestion'];
        $response = array();
        DB::beginTransaction();
        try {
            foreach ($listQuestion as $key=>$value){
                $hashcode = $value['hashcode'];
                $question = $this->answerQuestionService->getQuestionByHashcode($hashcode);
                if($question==null)
                    continue;
                $userQuestionForm = new AnswerQuestionForm();
                $userQuestionForm->setUserId($userId);
                $userQuestionForm->setQuestionId($question->id);
                $userQuestionForm->setStatus(env('COMMON_STATUS_ACTIVE'));
                $this->answerQuestionService->insertUserQuestion($userQuestionForm);
            }
            DB::commit();
            return $response;
        } catch (Exception $ex) {
            DB::rollback();
            throw new Exception(trans('exception.SYSTEM_ERROR'),$this->libService->setError("SYSTEM_ERROR"));
        }
    }
    
}

