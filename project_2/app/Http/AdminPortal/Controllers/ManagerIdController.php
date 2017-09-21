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
use App\Http\Api\Services\ApiLibService;
use App\Http\Services\ProIdentityService;
use App\Http\Services\ProIdentityDetailService;
use App\Http\Forms\ProIdentityForm;
use App\Http\Forms\ProIdentityDetailForm;

class ManagerIdController extends BaseController {

    private $apiService;
    private $identityService;
    private $identityDetailService;

    public function __construct() {
        parent::__construct();
        $this->apiService = new ApiLibService();
        $this->identityService = new ProIdentityService();
        $this->identityDetailService = new ProIdentityDetailService();
    }

    public function getData() {
        DB::beginTransaction();
        try {
            //$strData =  $this->apiService->getData();
            //$strData = "12345vsec|0*2017-06-28 14:57:10*c:\abc\d\f*md5|0*2017-06-28 14:57:10*c:\abc\d\f";
            $strData = "12349vsec|";
            if ($strData != null && $strData != "") {
                $arrData = explode("|", $strData);
                if (isset($arrData[0]) && $arrData[0] != null) {
                    $id = $arrData[0];
                    $identityObj = $this->identityService->getDataByIdenty($id);
                    // TH chưa tồn tại ID
                    $identityForm = new ProIdentityForm();
                    if ($identityObj == null) {
                        $identityForm->setIndentity($id);
                        //$identityForm->setUpdatedAt(time());
                        $identityForm->setLastLogin(date("Y-m-d H:i:s"));
                        $identityObj = $this->identityService->insert($identityForm);
                    }else{
                        //TH đã tồn tại ID
                        $identityForm->setId($identityObj->id);
                        $identityForm->setLastLogin(date("Y-m-d H:i:s"));
                        $identityObj = $this->identityService->update($identityForm);
                    }
                    $objId = $identityObj->id;
                    if (count($arrData) > 1) {

                        for ($i = 0; $i < count($arrData); $i++) {
                            if ($i > 0) {
                                ${'arrData_' . $i} = explode('*', $arrData[$i]);
                                $indentityDetailForm = new ProIdentityDetailForm();
                                $indentityDetailForm->setIndentityId($objId);
                                if (isset(${'arrData_' . $i}[0]) && ${'arrData_' . $i}[0]!="") {
                                    $indentityDetailForm->setType(${'arrData_' . $i}[0]);
                                }
                                if (isset(${'arrData_' . $i}[1]) && ${'arrData_' . $i}[1]!="") {
                                    $indentityDetailForm->setTime(${'arrData_' . $i}[1]);
                                }
                                if (isset(${'arrData_' . $i}[2]) && ${'arrData_' . $i}[2]!="") {
                                    $indentityDetailForm->setUrl(${'arrData_' . $i}[2]);
                                }
                                if (isset(${'arrData_' . $i}[3]) && ${'arrData_' . $i}[3]!="") {
                                    $indentityDetailForm->setCode(${'arrData_' . $i}[3]);
                                }
                                if ($indentityDetailForm->getType()!=null || $indentityDetailForm->getTime()!=null || $indentityDetailForm->getUrl()!=null || $indentityDetailForm->getCode()!=null) {
                                    $this->identityDetailService->insert($indentityDetailForm);
                                }
                            }
                        }
                    }
                    DB::commit();
                    echo "success";
                }
            }
        } catch (Exception $ex) {
            DB::rollback();
            echo "\nController ****\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine();
        }
    }

    /*
     * list
     */

    public function getList() {
        try {

            $searchForm = new ProIdentityForm();
            if (Session::has('SESSION_SEARCH_IDENTITY')) {
                $searchForm = Session::get('SESSION_SEARCH_IDENTITY');
            }
            // set page
            $page = 1;
            if (isset($_GET['page'])) {
                $page = intval($_GET['page']);
                if ($page == 0)
                    $page = 1;
            }
            $searchForm->setPageSize(env('PAGE_SIZE'));
            $listObj = $this->identityService->searchListData($searchForm);
            $countObj = $this->identityService->countList($searchForm);
            return view('AdminPortal.ManagerId.list', compact('listObj', 'countObj', 'searchForm', 'page'));
        } catch (Exception $ex) {
            $this->logs_custom("\nController ****\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }

    public function postList(Request $data) {
        try {
            $response = $data->input();
            $searchForm = new ProIdentityForm();
            $searchForm->setIndentity($response['identity']);
            $searchForm->setName($response['name']);
            //$searchForm->setStatus(env('COMMON_STATUS_DELETED'));
            Session::put('SESSION_SEARCH_IDENTITY', $searchForm);
            return redirect("/" . env('PREFIX_ADMIN_PORTAL') . "/manager-id");
        } catch (Exception $ex) {
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }
    
    public function getClearSearch(){
        try {
            Session::forget('SESSION_SEARCH_IDENTITY');
            return redirect("/" . env('PREFIX_ADMIN_PORTAL') . "/manager-id");
        } catch (Exception $ex) {
            $this->logs_custom("\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            $error = $ex->getMessage();
            return view('errors.503',  compact('error'));
        }
    }
    
    public function postDelete(Request $data) {
        DB::beginTransaction();
        try {
            $request = $data->input();
            $hashcode = $request['hashcode'];
            $identityForm = new ProIdentityDetailForm();
            $identityForm->setHashcode($hashcode);
            $identityForm->setStatus(env('COMMON_STATUS_DELETED'));
            $this->identityDetailService->update($identityForm);
            DB::commit();
            $response = array();
            $response['errCode']=200;
            $response['errMess']=  trans('common.action_success');
            return json_encode($response);
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            $response = array();
            $response['errCode']=100;
            $response['errMess']=  $error;
            return json_encode($response);
        }
    }
    
    public function getEdit($hashcode){
        try {
            $obj = $this->identityService->getDataByHashcode($hashcode);
            if($obj==null){
                throw new Exception(trans('exception.DATA_NOT_FOUND'));
            }
            $identityForm = new ProIdentityForm();
            $identityForm->setHashcode($hashcode);
            $identityForm->setName($obj->name);
            $identityForm->setIndentity($obj->identity);
            return view('AdminPortal.ManagerId.edit', compact('identityForm'));
        } catch (Exception $ex) {
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }
    
    public function postEdit(Request $data) {
        DB::beginTransaction();
        try {
            $request = $data->input();
            $hashcode = $request['hashcode'];
            $name = trim($request['name']);
            $obj = $this->identityService->getDataByHashcode($hashcode);
            if($obj==null){
                throw new Exception(trans('exception.DATA_NOT_FOUND'));
            }
            $identityForm = new ProIdentityForm();
            $identityForm->setId($obj->id);
            $identityForm->setHashcode($hashcode);
            $identityForm->setName($name);
            $this->identityService->update($identityForm);
            DB::commit();
            return redirect("/" . env('PREFIX_ADMIN_PORTAL') . "/manager-id/edit/".$hashcode);
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }

}
