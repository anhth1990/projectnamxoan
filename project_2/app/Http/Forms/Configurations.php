<?php
namespace App\Http\Forms;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Configurations{
    public $titlePageAdminPortal;
    public $titlePageAdminMerchant;


    public function __construct() {
        $this->titlePageAdminPortal = trans('common.title_page_admin_portal').' | ';
        $this->titlePageAdminMerchant = trans('common.title_page_admin_merchant').' | ';
    }
    
}

