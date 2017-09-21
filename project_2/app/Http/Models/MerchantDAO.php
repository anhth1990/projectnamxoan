<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Forms\MerchantForm;
use Exception;

class MerchantDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_merchant");
    }
    
    public function checkExistEmail($email){
        try {
            $data = MerchantDAO::select('*');
            $data = $data->where('email', $email);
            $data = $data->first();
            if($data==null)
                return true;
            else
                return false;
        } catch (Exception $ex) {
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function checkExistMobile($mobile){
        try {
            $data = MerchantDAO::select('*');
            $data = $data->where('mobile', $mobile);
            $data = $data->first();
            if($data==null)
                return true;
            else
                return false;
        } catch (Exception $ex) {
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function getList(MerchantForm $searchForm){
        try {
            $data = MerchantDAO::select('*');
            if($searchForm->getName()!=null){
                $data = $data->where('name', 'like', '%' . $searchForm->getName() . '%');
            }
            if($searchForm->getEmail()!=null){
                $data = $data->where('email', 'like', '%' . $searchForm->getEmail() . '%');
            }
            if($searchForm->getMobile()!=null){
                $data = $data->where('mobile', 'like', '%' . $searchForm->getMobile() . '%');
            }
            if($searchForm->getStatus()!=null){
                $data = $data->where('status',  $searchForm->getStatus());
            }
            $data = $data->where('name', 'like', '%' . $searchForm->getName() . '%');
            $data = $data->orderBy('id', 'desc');
            if($searchForm->getPageSize()!=null)
                $data = $data->paginate($searchForm->getPageSize());
            else
                $data = $data->get();
            return $data;
        } catch (Exception $ex) {
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function checkLogin($email,$password){
        try {
            $data = MerchantDAO::select('*');
            $data = $data->where('email',  $email);
            $data = $data->where('password',  md5($password));
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
    
    /*
     * get fist data
     */
    public function getFirstData(MerchantForm $searchForm){
        try {
            $data = MerchantDAO::select('*');
            if($searchForm->getEmail() != null){
                $data = $data->where('email', $searchForm->getEmail());
            }
            if($searchForm->getPassword() != null){
                $data = $data->where('password', md5($searchForm->getPassword()));
            }
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }
    
    /*
     * search list data
     */
    public function searchListData(MerchantForm $searchForm){
        try {
            $data = MerchantDAO::select('*');
            if($searchForm->getEmail() != null){
                $data = $data->where('email', $searchForm->getEmail());
            }
            if($searchForm->getPassword() != null){
                $data = $data->where('password', md5($searchForm->getPassword()));
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

}

?>