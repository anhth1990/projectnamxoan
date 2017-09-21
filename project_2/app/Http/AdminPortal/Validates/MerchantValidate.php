<?php

namespace App\Http\AdminPortal\Validates;

use App\Http\Forms\MerchantForm;
use App\Http\Services\MerchantService;
use App\Http\Validates\BaseValidate;
use Exception;

class MerchantValidate extends BaseValidate  {
    
    public function __construct() {
        $this->merchantService = new MerchantService();
    }

    public function validate(MerchantForm $validateForm){
        /*
         * validate form
         */
        $error = null;
        if($validateForm->getFirstName()==null || $validateForm->getFirstName()==""){
            $error = trans('error.merchant_first_name_required');
        }else if($validateForm->getLastName()==null || $validateForm->getLastName()==""){
            $error = trans('error.merchant_last_name_required');
        }else if($validateForm->getName()==null || $validateForm->getName()==""){
            $error = trans('error.merchant_name_required');
        }else if($validateForm->getEmail()==null || $validateForm->getEmail()==""){
            $error = trans('error.email_required');
        }else if(!preg_match ($this->PATTERN_FORMAT_EMAIL, $validateForm->getEmail())){
            $error = trans('error.email_incorrect');
        }else if(!$this->merchantService->checkExistEmail($validateForm->getEmail())){
            $error = trans('error.email_exist');
        }else if($validateForm->getMobile()==null || $validateForm->getMobile()==""){
            $error = trans('error.mobile_required');
        }else if(!preg_match ($this->PATTERN_IS_NUMBER, $validateForm->getMobile())){
            $error = trans('error.mobile_incorrect');
        }else if(!$this->merchantService->checkExistMobile($validateForm->getMobile())){
            $error = trans('error.mobile_exist');
        }else if($validateForm->getProvinceId()==null || $validateForm->getProvinceId()==""){
            $error = trans('error.province_required');
        }else if($validateForm->getDistrictId()==null || $validateForm->getDistrictId()==""){
            $error = trans('error.district_required');
        }else if($validateForm->getWardId()==null || $validateForm->getWardId()==""){
            $error = trans('error.ward_required');
        }else if($validateForm->getAddress()==null || $validateForm->getAddress()==""){
            $error = trans('error.address_required');
        }else if($validateForm->getImages()!=null){
            if(!in_array($validateForm->getImages()->getClientOriginalExtension(), array('png','jpg','jepg'))){
                $error = trans('error.only_upload_images');
            }elseif($validateForm->getImages()->getClientSize()>2097152){
                $error = trans('error.images_less_2M');
            } 
            
        }
        return $error;
    }

}
