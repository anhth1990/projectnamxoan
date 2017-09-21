<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class MerchantForm extends BaseForm{
    private $email;
    private $mobile;
    private $firstName;
    private $lastName;
    private $name;
    private $address;
    private $provinceId;
    private $districtId;
    private $wardId;
    private $lat;
    private $long;
    private $lastLogin;
    private $images;
    private $fullName;
    private $password;
    private $provinceName;
    private $districtName;
    private $wardName;
    private $saveLogin;
    private $type;
    private $partnerDefault;
            
    function getEmail() {
        return $this->email;
    }

    function getMobile() {
        return $this->mobile;
    }

    function getFirstName() {
        return $this->firstName;
    }

    function getLastName() {
        return $this->lastName;
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

    function getLastLogin() {
        return $this->lastLogin;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setMobile($mobile) {
        $this->mobile = $mobile;
    }

    function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    function setLastName($lastName) {
        $this->lastName = $lastName;
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

    function setLastLogin($lastLogin) {
        $this->lastLogin = $lastLogin;
    }
    
    function getImages() {
        return $this->images;
    }

    function setImages($images) {
        $this->images = $images;
    }

    function getFullName() {
        return $this->firstName.' '.$this->lastName;
    }

    function setFullName($fullName) {
        $this->fullName = $fullName;
    }

    function getPassword() {
        return $this->password;
    }

    function setPassword($password) {
        $this->password = $password;
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

    function setProvinceName($provinceName) {
        $this->provinceName = $provinceName;
    }

    function setDistrictName($districtName) {
        $this->districtName = $districtName;
    }

    function setWardName($wardName) {
        $this->wardName = $wardName;
    }

    function getSaveLogin() {
        return $this->saveLogin;
    }

    function setSaveLogin($saveLogin) {
        $this->saveLogin = $saveLogin;
    }
    
    function getType() {
        return $this->type;
    }

    function setType($type) {
        $this->type = $type;
    }

    function getPartnerDefault() {
        return $this->partnerDefault;
    }

    function setPartnerDefault($partnerDefault) {
        $this->partnerDefault = $partnerDefault;
    }


            
}

