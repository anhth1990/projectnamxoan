<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class ServiceForm extends BaseForm{
    private $name;
    private $price;
    private $quantity;
    private $category;
    
    function getName() {
        return $this->name;
    }

    function getPrice() {
        return $this->price;
    }

    function getQuantity() {
        return $this->quantity;
    }

    function getCategory() {
        return $this->category;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setPrice($price) {
        $this->price = $price;
    }

    function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    function setCategory($category) {
        $this->category = $category;
    }
            
}

