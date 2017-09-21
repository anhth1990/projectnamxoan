<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class TransactionForm extends BaseForm{
    private $type;
    private $accFrom;
    private $accTo;
    private $amount;
    private $userDetailId;
    private $merchantId;
    private $createdTime;
    private $idRef;
    private $description;
    
    function getType() {
        return $this->type;
    }

    function getAccFrom() {
        return $this->accFrom;
    }

    function getAccTo() {
        return $this->accTo;
    }

    function getAmount() {
        return $this->amount;
    }

    function getUserDetailId() {
        return $this->userDetailId;
    }

    function getMerchantId() {
        return $this->merchantId;
    }

    function getCreatedTime() {
        return $this->createdTime;
    }

    function getIdRef() {
        return $this->idRef;
    }

    function getDescription() {
        return $this->description;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setAccFrom($accFrom) {
        $this->accFrom = $accFrom;
    }

    function setAccTo($accTo) {
        $this->accTo = $accTo;
    }

    function setAmount($amount) {
        $this->amount = $amount;
    }

    function setUserDetailId($userDetailId) {
        $this->userDetailId = $userDetailId;
    }

    function setMerchantId($merchantId) {
        $this->merchantId = $merchantId;
    }

    function setCreatedTime($createdTime) {
        $this->createdTime = $createdTime;
    }

    function setIdRef($idRef) {
        $this->idRef = $idRef;
    }

    function setDescription($description) {
        $this->description = $description;
    }
            
}

