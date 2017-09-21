<?php

namespace App\Http\Requests;

use App\Http\Forms\RatingTypeForm;
use App\Http\Services\RatingTypeService;

class RatingTypeVatidate extends BaseValidate  {
    
    public function __construct() {
        $this->ratingTypeService = new RatingTypeService();
    }

    public function validate(RatingTypeForm $ratingTypeForm){
        /*
         * validate form
         */
        $error = "";
        if($ratingTypeForm->getCode()==null){
            $error = trans('error.rating_code_field_required');
        }else if($ratingTypeForm->getCode()== env("RATING_TYPE_FREQUENCY") || $ratingTypeForm->getCode()==env("RATING_TYPE_MONETARY_VALUE")){
           if($ratingTypeForm->getCycle()==null){
                $error = trans('error.rating_cycle_field_required');
            }else if(preg_match($this->PATTERN_IS_NUMBER, $ratingTypeForm->getCycle())){
                $error = trans('error.rating_cycle_field_is_number');
            }else if($ratingTypeForm->getName()==null){
                $error = trans('error.name_field_required');
            }else if($ratingTypeForm->getMinValue()==null){
                $error = trans('error.rating_min_value_field_required');
            }else if(preg_match($this->PATTERN_IS_NUMBER, $ratingTypeForm->getMinValue())){
                $error = trans('error.rating_min_value_field_is_number');
            }else if($ratingTypeForm->getMaxValue()==null){
                $error = trans('error.rating_max_value_field_required');
            }else if(preg_match($this->PATTERN_IS_NUMBER, $ratingTypeForm->getMaxValue())){
                $error = trans('error.rating_max_value_field_is_number');
            }else if(count($this->ratingTypeService->searchData($ratingTypeForm))>0){
                $error = trans('error.rating_type_exits');
            }
        }else{
            if($ratingTypeForm->getName()==null){
                $error = trans('error.name_field_required');
            }else if($ratingTypeForm->getMinValue()==null){
                $error = trans('error.rating_min_value_field_required');
            }else if(preg_match($this->PATTERN_IS_NUMBER, $ratingTypeForm->getMinValue())){
                $error = trans('error.rating_min_value_field_is_number');
            }else if($ratingTypeForm->getMaxValue()==null){
                $error = trans('error.rating_max_value_field_required');
            }else if(preg_match($this->PATTERN_IS_NUMBER, $ratingTypeForm->getMaxValue())){
                $error = trans('error.rating_max_value_field_is_number');
            }else if(count($this->ratingTypeService->searchData($ratingTypeForm))>0){
                $error = trans('error.rating_type_exits');
            }
        }
        return $error;
    }

}
