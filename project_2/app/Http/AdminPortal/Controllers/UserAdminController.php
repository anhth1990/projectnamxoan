<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\AdminPortal\Controllers;

use App\Http\AdminPortal\Controllers\BaseController;
use Illuminate\Http\Request;
use Session;
use Exception;
use DB;
use App\Http\Forms\UserAdminForm;
use App\Http\Services\UserAdminService;

class UserAdminController extends BaseController {
    private $userAdminService;
    public function __construct() {
        parent::__construct();
        $this->userAdminService = new UserAdminService();
    }

    

    /*
     * list
     */

    public function getList() {
        try {
            $searchForm = new UserAdminForm();
            if (Session::has('SESSION_SEARCH_USERADMIN')) {
                $searchForm = Session::get('SESSION_SEARCH_USERADMIN');
            }
            // set page
            $page = 1;
            if (isset($_GET['page'])) {
                $page = intval($_GET['page']);
                if ($page == 0)
                    $page = 1;
            }
            $searchForm->setPageSize(env('PAGE_SIZE'));
            $listObj = $this->userAdminService->searchListData($searchForm);
            $countObj = $this->userAdminService->countList($searchForm);
            return view('AdminPortal.UserAdmin.list', compact('searchForm', 'page','listObj','countObj'));
        } catch (Exception $ex) {
            $this->logs_custom("\nController ****\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }

    public function postList(Request $data) {
        try {
            $response = $data->input();
            $searchForm = new UserAdminForm();
            $searchForm->setUsername(trim($response['username']));
            $searchForm->setEmail(trim($response['email']));
            $searchForm->setMobile(trim($response['mobile']));
            $searchForm->setRole($response['role']);
            $searchForm->setStatus($response['status']);
            Session::put('SESSION_SEARCH_USERADMIN', $searchForm);
            return redirect("/" . env('PREFIX_ADMIN_PORTAL') . "/user-admin");
        } catch (Exception $ex) {
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }

    public function getClearSearch() {
        try {
            Session::forget('SESSION_SEARCH_USERADMIN');
            return redirect("/" . env('PREFIX_ADMIN_PORTAL') . "/user-admin");
        } catch (Exception $ex) {
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }

    

    public function postDelete(Request $data) {
        DB::beginTransaction();
        try {
            if($this->adminSession->getRole()!='SUPER_ADMIN'){
                throw new Exception(trans('exception.PERMISSION_DENIED'));
            }
            $request = $data->input();
            $hashcode = $request['hashcode'];
            $identityForm = new ProIdentityDetailForm();
            $identityForm->setHashcode($hashcode);
            $identityForm->setStatus(env('COMMON_STATUS_DELETED'));
            $this->identityDetailService->update($identityForm);
            DB::commit();
            $response = array();
            $response['errCode'] = 200;
            $response['errMess'] = trans('common.action_success');
            return json_encode($response);
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            $response = array();
            $response['errCode'] = 100;
            $response['errMess'] = $error;
            return json_encode($response);
        }
    }

    public function getAdd() {
        try {
            if ($this->adminSession->getRole() != 'SUPER_ADMIN') {
                throw new Exception(trans('exception.PERMISSION_DENIED'));
            }
            $addForm = new UserAdminForm();
            return view('AdminPortal.UserAdmin.add', compact('addForm'));
        } catch (Exception $ex) {
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }

    public function postAddConfirm(Request $data) {
        DB::beginTransaction();
        try {
            $request = $data->input();
            $addForm = new UserAdminForm();
            $addForm->setUsername(trim($request['username']));
            $addForm->setPassword($request['password']);
            $addForm->setRePassword($request['rePassword']);
            $addForm->setFirstName(trim($request['firstName']));
            $addForm->setLastName(trim($request['lastName']));
            $addForm->setEmail(trim($request['email']));
            $addForm->setMobile(trim($request['mobile']));
            $addForm->setRole($request['role']);
            $addForm->setStatus($request['status']);
            $error = "";
            
            if($addForm->getUsername()==null){
                $error="Nhập tên đăng nhập";
            }else if($this->userAdminService->getDataByUsername($addForm->getUsername())!=null){
                $error="Tên đăng nhập đã tồn tại";
            }else if($addForm->getPassword()==null){
                $error="Nhập mật khẩu";
            }else if($addForm->getRePassword()==null){
                $error="Nhập xác nhận mật khẩu";
            }else if($addForm->getRePassword()!=$addForm->getPassword()){
                $error="Xác nhận mật khẩu không đúng";
            }
            if($error!=""){
                return view('AdminPortal.UserAdmin.add', compact('addForm','error'));
            }else{
                Session::put('SESSION_ADD_USER_ADMIN',$addForm);
                return view('AdminPortal.UserAdmin.add-confirm', compact('addForm'));
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }
    
    public function getAddFinish(Request $data) {
        DB::beginTransaction();
        try {
            $addForm = new UserAdminForm();
            if(Session::has("SESSION_ADD_USER_ADMIN")){
                $addForm = Session::get("SESSION_ADD_USER_ADMIN");
                $this->userAdminService->insert($addForm);
                Session::forget('SESSION_ADD_USER_ADMIN');
                DB::commit();
                return view('AdminPortal.UserAdmin.add-finish',  compact('addForm'));
            }else{
                return redirect('/' . env('PREFIX_ADMIN_PORTAL').'/user-admin/add');
            }
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }
    
    public function getEdit($hashcode) {
        try {
            if ($this->adminSession->getRole() != 'SUPER_ADMIN') {
                throw new Exception(trans('exception.PERMISSION_DENIED'));
            }
            $obj = $this->userAdminService->getDataByHashcode($hashcode);
            if($obj==null){
                throw new Exception(trans('exception.DATA_NOT_FOUND'));
            }
            $editForm = new UserAdminForm();
            $editForm->setUsername($obj->username);
            $editForm->setFirstName($obj->firstName);
            $editForm->setLastName($obj->lastName);
            $editForm->setEmail($obj->email);
            $editForm->setMobile($obj->mobile);
            $editForm->setRole($obj->role);
            $editForm->setStatus($obj->status);
            $editForm->setHashcode($obj->hashcode);
            return view('AdminPortal.UserAdmin.edit', compact('editForm'));
        } catch (Exception $ex) {
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }
    
    public function postEditConfirm(Request $data) {
        DB::beginTransaction();
        try {
            $request = $data->input();
            $editForm = new UserAdminForm();
            $editForm->setUsername(trim($request['username']));
            $editForm->setFirstName(trim($request['firstName']));
            $editForm->setLastName(trim($request['lastName']));
            $editForm->setEmail(trim($request['email']));
            $editForm->setMobile(trim($request['mobile']));
            $editForm->setRole($request['role']);
            $editForm->setStatus($request['status']);
            $editForm->setHashcode($request['hashcode']);
            $error = "";
            $obj =$this->userAdminService->getDataByUsername($editForm->getUsername());
            if($editForm->getUsername()==null){
                $error="Nhập tên đăng nhập";
            }else if($obj!=null && $obj->hashcode != $editForm->getHashcode()){
                $error="Tên đăng nhập đã tồn tại";
            }
            if($error!=""){
                return view('AdminPortal.UserAdmin.edit', compact('editForm','error'));
            }else{
                Session::put('SESSION_EDIT_USER_ADMIN',$editForm);
                return view('AdminPortal.UserAdmin.edit-confirm', compact('editForm'));
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }
    
    public function getEditFinish() {
        DB::beginTransaction();
        try {
            $addForm = new UserAdminForm();
            if(Session::has("SESSION_EDIT_USER_ADMIN")){
                $editForm = Session::get("SESSION_EDIT_USER_ADMIN");
                $obj = $this->userAdminService->update($editForm);
                if($obj==null){
                    throw new Exception(trans('exception.DATA_NOT_FOUND'));
                }
                Session::forget('SESSION_EDIT_USER_ADMIN');
                DB::commit();
                return view('AdminPortal.UserAdmin.edit-finish',  compact('editForm'));
            }else{
                return redirect('/' . env('PREFIX_ADMIN_PORTAL').'/user-admin');
            }
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }
    
    public function postRefreshPassword(Request $data) {
        DB::beginTransaction();
        try {
            if ($this->adminSession->getRole() != 'SUPER_ADMIN') {
                throw new Exception(trans('exception.PERMISSION_DENIED'));
            }
            $request = $data->input();
            $hashcode = $request['hashcode'];
            $error = "";
            $response = array();
            $obj = $this->userAdminService->getDataByHashcode($hashcode);
            if($obj==null){
                throw new Exception(trans('exception.DATA_NOT_FOUND'));
            }
            if($error!=""){
                throw new Exception(trans('exception.SYSTEM_ERROR'));
            }else{
                $editForm = new UserAdminForm();
                $editForm->setHashcode($hashcode);
                $editForm->setPassword('123456a');
                $obj = $this->userAdminService->updatePassword($editForm);
                if($obj!=null){
                    $response['errCode'] = 200;
                    $response['errMess'] = trans('common.action_success');
                }else{
                    throw new Exception(trans('exception.DATA_NOT_FOUND'));
                }
            }
            $response = array();
            $response['errCode'] = 200;
            $response['errMess'] = trans('common.action_success');
            DB::commit();
            return json_encode($response);
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            $response = array();
            $response['errCode'] = 100;
            $response['errMess'] = $error;
            return json_encode($response);
        }
    }
    
    public function getChangePassword(){
        try {
            $obj = $this->userAdminService->getDataByHashcode($this->adminSession->getHashcode());
            if($obj==null){
                throw new Exception(trans('exception.DATA_NOT_FOUND'));
            }
            return view('AdminPortal.UserAdmin.change-password');
        } catch (Exception $ex) {
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }
    
    public function postChangePassword(Request $data){
        DB::beginTransaction();
        try {
            $request = $data->input();
            $editForm = new UserAdminForm();
            $editForm->setPassword(trim($request['password']));
            $editForm->setNewPassword(trim($request['newPassword']));
            $editForm->setReNewPassword(trim($request['reNewPassword']));
            $editForm->setHashcode($this->adminSession->getHashcode());
            $error = "";
            $obj = $this->userAdminService->getDataByHashcode($this->adminSession->getHashcode());
            if($obj==null){
                throw new Exception(trans('exception.DATA_NOT_FOUND'));
            }
            if($editForm->getPassword()==null){
                $error="Nhập mật khẩu hiện tại";
            }else if(md5($editForm->getPassword())!=$obj->password){
                $error="Mật khẩu hiện tại không đúng";
            }else if($editForm->getNewPassword()==null){
                $error="Nhập mật khẩu mới";
            }else if($editForm->getReNewPassword()==null){
                $error="Nhập xác nhận mật khẩu mới";
            }else if($editForm->getReNewPassword()!=$editForm->getNewPassword()){
                $error="Xác nhận mật khẩu mới không đúng";
            }
            if($error!=""){
                return view('AdminPortal.UserAdmin.change-password', compact('editForm','error'));
            }else{
                $editForm->setPassword($editForm->getNewPassword());
                $obj = $this->userAdminService->updatePassword($editForm);
                if($obj==null){
                    throw new Exception(trans('exception.DATA_NOT_FOUND'));
                }
                DB::commit();
                return redirect("/" . env('PREFIX_ADMIN_PORTAL') .'/logout');
                //return view('AdminPortal.userAdmin.change-password-finish');
            }
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }

}
