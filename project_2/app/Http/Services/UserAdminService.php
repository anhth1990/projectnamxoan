<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\UserAdminForm;
use App\Http\Models\UserAdminDAO;
use Session;
class UserAdminService extends BaseService {
    private $userAdminDao;
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
            $userAdminForm->setHashcode($userAdmin->hashcode);
            /*
             * set session
             */
            Session::put("login_admin_portal", $userAdminForm);
        }
        return $userAdmin;
    }
    
    public function getDataByUsername($username){
        return $this->userAdminDao->getDataByUsername($username);
    }
    
    public function insert(UserAdminForm $addForm){
        $obj = new UserAdminDAO();
        $obj->username = $addForm->getUsername();
        $obj->password = md5($addForm->getPassword());
        $obj->firstName = $addForm->getFirstName();
        $obj->lastName = $addForm->getLastName();
        $obj->email = $addForm->getEmail();
        $obj->mobile = $addForm->getMobile();
        $obj->role = $addForm->getRole();
        $obj->status = $addForm->getStatus();
        return $this->userAdminDao->saveResultId($obj);
        
    }
    
    public function update(UserAdminForm $editForm){
        if($editForm->getHashcode()!=null){
            $obj = $this->userAdminDao->findByHashcode($editForm->getHashcode());
            if($obj!=null){
                $obj->username = $editForm->getUsername();
                $obj->firstName = $editForm->getFirstName();
                $obj->lastName = $editForm->getLastName();
                $obj->email = $editForm->getEmail();
                $obj->mobile = $editForm->getMobile();
                $obj->role = $editForm->getRole();
                $obj->status = $editForm->getStatus();
                return $this->userAdminDao->saveResultId($obj);
            }
            return null;
        }
        return null;
        
    }
    
    public function searchListData(UserAdminForm $searchForm){
        return $this->userAdminDao->getList($searchForm);
    }
    
    public function countList(UserAdminForm $searchForm){
        $searchForm->setPageSize(null);
        return count($this->userAdminDao->getList($searchForm));
    }
    
    public function getDataByHashcode($hashcode){
        return $this->userAdminDao->findByHashcode($hashcode);
    }
    
    public function updatePassword(UserAdminForm $editForm){
        if($editForm->getHashcode()!=null){
            $obj = $this->userAdminDao->findByHashcode($editForm->getHashcode());
            if($obj!=null){
                $obj->password = md5($editForm->getNewPassword());
                return $this->userAdminDao->saveResultId($obj);
            }
            return null;
        }
        return null;
    }
    
}

