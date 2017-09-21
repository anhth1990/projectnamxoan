<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class ActiveCodeForm extends BaseForm{
    private $type;
    private $idRef;
    private $activeCode;
    private $createdDate;
    
    function getType() {
        return $this->type;
    }

    function getIdRef() {
        return $this->idRef;
    }

    function getActiveCode() {
        return $this->activeCode;
    }

    function getCreatedDate() {
        return $this->createdDate;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setIdRef($idRef) {
        $this->idRef = $idRef;
    }

    function setActiveCode($activeCode) {
        $this->activeCode = $activeCode;
    }

    function setCreatedDate($createdDate) {
        $this->createdDate = $createdDate;
    }


            
}

