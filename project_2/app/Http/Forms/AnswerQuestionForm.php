<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;

class AnswerQuestionForm extends BaseForm {

    private $name;
    private $result;
    private $answerId;
    private $answer;
    private $type;
    private $userId;
    private $questionId;
    private $listQuestion;
    private $listResult;
    private $statusAnswer;
    private $hashcodeAnswer;

    function getName() {
        return $this->name;
    }

    function getResult() {
        return $this->result;
    }

    function getAnswerId() {
        return $this->answerId;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setResult($result) {
        $this->result = $result;
    }

    function setAnswerId($answerId) {
        $this->answerId = $answerId;
    }

    function getAnswer() {
        return $this->answer;
    }

    function getType() {
        return $this->type;
    }

    function getUserId() {
        return $this->userId;
    }

    function getQuestionId() {
        return $this->questionId;
    }

    function setAnswer($answer) {
        $this->answer = $answer;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setUserId($userId) {
        $this->userId = $userId;
    }

    function setQuestionId($questionId) {
        $this->questionId = $questionId;
    }

    function getListQuestion() {
        return $this->listQuestion;
    }

    function setListQuestion($listQuestion) {
        $this->listQuestion = $listQuestion;
    }

    function getListResult() {
        return $this->listResult;
    }

    function setListResult($listResult) {
        $this->listResult = $listResult;
    }

    function getStatusAnswer() {
        return $this->statusAnswer;
    }

    function setStatusAnswer($statusAnswer) {
        $this->statusAnswer = $statusAnswer;
    }

    function getHashcodeAnswer() {
        return $this->hashcodeAnswer;
    }

    function setHashcodeAnswer($hashcodeAnswer) {
        $this->hashcodeAnswer = $hashcodeAnswer;
    }

}
