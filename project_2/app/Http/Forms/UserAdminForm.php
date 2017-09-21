<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class UserAdminForm extends BaseForm{
    private $username;
    private $password;
    private $role;
    private $saveLogin;
    private $firstName;
    private $lastName;
    private $fullName;
    private $lastLogin;
    private $email;
    private $mobile;
            
    function getUsername() {
        return $this->username;
    }

    function getPassword() {
        return $this->password;
    }

    function getRole() {
        return $this->role;
    }

    function getSaveLogin() {
        return $this->saveLogin;
    }

    function getFirstName() {
        return $this->firstName;
    }

    function getLastName() {
        return $this->lastName;
    }

    function getFullName() {
        return $this->firstName.' '.$this->lastName;
    }

    function setUsername($username) {
        $this->username = $username;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setRole($role) {
        $this->role = $role;
    }

    function setSaveLogin($saveLogin) {
        $this->saveLogin = $saveLogin;
    }

    function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    function setFullName($fullName) {
        $this->fullName = $fullName;
    }
    
    function getLastLogin() {
        return $this->lastLogin;
    }

    function setLastLogin($lastLogin) {
        $this->lastLogin = $lastLogin;
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


    

}

