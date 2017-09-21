<?php

namespace App\Http\AdminPortal\Validates;

use App\Http\Forms\UserAdminForm;
use App\Http\Services\UserAdminService;
use App\Http\Validates\BaseValidate;
use Exception;

class AuthValidate extends BaseValidate  {
    
    public function __construct() {
        $this->userAdminService = new UserAdminService();
    }

    public function validateLogin(UserAdminForm $validateForm){
        /*
         * validate form
         */
        $error = null;
        if($validateForm->getUsername()==null || $validateForm->getUsername()==""){
            $error = trans('error.username_required');
        }else if($validateForm->getPassword()==null || $validateForm->getPassword()==""){
            $error = trans('error.password_required');
        }else if(!$this->userAdminService->checkLogin($validateForm->getUsername(),$validateForm->getPassword())){
            $error = trans('error.username_or_password_incorrect');
        }
        return $error;
    }

}
