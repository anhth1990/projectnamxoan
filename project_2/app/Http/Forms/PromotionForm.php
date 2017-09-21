<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class PromotionForm extends BaseForm{
    private $name;
    private $images;
    private $linkImage;
    private $dateFrom;
    private $dateTo;
    private $type;
    private $idRef;
    private $description;
    private $storeId;
    private $branchId;
            
    function getName() {
        return $this->name;
    }

    function getImages() {
        return $this->images;
    }

    function getDateFrom() {
        return $this->dateFrom;
    }

    function getDateTo() {
        return $this->dateTo;
    }

    function getType() {
        return $this->type;
    }

    function getIdRef() {
        return $this->idRef;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setImages($images) {
        $this->images = $images;
    }

    function setDateFrom($dateFrom) {
        $this->dateFrom = $dateFrom;
    }

    function setDateTo($dateTo) {
        $this->dateTo = $dateTo;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setIdRef($idRef) {
        $this->idRef = $idRef;
    }
    
    function getDescription() {
        return $this->description;
    }

    function setDescription($description) {
        $this->description = $description;
    }
    
    function getLinkImage() {
        return $this->linkImage;
    }

    function setLinkImage($linkImage) {
        $this->linkImage = $linkImage;
    }

    function getStoreId() {
        return $this->storeId;
    }

    function getBranchId() {
        return $this->branchId;
    }

    function setStoreId($storeId) {
        $this->storeId = $storeId;
    }

    function setBranchId($branchId) {
        $this->branchId = $branchId;
    }



}

