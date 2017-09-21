<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Http\FrontEnd\Controllers;
use App\Http\FrontEnd\Controllers\BaseController;
use Illuminate\Http\Request;
use Session;
use Exception;
use App\Http\FrontEnd\Services\UserService;

class IndexController extends BaseController {
    private $userService;
    
    public function __construct() {
        parent::__construct();
        $this->userService = new UserService();
    }
    
    public function getIndex(){
        return view('FrontEnd.Index.index');
    }
    
    public function getActiveCode(Request $data){
        $request = $data->input();
        if(!isset($request['merchantId']) || !isset($request['activeCode']) || !isset($request['userId'])){
            echo "Không thực hiện dược";
            die();
        }
        $merchantHashcode = $request['merchantId'];
        $activeCode = $request['activeCode'];
        $userHashcode = $request['userId'];
        try {
            $this->userService->activeUserWeb($userHashcode, $merchantHashcode, $activeCode);
            echo "Kích hoạt tài khoản thành công";
        } catch (Exception $ex) {
            echo "Có lỗi xảy ra";
            $this->logs_custom("--Message:".$ex->getMessage()." --File : ".$ex->getFile()." --Line : ".$ex->getLine());
        }
    }
    
    
}

