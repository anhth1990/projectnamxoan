<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class NotiEmailForm extends BaseForm{
    private $type;
    private $content;
    private $typeSend;
            
    function getType() {
        return $this->type;
    }

    function getContent() {
        return $this->content;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setContent($content) {
        $this->content = $content;
    }
    
    function getTypeSend() {
        return $this->typeSend;
    }

    function setTypeSend($typeSend) {
        $this->typeSend = $typeSend;
    }


            
}

