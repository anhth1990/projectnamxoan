<?php

/* 
 * anhth1990 
 */
namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class HistoryActionForm extends BaseForm{
    private $beforeValue;
    private $afterValue;
    private $type;
    private $idRef;
    
    function getBeforeValue() {
        return $this->beforeValue;
    }

    function getAfterValue() {
        return $this->afterValue;
    }

    function getType() {
        return $this->type;
    }

    function getIdRef() {
        return $this->idRef;
    }

    function setBeforeValue($beforeValue) {
        $this->beforeValue = $beforeValue;
    }

    function setAfterValue($afterValue) {
        $this->afterValue = $afterValue;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setIdRef($idRef) {
        $this->idRef = $idRef;
    }


}
