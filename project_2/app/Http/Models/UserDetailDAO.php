<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\UserDetailForm;

class UserDetailDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_users_detail");
    }
    
    public function searchDataFirst(UserDetailForm $searchForm){
        try {
            $data = UserDetailDAO::select('*');
            if($searchForm->getType() != null){
                $data = $data->where('type', $searchForm->getType());
            }
            if($searchForm->getUserId() != null){
                $data = $data->where('userId', $searchForm->getUserId());
            }
            if($searchForm->getMerchantId() != null){
                $data = $data->where('merchantId', $searchForm->getMerchantId());
            }
            if($searchForm->getStatus() != null){
                $data = $data->where('status', $searchForm->getStatus());
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
    
    public function searchListData(UserDetailForm $searchForm){
        try {
            $data = UserDetailDAO::select('*');
            if($searchForm->getType() != null){
                $data = $data->where('type', $searchForm->getType());
            }
            if($searchForm->getUserId() != null){
                $data = $data->where('userId', $searchForm->getUserId());
            }
            if($searchForm->getMerchantId() != null){
                $data = $data->where('merchantId', $searchForm->getMerchantId());
            }
            if($searchForm->getStatus() != null){
                $data = $data->where('status', $searchForm->getStatus());
            }
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
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

    public function checkExistEmail($email){
        try {
            $data = UserDetailDAO::select('*');
            $data = $data->where('email', $email);
            $data = $data->get();
            if(count($data)>0)
                return $data[0];
            else
                return null;
        } catch (Exception $ex) {
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function checkExistMobile($mobile){
        try {
            $data = UserDetailDAO::select('*');
            $data = $data->where('mobile', $mobile);
            $data = $data->get();
            if(count($data)>0)
                return $data[0];
            else
                return null;
        } catch (Exception $ex) {
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function checkExistEmailInMerchant($email,$merchantId){
        try {
            $data = UserDetailDAO::select('*');
            $data = $data->where('email', $email);
            $data = $data->where('merchantId', $merchantId);
            $data = $data->get();
            if(count($data)>0)
                return $data[0];
            else
                return null;
        } catch (Exception $ex) {
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function checkExistMobileInMerchant($mobile,$merchantId){
        try {
            $data = UserDetailDAO::select('*');
            $data = $data->where('mobile', $mobile);
            $data = $data->where('merchantId', $merchantId);
            $data = $data->get();
            if(count($data)>0)
                return $data[0];
            else
                return null;
        } catch (Exception $ex) {
            throw new Exception(trans('error.error_system'));
        }
    }
    
    
    
    public function checkExistMobileInPartnerMerchant($mobile,$merchantId,$partnerId){
        try {
            $data = UserDetailDAO::select('*');
            $data = $data->where('mobile', $mobile);
            $data = $data->where('merchantId', $merchantId);
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function getListUser(UserDetailForm $userDetailForm){
        try {
            $data = DB::table('tb_users_detail')
                    ->select('tb_users_detail.*','tb_users.username as username')
            ->leftJoin('tb_users', 'tb_users_detail.userId', '=', 'tb_users.id');
            
            if($userDetailForm->getMerchantId()!=null){
                $data->where('tb_users_detail.merchantId', $userDetailForm->getMerchantId());
            }
            $data = $data->where('tb_users.status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->orderBy('tb_users_detail.id', 'desc');
            if($userDetailForm->getPageSize()!=null)
                $data = $data->paginate($userDetailForm->getPageSize());
            else
                $data = $data->get();
            
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("function name getListUser() UserDetailDAO -- ".$ex->getMessage());
            throw new Exception(trans("error.error_system"));
        }
    }
    
    public function checkUserDetail(UserDetailForm $userDetailForm){
        try {
            $data = UserDetailDAO::select('*');
            $data = $data->where('userId',  $userDetailForm->getUserId());
            $data = $data->where('merchantId',  $userDetailForm->getMerchantId());
            $data = $data->where('status', $userDetailForm->getStatus());
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
    
    public function getUserDetailByUsername($username , $merchantId){
        try {
            $data = DB::table('tb_users_detail')
                    ->select('tb_users_detail.*','tb_users.username as username')
            ->leftJoin('tb_users', 'tb_users_detail.userId', '=', 'tb_users.id');
            $data->where('tb_users.username', $username);
            $data->where('tb_users_detail.merchantId', $merchantId);
            $data = $data->where('tb_users.status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->orderBy('tb_users_detail.id', 'desc');
            
            return $data->first();
        } catch (Exception $ex) {
            $this->logs_custom("function name getListUser() UserDetailDAO -- ".$ex->getMessage());
            throw new Exception(trans("error.error_system"));
        }
    }
    
}

?>