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
use App\Http\Services\ProIdentityLogService;
use App\Http\Forms\ProIdentityForm;
use App\Http\Forms\ProIdentityDetailForm;
use App\Http\Forms\ProIdentityLogForm;
use App\Http\Services\LibService;

class ManagerIdController extends BaseController {

    private $apiService;
    private $identityService;
    private $identityDetailService;
    private $libService;
    private $identityLogService;

    public function __construct() {
        parent::__construct();
        $this->apiService = new ApiLibService();
        $this->identityService = new ProIdentityService();
        $this->identityDetailService = new ProIdentityDetailService();
        $this->identityLogService = new ProIdentityLogService();
        $this->libService =  new LibService();
    }

    public function getData() {
        DB::beginTransaction();
        try {
            $strData = $this->apiService->getData();
            //$strData = "12345vsec|0*2017-06-28 14:57:10*c:\abc\d\f*md5|0*2017-06-28 14:57:10*c:\abc\d\f";
            //$strData = "12348vsec|";
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
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
                    } else {
                        //TH đã tồn tại ID
                        // TH đã tồn tại usb -> Kiểm tra xem usb có đang online
                        // nếu đang offline thì sẽ ghi log ip
                        if(time() - strtotime($identityObj->last_login) >= env('TIME_OFFLINE')){
                            $identityForm->setIp($ip);
                            // ghi log db
                            $identityLogForm = new ProIdentityLogForm();
                            $identityLogForm->setIndentityId($identityObj->id);
                            $identityLogForm->setIp($ip);
                            $identityLogForm->setLogTime(date("Y-m-d H:i:s"));
                            $this->identityLogService->insert($identityLogForm);
                        }   
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
                                if (isset(${'arrData_' . $i}[0]) && ${'arrData_' . $i}[0] != "") {
                                    $indentityDetailForm->setType(${'arrData_' . $i}[0]);
                                }
                                if (isset(${'arrData_' . $i}[1]) && ${'arrData_' . $i}[1] != "") {
                                    $indentityDetailForm->setTime(${'arrData_' . $i}[1]);
                                }
                                if (isset(${'arrData_' . $i}[2]) && ${'arrData_' . $i}[2] != "") {
                                    $indentityDetailForm->setUrl(${'arrData_' . $i}[2]);
                                }
                                if (isset(${'arrData_' . $i}[3]) && ${'arrData_' . $i}[3] != "") {
                                    $indentityDetailForm->setCode(${'arrData_' . $i}[3]);
                                }
                                if ($indentityDetailForm->getType() != null || $indentityDetailForm->getTime() != null || $indentityDetailForm->getUrl() != null || $indentityDetailForm->getCode() != null) {
                                    $this->identityDetailService->insert($indentityDetailForm);
                                }
                            }
                        }
                    }
                    DB::commit();
                    return "success";
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
            //$listObj = $this->identityService->searchListData($searchForm);
            //$countObj = $this->identityService->countList($searchForm);
            return view('AdminPortal.ManagerId.list', compact('searchForm', 'page'));
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
            if (isset($response['orderName'])) {
                $searchForm->setOrderName($response['orderName']);
            }
            if (isset($response['orderIDOnline'])) {
                $searchForm->setOrderIDOnline($response['orderIDOnline']);
            }
            Session::put('SESSION_SEARCH_IDENTITY', $searchForm);
            return redirect("/" . env('PREFIX_ADMIN_PORTAL') . "/manager-id");
        } catch (Exception $ex) {
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }

    public function getClearSearch() {
        try {
            Session::forget('SESSION_SEARCH_IDENTITY');
            return redirect("/" . env('PREFIX_ADMIN_PORTAL') . "/manager-id");
        } catch (Exception $ex) {
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }

    public function getDataList(Request $data) {
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
            if ($searchForm->getOrderIDOnline() != null) {
                $searchForm->setPageSize(null);
                $listObj = $this->identityService->searchListData($searchForm);
                foreach ($listObj as $key => $value) {
                    if (time() - strtotime($value->last_login) >= env('TIME_OFFLINE')) {
                        unset($listObj[$key]);
                    }
                }
            }
            return view('AdminPortal.ManagerId.data', compact('listObj', 'countObj', 'searchForm', 'page'))->render();
        } catch (Exception $ex) {
            $this->logs_custom("\nController ****\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }

    public function postDelete(Request $data) {
        DB::beginTransaction();
        try {
            if ($this->adminSession->getRole() != 'SUPER_ADMIN') {
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

    public function getEdit($hashcode) {
        try {
            if ($this->adminSession->getRole() != 'SUPER_ADMIN') {
                throw new Exception(trans('exception.PERMISSION_DENIED'));
            }
            $obj = $this->identityService->getDataByHashcode($hashcode);
            if ($obj == null) {
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
            if ($obj == null) {
                throw new Exception(trans('exception.DATA_NOT_FOUND'));
            }
            $identityForm = new ProIdentityForm();
            $identityForm->setId($obj->id);
            $identityForm->setHashcode($hashcode);
            $identityForm->setName($name);
            $this->identityService->update($identityForm);
            DB::commit();
            return redirect("/" . env('PREFIX_ADMIN_PORTAL') . "/manager-id/edit/" . $hashcode);
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }

    public function getUploadFile() {
        try {
            if ($this->adminSession->getRole() != 'SUPER_ADMIN') {
                throw new Exception(trans('exception.PERMISSION_DENIED'));
            }

            return view('AdminPortal.ManagerId.upload-file');
        } catch (Exception $ex) {
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }

    public function postUploadFile(Request $data) {
        DB::beginTransaction();
        try {
            $request = $data->input();
            $file = $data->file('file');
            $error = "";
            if ($file == null) {
                $error = "Mời chọn tệp";
            } elseif ($file->getClientOriginalExtension() != 'xlsx') {
                $error = "Tệp không hợp lệ";
            }
            // upload file
            $path = base_path().$this->libService->uploadFile($file, 'data', 'data_id');
            $type = $file->getClientOriginalExtension();
            $arrayData = null;
            switch ($type) {
                case "xlsx":
                    // include PHP Excel
                    include base_path().'/app/Http/Services/PHPExcel/PHPExcel/IOFactory.php';
                    try {
                        $inputFileType = \PHPExcel_IOFactory::identify($path);
                        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                        $objPHPExcel = $objReader->load($path);
                    } catch(Exception $e) {
                        die('Lỗi không thể đọc file "'.pathinfo($path,PATHINFO_BASENAME).'": '.$e->getMessage());
                    }
                    //  Lấy thông tin cơ bản của file excel

                    // Lấy sheet hiện tại
                    $sheet = $objPHPExcel->getSheet(0); 

                    // Lấy tổng số dòng của file, trong trường hợp này là 6 dòng
                    $highestRow = $sheet->getHighestRow(); 

                    // Lấy tổng số cột của file, trong trường hợp này là 4 dòng
                    $highestColumn = $sheet->getHighestColumn();

                    // Khai báo mảng $rowData chứa dữ liệu

                    //  Thực hiện việc lặp qua từng dòng của file, để lấy thông tin
                    for ($row = 1; $row <= $highestRow; $row++){ 
                        // Lấy dữ liệu từng dòng và đưa vào mảng $rowData
                        $rowData[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE,FALSE);
                    }

                    //In dữ liệu của mảng
                    $arrayData = $rowData;
                    if(!isset($arrayData[0][0][0]) || !isset($arrayData[0][0][1]) || $arrayData[0][0][0]!='Id' || $arrayData[0][0][1]!='Name'){
                        throw new Exception('NỘI DUNG DỮ LIỆU KHÔNG HỢP LỆ');
                    }
                    break;
                default:
                    $err = "Tệp không hợp lệ";
            }
            if ($error != "") {
                return view('AdminPortal.ManagerId.upload-file', compact('error'));
            } else {
                foreach ($arrayData as $key=>$value){
                    if($key!=0){
                        $idForm = new ProIdentityForm();
                        $idForm->setIndentity(trim($value[0][0]));
                        if($value[0][1]!=""){
                            $idForm->setName($value[0][1]);
                        }
                        $obj = $this->identityService->getDataByIdenty($idForm->getIndentity());
                        if($obj==null){
                            $this->identityService->insert($idForm);
                        }else{
                            $idForm->setId($obj->id);
                            $this->identityService->update($idForm);
                        }
                    }
                }
                DB::commit();
                return view('AdminPortal.ManagerId.upload-file-finish');
            }
        } catch (Exception $ex) {
            DB::rollback();
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }
    public function getLog($hashcode) {
        try {
            if ($this->adminSession->getRole() != 'SUPER_ADMIN') {
                throw new Exception(trans('exception.PERMISSION_DENIED'));
            }
            $obj = $this->identityService->getDataByHashcode($hashcode);
            if ($obj == null) {
                throw new Exception(trans('exception.DATA_NOT_FOUND'));
            }
            // set page
            $page = 1;
            if (isset($_GET['page'])) {
                $page = intval($_GET['page']);
                if ($page == 0)
                    $page = 1;
            }
            $identityLogForm = new ProIdentityLogForm();
            $identityLogForm->setIndentityId($obj->id);
            $identityLogForm->setStatus(env('COMMON_STATUS_ACTIVE'));
            $identityLogForm->setPageSize(env('PAGE_SIZE'));
            $listObj = $this->identityLogService->searchListData($identityLogForm);
            $countObj = $this->identityLogService->countList($identityLogForm);
            return view('AdminPortal.ManagerId.log', compact('listObj','obj','page','countObj'));
        } catch (Exception $ex) {
            $this->logs_custom("\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }
    
    public function postLogDelete(Request $data) {
        DB::beginTransaction();
        try {
            if ($this->adminSession->getRole() != 'SUPER_ADMIN') {
                throw new Exception(trans('exception.PERMISSION_DENIED'));
            }
            $request = $data->input();
            $hashcode = $request['hashcode'];
            $identityLogForm = new ProIdentityLogForm();
            $identityLogForm->setHashcode($hashcode);
            $identityLogForm->setStatus(env('COMMON_STATUS_DELETED'));
            $this->identityLogService->update($identityLogForm);
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
    
    public function postLogDeleteMulti(Request $data) {
        DB::beginTransaction();
        try {
            if ($this->adminSession->getRole() != 'SUPER_ADMIN') {
                throw new Exception(trans('exception.PERMISSION_DENIED'));
            }
            $request = $data->input();
            $listHashcode = explode(',', $request['myCheckboxes']);
            foreach ($listHashcode as $value){
                $identityLogForm = new ProIdentityLogForm();
                $identityLogForm->setHashcode($value);
                $identityLogForm->setStatus(env('COMMON_STATUS_DELETED'));
                $this->identityLogService->update($identityLogForm);
            }
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
    
    public function postDeleteAll(Request $data){
        DB::beginTransaction();
        try {
            if ($this->adminSession->getRole() != 'SUPER_ADMIN') {
                throw new Exception(trans('exception.PERMISSION_DENIED'));
            }
            $request = $data->input();
            $searchForm = new ProIdentityDetailForm();
            $listIdentityDetail = $this->identityDetailService->searchListData($searchForm);
            foreach ($listIdentityDetail as $key=>$value){
                $identityForm = new ProIdentityDetailForm();
                $identityForm->setHashcode($value->hashcode);
                $identityForm->setStatus(env('COMMON_STATUS_DELETED'));
                $this->identityDetailService->update($identityForm);
            }
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

}
