<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class PartnerForm extends BaseForm{
    private $type;
    private $name;
    private $email;
    private $mobile;
    private $address;
    private $code;
    
    function getType() {
        return $this->type;
    }

    function getName() {
        return $this->name;
    }

    function getEmail() {
        return $this->email;
    }

    function getMobile() {
        return $this->mobile;
    }

    function getAddress() {
        return $this->address;
    }

    function getCode() {
        return $this->code;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setMobile($mobile) {
        $this->mobile = $mobile;
    }

    function setAddress($address) {
        $this->address = $address;
    }

    function setCode($code) {
        $this->code = $code;
    }


    
            
}

