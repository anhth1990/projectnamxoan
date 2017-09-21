<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Http\AdminPortal\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Forms\Configurations;

class BaseController extends Controller {
    public function __construct() {
        $this->configuration = new Configurations();
    }
}

