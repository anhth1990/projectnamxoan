<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class LevelUserForm extends BaseForm{
    private $code;
    private $images;
    
    function getCode() {
        return $this->code;
    }

    function getImages() {
        return $this->images;
    }

    function setCode($code) {
        $this->code = $code;
    }

    function setImages($images) {
        $this->images = $images;
    }


}

