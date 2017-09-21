<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class RatingTypeForm extends BaseForm{
    private $code;
    private $period;
    private $periodUnit;
    private $unit;
    private $unitStart;
    private $unitEnd;
            
    function getCode() {
        return $this->code;
    }

    function getPeriod() {
        return $this->period;
    }

    function getUnit() {
        return $this->unit;
    }

    function getUnitStart() {
        return $this->unitStart;
    }

    function getUnitEnd() {
        return $this->unitEnd;
    }

    function setCode($code) {
        $this->code = $code;
    }

    function setPeriod($period) {
        $this->period = $period;
    }

    function setUnit($unit) {
        $this->unit = $unit;
    }

    function setUnitStart($unitStart) {
        $this->unitStart = $unitStart;
    }

    function setUnitEnd($unitEnd) {
        $this->unitEnd = $unitEnd;
    }

    function getPeriodUnit() {
        return $this->periodUnit;
    }

    function setPeriodUnit($periodUnit) {
        $this->periodUnit = $periodUnit;
    }


     
}

