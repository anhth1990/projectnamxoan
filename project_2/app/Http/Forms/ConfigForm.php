<?php

/* 
 * anhth1990 
 */
namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class ConfigForm extends BaseForm{
    private $key;
    private $value;
    private $rankUser;
    private $recensy;
    private $frequency;
    private $monetary;
            
    function getKey() {
        return $this->key;
    }

    function getValue() {
        return $this->value;
    }

    function setKey($key) {
        $this->key = $key;
    }

    function setValue($value) {
        $this->value = $value;
    }

    function getRankUser() {
        return $this->rankUser;
    }

    function setRankUser($rankUser) {
        $this->rankUser = $rankUser;
    }

    function getRecensy() {
        return $this->recensy;
    }

    function getFrequency() {
        return $this->frequency;
    }

    function getMonetary() {
        return $this->monetary;
    }

    function setRecensy($recensy) {
        $this->recensy = $recensy;
    }

    function setFrequency($frequency) {
        $this->frequency = $frequency;
    }

    function setMonetary($monetary) {
        $this->monetary = $monetary;
    }



}
