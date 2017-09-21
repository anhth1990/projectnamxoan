<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class UserDetailForm extends BaseForm{
    private $userId;
    private $merchantId;
    private $firstName;
    private $lastName;
    private $address;
    private $lat;
    private $long;
    private $provinceId;
    private $districtId;
    private $wardId;
    private $email;
    private $mobile;
    private $status;
    private $type;
    private $dateOfBirth;
    private $gender;
            
    function getUserId() {
        return $this->userId;
    }

    function getMerchantId() {
        return $this->merchantId;
    }

    function getFirstName() {
        return $this->firstName;
    }

    function getLastName() {
        return $this->lastName;
    }

    function getAddress() {
        return $this->address;
    }

    function getLat() {
        return $this->lat;
    }

    function getLong() {
        return $this->long;
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
    
    function getEmail() {
        return $this->email;
    }

    function getMobile() {
        return $this->mobile;
    }

    function setUserId($userId) {
        $this->userId = $userId;
    }

    function setMerchantId($merchantId) {
        $this->merchantId = $merchantId;
    }

    function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    function setAddress($address) {
        $this->address = $address;
    }

    function setLat($lat) {
        $this->lat = $lat;
    }

    function setLong($long) {
        $this->long = $long;
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

    function setEmail($email) {
        $this->email = $email;
    }

    function setMobile($mobile) {
        $this->mobile = $mobile;
    }
    
    function getStatus() {
        return $this->status;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function getType() {
        return $this->type;
    }

    function setType($type) {
        $this->type = $type;
    }
    
    function getDateOfBirth() {
        return $this->dateOfBirth;
    }

    function setDateOfBirth($dateOfBirth) {
        $this->dateOfBirth = $dateOfBirth;
    }

    function getGender() {
        return $this->gender;
    }

    function setGender($gender) {
        $this->gender = $gender;
    }


            
}

