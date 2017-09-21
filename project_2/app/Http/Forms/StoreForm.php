<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class StoreForm extends BaseForm{
    private $email;
    private $mobile;
    private $name;
    private $address;
    private $provinceId;
    private $districtId;
    private $wardId;
    private $lat;
    private $long;
    private $images;
    private $provinceName;
    private $districtName;
    private $wardName;
    private $logo;
    private $openTime;
    private $closeTime;
    private $nameBranch;
    private $dataFileLogo;
    private $dataFileImages;
    private $linkLogo;
    private $linkImages;
    private $storeId;
    private $storeBranchId;
    private $categoryId;
    private $storeHashcode;
    private $description;
            
    function getEmail() {
        return $this->email;
    }

    function getMobile() {
        return $this->mobile;
    }

    function getName() {
        return $this->name;
    }

    function getAddress() {
        return $this->address;
    }

    function getProvinceId() {
        return $this->provinceId;
    }

    function getDistrictId() {
        return $this->districtId;
    }

    function getWardId() {
        return $this->wardId;
    }

    function getLat() {
        return $this->lat;
    }

    function getLong() {
        return $this->long;
    }

    function getImages() {
        return $this->images;
    }

    function getProvinceName() {
        return $this->provinceName;
    }

    function getDistrictName() {
        return $this->districtName;
    }

    function getWardName() {
        return $this->wardName;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setMobile($mobile) {
        $this->mobile = $mobile;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setAddress($address) {
        $this->address = $address;
    }

    function setProvinceId($provinceId) {
        $this->provinceId = $provinceId;
    }

    function setDistrictId($districtId) {
        $this->districtId = $districtId;
    }

    function setWardId($wardId) {
        $this->wardId = $wardId;
    }

    function setLat($lat) {
        $this->lat = $lat;
    }

    function setLong($long) {
        $this->long = $long;
    }

    function setImages($images) {
        $this->images = $images;
    }

    function setProvinceName($provinceName) {
        $this->provinceName = $provinceName;
    }

    function setDistrictName($districtName) {
        $this->districtName = $districtName;
    }

    function setWardName($wardName) {
        $this->wardName = $wardName;
    }

    function getLogo() {
        return $this->logo;
    }

    function getOpenTime() {
        return $this->openTime;
    }

    function getCloseTime() {
        return $this->closeTime;
    }

    function setLogo($logo) {
        $this->logo = $logo;
    }

    function setOpenTime($openTime) {
        $this->openTime = $openTime;
    }

    function setCloseTime($closeTime) {
        $this->closeTime = $closeTime;
    }
      
    function getNameBranch() {
        return $this->nameBranch;
    }

    function setNameBranch($nameBranch) {
        $this->nameBranch = $nameBranch;
    }
    
    function getDataFileLogo() {
        return $this->dataFileLogo;
    }

    function getDataFileImages() {
        return $this->dataFileImages;
    }

    function setDataFileLogo($dataFileLogo) {
        $this->dataFileLogo = $dataFileLogo;
    }

    function setDataFileImages($dataFileImages) {
        $this->dataFileImages = $dataFileImages;
    }
    
    function getLinkLogo() {
        return $this->linkLogo;
    }

    function getLinkImages() {
        return $this->linkImages;
    }

    function setLinkLogo($linkLogo) {
        $this->linkLogo = $linkLogo;
    }

    function setLinkImages($linkImages) {
        $this->linkImages = $linkImages;
    }

    function getStoreId() {
        return $this->storeId;
    }

    function setStoreId($storeId) {
        $this->storeId = $storeId;
    }

    function getStoreBranchId() {
        return $this->storeBranchId;
    }

    function setStoreBranchId($storeBranchId) {
        $this->storeBranchId = $storeBranchId;
    }
    
    function getCategoryId() {
        return $this->categoryId;
    }

    function setCategoryId($categoryId) {
        $this->categoryId = $categoryId;
    }

    function getStoreHashcode() {
        return $this->storeHashcode;
    }

    function setStoreHashcode($storeHashcode) {
        $this->storeHashcode = $storeHashcode;
    }

    function getDescription() {
        return $this->description;
    }

    function setDescription($description) {
        $this->description = $description;
    }

}

