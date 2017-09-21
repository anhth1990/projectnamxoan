<?php

namespace App\Http\Services;

/*
 * anhth1990 
 */

use App\Http\Forms\AnswerQuestionForm;
use App\Http\Models\AnswerDAO;
use App\Http\Models\AnswerQuestionDAO;
use App\Http\Models\AnswerUserQuestionDAO;
use App\Http\Services\LibService;

class AnswerQuestionService extends BaseService {

    private $libService;
    private $answerDao;
    private $answerQuestionDao;
    private $userQuestionDao;

    public function __construct() {
        $this->libService = new LibService();
        $this->answerQuestionDao = new AnswerQuestionDAO();
        $this->answerDao = new AnswerDAO();
        $this->userQuestionDao = new AnswerUserQuestionDAO();
    }

    public function searchListData(AnswerQuestionForm $searchForm) {
        return $this->answerDao->getList($searchForm);
    }

    public function countList(AnswerQuestionForm $searchForm) {
        $searchForm->setPageSize(null);
        return count($this->answerDao->getList($searchForm));
    }

    public function searchListQuestion(AnswerQuestionForm $searchForm) {
        return $this->answerQuestionDao->getList($searchForm);
    }
    
    public function searchListUserQuestion(AnswerQuestionForm $searchForm) {
        return $this->userQuestionDao->getList($searchForm);
    }
    
    public function countListUserQuestion(AnswerQuestionForm $searchForm) {
        $searchForm->setPageSize(null);
        return count($this->userQuestionDao->getList($searchForm));
    }

    public function getAnswerByHashcode($hashcode) {
        return $this->answerDao->findByHashcode($hashcode);
    }

    public function getQuestionByHashcode($hashcode) {
        return $this->answerQuestionDao->getDataByHashcode($hashcode);
    }

    public function insertAnswer(AnswerQuestionForm $addForm) {
        $answerDao = new AnswerDAO();
        $answerDao->answer = $addForm->getAnswer();
        $answerDao->type = $addForm->getType();
        $answerDao->merchantId = $addForm->getMerchantId();
        $answerDao->status = $addForm->getStatus();
        return $this->answerDao->saveResultId($answerDao);
    }

    public function insertQuestion(AnswerQuestionForm $addForm) {
        $questionDao = new AnswerQuestionDAO();
        $questionDao->name = $addForm->getName();
        $questionDao->result = $addForm->getResult();
        $questionDao->answerId = $addForm->getAnswerId();
        $questionDao->status = $addForm->getStatus();
        return $this->answerQuestionDao->saveResultId($questionDao);
    }
    
    public function insertUserQuestion(AnswerQuestionForm $addForm) {
        $userQuestionDao = new AnswerUserQuestionDAO();
        $userQuestionDao->questionId = $addForm->getQuestionId();
        $userQuestionDao->userId = $addForm->getUserId();
        $userQuestionDao->status = $addForm->getStatus();
        return $this->userQuestionDao->saveResultId($userQuestionDao);
    }

    public function editAnswer(AnswerQuestionForm $editForm) {
        $answerDao = $this->getAnswerByHashcode($editForm->getHashcode());
        $answerDao->answer = $editForm->getAnswer();
        $answerDao->type = $editForm->getType();
        $answerDao->status = $editForm->getStatus();
        return $this->answerDao->saveResultId($answerDao);
    }

    public function editQUestion(AnswerQuestionForm $editForm) {
        $questionDao = $this->getQuestionByHashcode($editForm->getHashcode());
        $questionDao->name = $editForm->getName();
        $questionDao->result = $editForm->getResult();
        $questionDao->status = $editForm->getStatus();
        return $this->answerQuestionDao->saveResultId($questionDao);
    }

    public function deleteQuestion($hashcode) {
        $questionDao = $this->getQuestionByHashcode($hashcode);
        if ($questionDao != null) {
            $questionDao->status = env('COMMON_STATUS_DELETED');
            $this->answerQuestionDao->saveResultId($questionDao);
        }
    }
    
    public function deleteAnswer($hashcode) {
        $answerDao = $this->getAnswerByHashcode($hashcode);
        if ($answerDao != null) {
            $answerDao->status = env('COMMON_STATUS_DELETED');
            $this->answerDao->saveResultId($answerDao);
        }
        $questionForm = new AnswerQuestionForm();
        $questionForm->setAnswerId($answerDao->id);
        $listQuestion = $this->searchListQuestion($questionForm);
        foreach ($listQuestion as $key => $value) {
            $this->deleteQuestion($value->hashcode);
        }
    }

}
