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

}

?>