<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\UserForm;
use App\Http\Models\AnswerUserQuestionDAO;

class UserDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_users");
    }
    
    public function userQuestion()
    {
        return $this->hasMany(new AnswerUserQuestionDAO(),'userId');
    }
    
    public function searchData(UserForm $searchForm){
        try {
            $data = DB::table('tb_users')
                    ->select('tb_users.*','tb_users_detail.firstName as firstName','tb_users_detail.lastName as lastName','tb_account.balance as balance')
                    ->leftJoin('tb_users_detail', 'tb_users_detail.userId', '=', 'tb_users.id')
                    ->leftJoin('tb_account', 'tb_account.idRef', '=', 'tb_users_detail.id');
            $data = $data->where('tb_users_detail.type', 'DEFAULT');
            //$data = $data->where('tb_account.type', 'DEFAULT');
            if($searchForm->getMobile() != null){
                $data = $data->where('tb_users.mobile', $searchForm->getMobile());
            }
            if($searchForm->getEmail() != null){
                $data = $data->where('tb_users.email', $searchForm->getEmail());
            }
            $data = $data->where('tb_users.status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->orderBy('id', 'desc');
            if($searchForm->getPageSize() != null){
                $data = $data->paginate($searchForm->getPageSize());;
            }else{
                $data = $data->get();
            }
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }
    
    public function searchDataFirst(UserForm $searchForm){
        try {
            $data = UserDAO::select('*');
            if($searchForm->getMobile() != null){
                $data = $data->where('mobile', $searchForm->getMobile());
            }
            if($searchForm->getEmail() != null){
                $data = $data->where('email', $searchForm->getEmail());
            }
            if($searchForm->getPassword() != null){
                $data = $data->where('password', $searchForm->getPassword());
            }
            if($searchForm->getToken() != null){
                $data = $data->where('token', $searchForm->getToken());
            }
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->orderBy('id', 'desc');
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }

    public function checkExistUsername($username){
        try {
            $data = UserDAO::select('*');
            $data = $data->where('username', $username);
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom($ex->getMessage()." --File : ".$ex->getFile()." --Line : ".$ex->getLine());
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function getLoginByUser($username,$password){
        try {
            $data = UserDAO::select('*');
            $data = $data->where('username', $username);
            $data = $data->where('password', $password);
            $data = $data->where('status', '!=',env('COMMON_STATUS_DELETED'));
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom($ex->getMessage()." --File : ".$ex->getFile()." --Line : ".$ex->getLine());
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function getUserByToken($token){
        try {
            $data = UserDAO::select('*');
            $data = $data->where('token', $token);
            $data = $data->where('status', env('COMMON_STATUS_ACTIVE'));
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom($ex->getMessage()." --File : ".$ex->getFile()." --Line : ".$ex->getLine());
            throw new Exception(trans('error.error_system'));
        }
    }
}

?>