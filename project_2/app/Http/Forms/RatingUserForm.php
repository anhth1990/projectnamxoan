<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class RatingUserForm extends BaseForm{
    private $code;
    private $ratingLevel;
    private $order;
    private $listRatingLevelId;
    private $typeCode;
    private $minValue;
    private $maxValue;
    private $period;
    private $unit;
    private $name;
            
    function getCode() {
        return $this->code;
    }

    function getRatingLevel() {
        return $this->ratingLevel;
    }

    function getOrder() {
        return $this->order;
    }

    function setCode($code) {
        $this->code = $code;
    }

    function setRatingLevel($ratingLevel) {
        $this->ratingLevel = $ratingLevel;
    }

    function setOrder($order) {
        $this->order = $order;
    }

    function getListRatingLevelId() {
        return $this->listRatingLevelId;
    }

    function setListRatingLevelId($listRatingLevelId) {
        $this->listRatingLevelId = $listRatingLevelId;
    }

    function getTypeCode() {
        return $this->typeCode;
    }

    function setTypeCode($typeCode) {
        $this->typeCode = $typeCode;
    }
    
    function getMinValue() {
        return $this->minValue;
    }

    function getMaxValue() {
        return $this->maxValue;
    }

    function getPeriod() {
        return $this->period;
    }

    function getUnit() {
        return $this->unit;
    }

    function setMinValue($minValue) {
        $this->minValue = $minValue;
    }

    function setMaxValue($maxValue) {
        $this->maxValue = $maxValue;
    }

    function setPeriod($period) {
        $this->period = $period;
    }

    function setUnit($unit) {
        $this->unit = $unit;
    }

    function getName() {
        return $this->name;
    }

    function setName($name) {
        $this->name = $name;
    }



}

