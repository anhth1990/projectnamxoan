<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class NewsForm extends BaseForm{
    private $name;
    private $images;
    private $linkImage;
    private $description;
            
    function getName() {
        return $this->name;
    }

    function getImages() {
        return $this->images;
    }

    function getLinkImage() {
        return $this->linkImage;
    }

    function getDescription() {
        return $this->description;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setImages($images) {
        $this->images = $images;
    }

    function setLinkImage($linkImage) {
        $this->linkImage = $linkImage;
    }

    function setDescription($description) {
        $this->description = $description;
    }


}

