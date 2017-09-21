<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class RatingForm extends BaseForm{
    private $ratingGroupId;
    private $ratingGroupName;
    private $order;
    private $code;
    private $listRatingGroupId;
    private $recensyName;
    private $frequencyName;
    private $monetaryName;
            
    function getRatingGroupId() {
        return $this->ratingGroupId;
    }

    function getRatingGroupName() {
        return $this->ratingGroupName;
    }

    function setRatingGroupId($ratingGroupId) {
        $this->ratingGroupId = $ratingGroupId;
    }

    function setRatingGroupName($ratingGroupName) {
        $this->ratingGroupName = $ratingGroupName;
    }
    
    function getOrder() {
        return $this->order;
    }

    function setOrder($order) {
        $this->order = $order;
    }

    function getCode() {
        return $this->code;
    }

    function setCode($code) {
        $this->code = $code;
    }
    
    function getListRatingGroupId() {
        return $this->listRatingGroupId;
    }

    function setListRatingGroupId($listRatingGroupId) {
        $this->listRatingGroupId = $listRatingGroupId;
    }

    function getRecensyName() {
        return $this->recensyName;
    }

    function getFrequencyName() {
        return $this->frequencyName;
    }

    function getMonetaryName() {
        return $this->monetaryName;
    }

    function setRecensyName($recensyName) {
        $this->recensyName = $recensyName;
    }

    function setFrequencyName($frequencyName) {
        $this->frequencyName = $frequencyName;
    }

    function setMonetaryName($monetaryName) {
        $this->monetaryName = $monetaryName;
    }


     
}

