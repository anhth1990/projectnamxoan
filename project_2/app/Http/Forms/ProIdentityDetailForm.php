<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;

class ProIdentityDetailForm extends BaseForm {

    private $indentityId;
    private $type;
    private $time;
    private $url;
    private $code;

    function getIndentityId() {
        return $this->indentityId;
    }

    function getType() {
        return $this->type;
    }

    function getTime() {
        return $this->time;
    }

    function getUrl() {
        return $this->url;
    }

    function getCode() {
        return $this->code;
    }

    function setIndentityId($indentityId) {
        $this->indentityId = $indentityId;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setTime($time) {
        $this->time = $time;
    }

    function setUrl($url) {
        $this->url = $url;
    }

    function setCode($code) {
        $this->code = $code;
    }

}
