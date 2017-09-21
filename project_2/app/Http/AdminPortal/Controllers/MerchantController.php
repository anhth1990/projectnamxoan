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

use App\Http\Services\LibService;
use App\Http\Services\AddressService;
use App\Http\Forms\MerchantForm;
use App\Http\AdminPortal\Validates\MerchantValidate;
use App\Http\Services\MerchantService;
use App\Http\Models\MerchantDAO;
use Exception;
use App\Http\Models\ProvinceDAO;
use App\Http\Models\DistrictDAO;
use App\Http\Models\WardDAO;

class MerchantController extends BaseController {
    
    public function __construct() {
        parent::__construct();
        $this->libService = new LibService();
        $this->addressService = new AddressService();
        $this->merchantValidate = new MerchantValidate();
        $this->merchantService = new MerchantService();
    }
    
    public function listStatusUser(){
        return array(env('COMMON_STATUS_INACTIVE'),env('COMMON_STATUS_ACTIVE'));
    }
    
    public function copyMerchant(Request $request){
        $merchantForm = new MerchantForm();
        $data = $request->input();
        $keyStr = "firstName,lastName,name,email,mobile,provinceId,districtId,wardId,address,status,lat,long,images";
        $dataFormat = $this->getInput($data, $keyStr,1);
        $merchantForm->setFirstName($dataFormat["firstName"]);
        $merchantForm->setLastName($dataFormat["lastName"]);
        $merchantForm->setName($dataFormat["name"]);
        $merchantForm->setEmail($dataFormat["email"]);
        $merchantForm->setMobile($dataFormat["mobile"]);
        $merchantForm->setProvinceId($dataFormat["provinceId"]);
        $merchantForm->setDistrictId($dataFormat["districtId"]);
        $merchantForm->setWardId($dataFormat["wardId"]);
        $merchantForm->setAddress($dataFormat["address"]);
        $merchantForm->setStatus($dataFormat["status"]);
        $merchantForm->setLat($dataFormat["lat"]);
        $merchantForm->setLong($dataFormat["long"]);
        if($request->file('images')!=null)
            $merchantForm->setImages($request->file('images'));
        return $merchantForm;
    }

    /*
     * List
     */
    public function getList(){
        $titlePage = $this->configuration->titlePageAdminPortal.  trans('merchant.title');
        $searchForm = new MerchantForm();
        if(Session::has('SEARCH_MERCHANT_SESSION')){
            $searchForm = Session::get('SEARCH_MERCHANT_SESSION');
        }
        // set page
        $page=1;
        if(isset($_GET['page'])){
            $page = intval($_GET['page']);
            if($page==0)$page=1; 
        }
        $listStatusUser = $this->listStatusUser();
        $listMerchantDao = $this->merchantService->getList($searchForm);
        $countMerchantDao = $this->merchantService->getCount($searchForm);
        return view('AdminPortal.Merchant.list',  compact('titlePage','listMerchantDao','countMerchantDao','page','searchForm','listStatusUser'));
    }
    
    public function postList(Request $request){
        $merchantForm = new MerchantForm();
        $data = $request->input();
        $merchantForm->setName($data['name']);
        $merchantForm->setEmail($data['email']);
        $merchantForm->setMobile($data['mobile']);
        $merchantForm->setStatus($data['status']);
        Session::put('SEARCH_MERCHANT_SESSION',$merchantForm);
        return redirect("/" . env('PREFIX_ADMIN_PORTAL')."/merchant/list");
    }
    
    public function getDeleteSearch(){
        Session::forget('SEARCH_MERCHANT_SESSION');
        return redirect("/" . env('PREFIX_ADMIN_PORTAL')."/merchant/list");
    }


    /*
     * Add
     */
    public function getAdd(){
        $titlePage = $this->configuration->titlePageAdminPortal.  trans('merchant.title');
        $merchantForm = new MerchantForm();
        // list province - district - ward
        $listProvince = $this->addressService->getProvince();
        $listDistrict = $this->addressService->getDistrict();
        $listWard = $this->addressService->getWard();
        $listStatusUser = $this->listStatusUser();
        return view('AdminPortal.Merchant.add',  compact('titlePage','listWard','listDistrict','listProvince','listStatusUser','merchantForm'));
    }
    
    public function postAdd(Request $request){
        $titlePage = $this->configuration->titlePageAdminPortal.  trans('merchant.title');
        
        //echo $request->file('images')->getClientOriginalExtension();die();
        //$path = public_path() . '/public/uploads/merchant1';
        //File::makeDirectory($path, $mode = 0777, true, true);
        $merchantForm = new MerchantForm();
        $merchantForm = $this->copyMerchant($request);
        $merchantDao = new MerchantDAO();
        $error = null;
        $error = $this->merchantValidate->validate($merchantForm);
        // thông tin tỉnh thành phố
        $listProvince = $this->addressService->getProvince();
        $listDistrict = $this->addressService->getDistrict();
        $listWard = $this->addressService->getWard();
        $listStatusUser = $this->listStatusUser();
        // end thông tin tỉnh thành phố
        if($error!=null){
            return view('AdminPortal.Merchant.add',  compact('merchantForm','error','titlePage','listWard','listDistrict','listProvince','listStatusUser'));
        }else{
            
            try {
                $merchantDao = $this->merchantService->insertMerchant($merchantForm);
                /*
                 * set lại đường dẫn ảnh
                 */
                $merchantForm->setImages($merchantDao->images);
                // set name province - district - ward
                $provinceDao = new ProvinceDAO();
                $provinceDao = $this->addressService->getProvinceById($merchantForm->getProvinceId());
                $merchantForm->setProvinceName($provinceDao->type.' '.$provinceDao->name);
                $districtDao = new DistrictDAO();
                $districtDao = $this->addressService->getDistrictById($merchantForm->getDistrictId());
                $merchantForm->setDistrictName($districtDao->type.' '.$districtDao->name);
                $wardDao = new WardDAO();
                $wardDao = $this->addressService->getWardById($merchantForm->getWardId());
                $merchantForm->setWardName($wardDao->type.' '.$wardDao->name);
                return view('AdminPortal.Merchant.add-finish',  compact('merchantForm','titlePage'));
            } catch (Exception $ex) {
                $error = $ex->getLine();
                return view('AdminPortal.Merchant.add',  compact('merchantForm','error','titlePage','listWard','listDistrict','listProvince','listStatusUser'));
            }
        }
    }
    
    public function getLatLong(Request $request){
        $data = $request->input();
        return $this->libService->getLatLong($data['address']);
    }
    
    
}

