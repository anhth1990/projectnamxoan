<?php

/* 
 * anhth1990 
 */
namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class ExchangeRateForm extends BaseForm{
    private $type;
    private $rate;
    private $startDate;
    private $endDate;
    private $status;
    private $idRef;
            
    function getType() {
        return $this->type;
    }

    function getRate() {
        return $this->rate;
    }

    function getStartDate() {
        return $this->startDate;
    }

    function getEndDate() {
        return $this->endDate;
    }

    function getStatus() {
        return $this->status;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setRate($rate) {
        $this->rate = $rate;
    }

    function setStartDate($startDate) {
        $this->startDate = $startDate;
    }

    function setEndDate($endDate) {
        $this->endDate = $endDate;
    }

    function setStatus($status) {
        $this->status = $status;
    }
    
    function getIdRef() {
        return $this->idRef;
    }

    function setIdRef($idRef) {
        $this->idRef = $idRef;
    }


}
