<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\UserAdminForm;
use App\Http\Models\UserAdminDAO;
use Session;
class UserAdminService extends BaseService {
    public function __construct() {
        $this->userAdminDao = new UserAdminDAO();
    }
    
    public function checkLogin($username ,$password){
        return $this->userAdminDao->checkLogin($username,$password);
    }
    
    /*
     * Login
     */
    public function login(UserAdminForm $userAdminForm){
        $userAdmin = new UserAdminDAO();
        $userAdmin = $this->userAdminDao->checkLogin($userAdminForm->getUsername(),$userAdminForm->getPassword());
        /*
         * Bản ghi hợp lệ
         */
        if($userAdmin!=null){
            // set last login
            $userAdmin->lastLogin = date(env('DATE_FORMAT_Y_M_D'));
            $this->userAdminDao->saveResultId($userAdmin);
            // set session
            $userAdminForm = new UserAdminForm();
            $userAdminForm->setId($userAdmin->id);
            $userAdminForm->setEmail($userAdmin->email);
            $userAdminForm->setMobile($userAdmin->mobile);
            $userAdminForm->setLastLogin($userAdmin->lastLogin);
            $userAdminForm->setStatus($userAdmin->status);
            $userAdminForm->setRole($userAdmin->role);
            $userAdminForm->setFirstName($userAdmin->firstName);
            $userAdminForm->setLastName($userAdmin->lastName);
            /*
             * set session
             */
            Session::put("login_admin_portal", $userAdminForm);
        }
        return $userAdmin;
    }
    
}

