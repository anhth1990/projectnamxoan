<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class RatingLevelForm extends BaseForm{
    private $typeCode;
    private $typeName;
    private $name;
    private $minValue;
    private $maxValue;
    private $period;
    private $unitStart;
    private $unitEnd;
            
    function getTypeCode() {
        return $this->typeCode;
    }

    function getName() {
        return $this->name;
    }

    function getMinValue() {
        return $this->minValue;
    }

    function getMaxValue() {
        return $this->maxValue;
    }

    function setTypeCode($typeCode) {
        $this->typeCode = $typeCode;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setMinValue($minValue) {
        $this->minValue = $minValue;
    }

    function setMaxValue($maxValue) {
        $this->maxValue = $maxValue;
    }

    function getPeriod() {
        return $this->period;
    }

    function setPeriod($period) {
        $this->period = $period;
    }
    
    function getUnitStart() {
        return $this->unitStart;
    }

    function getUnitEnd() {
        return $this->unitEnd;
    }

    function setUnitStart($unitStart) {
        $this->unitStart = $unitStart;
    }

    function setUnitEnd($unitEnd) {
        $this->unitEnd = $unitEnd;
    }

    function getTypeName() {
        return $this->typeName;
    }

    function setTypeName($typeName) {
        $this->typeName = $typeName;
    }





     
}

