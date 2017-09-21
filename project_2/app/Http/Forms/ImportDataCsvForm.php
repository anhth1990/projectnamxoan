<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class ImportDataCsvForm extends BaseForm{
    private $email;
    private $mobile;
    private $hashcodeMerchant;
    private $hashcodePartner;
    private $idRef;
    private $amount;
    private $createdTime;
    private $address;
    private $fullname;
            
    function getEmail() {
        return $this->email;
    }

    function getMobile() {
        return $this->mobile;
    }

    function getHashcodeMerchant() {
        return $this->hashcodeMerchant;
    }

    function getHashcodePartner() {
        return $this->hashcodePartner;
    }

    function getIdRef() {
        return $this->idRef;
    }

    function getAmount() {
        return $this->amount;
    }

    function getCreatedTime() {
        return $this->createdTime;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setMobile($mobile) {
        $this->mobile = $mobile;
    }

    function setHashcodeMerchant($hashcodeMerchant) {
        $this->hashcodeMerchant = $hashcodeMerchant;
    }

    function setHashcodePartner($hashcodePartner) {
        $this->hashcodePartner = $hashcodePartner;
    }

    function setIdRef($idRef) {
        $this->idRef = $idRef;
    }

    function setAmount($amount) {
        $this->amount = $amount;
    }

    function setCreatedTime($createdTime) {
        $this->createdTime = $createdTime;
    }
    
    function getAddress() {
        return $this->address;
    }

    function setAddress($address) {
        $this->address = $address;
    }

    function getFullname() {
        return $this->fullname;
    }

    function setFullname($fullname) {
        $this->fullname = $fullname;
    }


            
}

