<?php

/* 
 * anhth1990 
 */
namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class AnalysisForm extends BaseForm{
    private $code;
    private $minValue;
    private $maxValue;
    private $listRecensy;
    private $listFrequency;
    private $listMonetary;
    private $recensy;
    private $frequency;
    private $monetary;
    private $point;
            
    function getCode() {
        return $this->code;
    }

    function getMinValue() {
        return $this->minValue;
    }

    function getMaxValue() {
        return $this->maxValue;
    }

    function setCode($code) {
        $this->code = $code;
    }

    function setMinValue($minValue) {
        $this->minValue = $minValue;
    }

    function setMaxValue($maxValue) {
        $this->maxValue = $maxValue;
    }

    function getListRecensy() {
        return $this->listRecensy;
    }

    function getListFrequency() {
        return $this->listFrequency;
    }

    function getListMonetary() {
        return $this->listMonetary;
    }

    function setListRecensy($listRecensy) {
        $this->listRecensy = $listRecensy;
    }

    function setListFrequency($listFrequency) {
        $this->listFrequency = $listFrequency;
    }

    function setListMonetary($listMonetary) {
        $this->listMonetary = $listMonetary;
    }

    function getRecensy() {
        return $this->recensy;
    }

    function getFrequency() {
        return $this->frequency;
    }

    function getMonetary() {
        return $this->monetary;
    }

    function setRecensy($recensy) {
        $this->recensy = $recensy;
    }

    function setFrequency($frequency) {
        $this->frequency = $frequency;
    }

    function setMonetary($monetary) {
        $this->monetary = $monetary;
    }

    function getPoint() {
        return $this->point;
    }

    function setPoint($point) {
        $this->point = $point;
    }
}
