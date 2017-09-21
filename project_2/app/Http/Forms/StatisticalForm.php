<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class StatisticalForm extends BaseForm{
    private $fromDate;
    private $toDate;
    private $timeSlot;
    private $store;
            
    function getFromDate() {
        return $this->fromDate;
    }

    function getToDate() {
        return $this->toDate;
    }

    function setFromDate($fromDate) {
        $this->fromDate = $fromDate;
    }

    function setToDate($toDate) {
        $this->toDate = $toDate;
    }

    function getTimeSlot() {
        return $this->timeSlot;
    }

    function setTimeSlot($timeSlot) {
        $this->timeSlot = $timeSlot;
    }
    
    function getStore() {
        return $this->store;
    }

    function setStore($store) {
        $this->store = $store;
    }


            
}

