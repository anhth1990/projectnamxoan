<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;

class ProIdentityForm extends BaseForm {

    private $indentity;
    private $name;
    private $lastLogin;
    private $orderName;
    private $orderIDOnline;
    private $ip;
            
    function getIndentity() {
        return $this->indentity;
    }

    function setIndentity($indentity) {
        $this->indentity = $indentity;
    }
    
    function getName() {
        return $this->name;
    }

    function setName($name) {
        $this->name = $name;
    }

    function getLastLogin() {
        return $this->lastLogin;
    }

    function setLastLogin($lastLogin) {
        $this->lastLogin = $lastLogin;
    }
    
    function getOrderName() {
        return $this->orderName;
    }

    function getOrderIDOnline() {
        return $this->orderIDOnline;
    }

    function setOrderName($orderName) {
        $this->orderName = $orderName;
    }

    function setOrderIDOnline($orderIDOnline) {
        $this->orderIDOnline = $orderIDOnline;
    }
    
    function getIp() {
        return $this->ip;
    }

    function setIp($ip) {
        $this->ip = $ip;
    }


}
