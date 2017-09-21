<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class UserInfoForm extends BaseForm{
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
    private $username;
    private $lastLogin;
    private $status;
    private $rencensyId;
    private $frequencyId;
    private $monetaryId;
    private $ratingName;
    private $rencensyName;
    private $frequencyName;
    private $monetaryName;
    private $ratingGroupName;
    private $fullname;
    private $ratingCode;
            
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

    function getUsername() {
        return $this->username;
    }

    function getLastLogin() {
        return $this->lastLogin;
    }

    function getStatus() {
        return $this->status;
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

    function setUsername($username) {
        $this->username = $username;
    }

    function setLastLogin($lastLogin) {
        $this->lastLogin = $lastLogin;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function getRencensyId() {
        return $this->rencensyId;
    }

    function getFrequencyId() {
        return $this->frequencyId;
    }

    function getMonetaryId() {
        return $this->monetaryId;
    }

    function setRencensyId($rencensyId) {
        $this->rencensyId = $rencensyId;
    }

    function setFrequencyId($frequencyId) {
        $this->frequencyId = $frequencyId;
    }

    function setMonetaryId($monetaryId) {
        $this->monetaryId = $monetaryId;
    }
    
    function getRatingName() {
        return $this->ratingName;
    }

    function setRatingName($ratingName) {
        $this->ratingName = $ratingName;
    }

    function getRencensyName() {
        return $this->rencensyName;
    }

    function getFrequencyName() {
        return $this->frequencyName;
    }

    function getMonetaryName() {
        return $this->monetaryName;
    }

    function setRencensyName($rencensyName) {
        $this->rencensyName = $rencensyName;
    }

    function setFrequencyName($frequencyName) {
        $this->frequencyName = $frequencyName;
    }

    function setMonetaryName($monetaryName) {
        $this->monetaryName = $monetaryName;
    }
    
    function getRatingGroupName() {
        return $this->ratingGroupName;
    }

    function setRatingGroupName($ratingGroupName) {
        $this->ratingGroupName = $ratingGroupName;
    }


    function getFullname() {
        return $this->firstName.' '.$this->lastName;
    }

    function setFullname($fullname) {
        $this->fullname = $fullname;
    }

    function getRatingCode() {
        return $this->ratingCode;
    }

    function setRatingCode($ratingCode) {
        $this->ratingCode = $ratingCode;
    }


            
}

