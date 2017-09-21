<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class AccountForm extends BaseForm{
    private $noAccount;
    private $balance;
    private $deduct;
    private $plus;
    private $type;
    private $idRef;
    private $group;
            
    function getNoAccount() {
        return $this->noAccount;
    }

    function getBalance() {
        return $this->balance;
    }

    function getDeduct() {
        return $this->deduct;
    }

    function getPlus() {
        return $this->plus;
    }

    function getType() {
        return $this->type;
    }

    function setNoAccount($noAccount) {
        $this->noAccount = $noAccount;
    }

    function setBalance($balance) {
        $this->balance = $balance;
    }

    function setDeduct($deduct) {
        $this->deduct = $deduct;
    }

    function setPlus($plus) {
        $this->plus = $plus;
    }

    function setType($type) {
        $this->type = $type;
    }

    function getIdRef() {
        return $this->idRef;
    }

    function setIdRef($idRef) {
        $this->idRef = $idRef;
    }

    function getGroup() {
        return $this->group;
    }

    function setGroup($group) {
        $this->group = $group;
    }


            
}

