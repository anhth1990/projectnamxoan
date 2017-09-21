<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Http\AdminPortal\Controllers;
use App\Http\AdminPortal\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Forms\UserAdminForm;
use App\Http\AdminPortal\Validates\AuthValidate;
use Session;
use App\Http\Services\UserAdminService;

class AuthController extends BaseController {
    
    public function __construct() {
        $this->authValidate = new AuthValidate();
        $this->userAdminService = new UserAdminService();
    }

    /*
     * Login
     */
    public function getLogin(){
        if (Session::has("login_admin_portal")){
            return redirect("/" . env('PREFIX_ADMIN_PORTAL'));
        }      
        $userAdminForm = new UserAdminForm();
        return view('AdminPortal.Auth.login',  compact('userAdminForm'));
    }
    
    public function postLogin(Request $request){
        $userAdminForm = new UserAdminForm();
        $userAdminForm = $this->copyUserAdmin($request);
        $validate = $this->authValidate->validateLogin($userAdminForm);
        if($validate!=null){
            $error = $validate;
            return view('AdminPortal.Auth.login',  compact('userAdminForm','error'));
        }else{
            $this->userAdminService->login($userAdminForm);
            return redirect("/" . env('PREFIX_ADMIN_PORTAL'));
        }
    }
    
    /*
     * logout
     */
    public function getLogout(){
            Session::forget('login_admin_portal');
            return redirect('/' . env('PREFIX_ADMIN_PORTAL'));
    }
    
    public function copyUserAdmin(Request $request){
        $userAdminForm = new UserAdminForm();
        $data = $request->input();
        $keyStr = "username,password,saveLogin";
        $dataFormat = $this->getInput($data, $keyStr,1);
        $userAdminForm->setUsername($dataFormat["username"]);
        $userAdminForm->setPassword($dataFormat["password"]);
        if(isset($dataFormat["saveLogin"]))
            $userAdminForm->setSaveLogin($dataFormat["saveLogin"]);
        return $userAdminForm;
    }
    
}

