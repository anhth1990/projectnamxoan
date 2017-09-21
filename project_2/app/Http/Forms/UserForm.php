<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class UserForm extends BaseForm{
    private $email;
    private $mobile;
    private $password;
    private $lastLogin;
    private $saveLogin;
    private $source;
    private $token;
    private $lastName;
    private $firstName;
    private $gender;
    private $provinceId;
    private $districtId;
    private $dateOfBirth;
    private $address;
            
    function getPassword() {
        return $this->password;
    }

    function getLastLogin() {
        return $this->lastLogin;
    }

    function getSaveLogin() {
        return $this->saveLogin;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setLastLogin($lastLogin) {
        $this->lastLogin = $lastLogin;
    }

    function setSaveLogin($saveLogin) {
        $this->saveLogin = $saveLogin;
    }

    function getEmail() {
        return $this->email;
    }

    function getMobile() {
        return $this->mobile;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setMobile($mobile) {
        $this->mobile = $mobile;
    }

    function getSource() {
        return $this->source;
    }

    function setSource($source) {
        $this->source = $source;
    }

    function getToken() {
        return $this->token;
    }

    function setToken($token) {
        $this->token = $token;
    }

    function getLastName() {
        return $this->lastName;
    }

    function getFirstName() {
        return $this->firstName;
    }

    function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    function getGender() {
        return $this->gender;
    }

    function getProvinceId() {
        return $this->provinceId;
    }

    function getDistrictId() {
        return $this->districtId;
    }

    function getDateOfBirth() {
        return $this->dateOfBirth;
    }

    function setGender($gender) {
        $this->gender = $gender;
    }

    function setProvinceId($provinceId) {
        $this->provinceId = $provinceId;
    }

    function setDistrictId($districtId) {
        $this->districtId = $districtId;
    }

    function setDateOfBirth($dateOfBirth) {
        $this->dateOfBirth = $dateOfBirth;
    }

    function getAddress() {
        return $this->address;
    }

    function setAddress($address) {
        $this->address = $address;
    }



            
}

