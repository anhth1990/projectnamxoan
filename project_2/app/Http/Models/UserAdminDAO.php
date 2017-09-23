<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Forms\UserAdminForm;

class UserAdminDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_user_admin");
    }
    
    public function checkLogin($username,$password){
        try {
            $data = UserAdminDAO::select('*');
            $data = $data->where('username', $username);
            $data = $data->where('password', md5($password));
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function getDataByUsername($username){
        try {
            $data = UserAdminDAO::select('*');
            $data = $data->where('username', $username);
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function getList(UserAdminForm $searchForm){
        try {
            $data = UserAdminDAO::select('*');
            if($searchForm->getUsername()!=null){
                $data = $data->where('username', 'like' ,'%'.$searchForm->getUsername().'%');
            }
            if($searchForm->getEmail()!=null){
                $data = $data->where('email', 'like' ,'%'.$searchForm->getEmail().'%');
            }
            if($searchForm->getMobile()!=null){
                $data = $data->where('mobile', 'like' ,'%'.$searchForm->getMobile().'%');
            }
            if($searchForm->getRole()!=null){
                $data = $data->where('role', $searchForm->getRole());
            }
            if($searchForm->getStatus()!=null){
                $data = $data->where('status', $searchForm->getStatus());
            }
            
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->orderBy('id', 'desc');
            if($searchForm->getPageSize()!=null)
                $data = $data->paginate($searchForm->getPageSize(), ['*'], 'page', $searchForm->getPageIndex());
            else
                $data = $data->get();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }

}

?>