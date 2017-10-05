<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;

class ProIdentityLogForm extends BaseForm {

    private $indentityId;
    private $ip;
    private $logTime;
            
    
    function getIndentityId() {
        return $this->indentityId;
    }

    function getIp() {
        return $this->ip;
    }

    function getLogTime() {
        return $this->logTime;
    }

    function setIndentityId($indentityId) {
        $this->indentityId = $indentityId;
    }

    function setIp($ip) {
        $this->ip = $ip;
    }

    function setLogTime($logTime) {
        $this->logTime = $logTime;
    }



}
