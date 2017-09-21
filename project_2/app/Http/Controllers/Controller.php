<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Exception;
use App\Http\Services\LibService;

class Controller extends BaseController {
    
    public $PATTERN_IS_NUMBER = '/^[0-9]+$/';
    public $PATTERN_FORMAT_EMAIL = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
    
    public function listStatusCommon(){
        return array(env('COMMON_STATUS_ACTIVE'),env('COMMON_STATUS_INACTIVE'));
    }
    
    public function listUnitRecensy(){
        return array("hour","day","month","year");
    }
    
    public function listUnitFrequency(){
        return array("transaction");
    }
    
    public function listUnitMonetary(){
        return array("dong","million");
    }


    /*
     * Config rating
     */
    public function listTypeConfigRating(){
        return array(env('RATING_TYPE_RECENSY'),env('RATING_TYPE_FREQUENCY'),env('RATING_TYPE_MONETARY_VALUE'));
    }
    
    /*
     * Rating
     */
    public function listRating(){
        return array(env('RATING_NORMAL'),env('RATING_SILVER'),env('RATING_GOLD'),env('RATING_PLATINUM'));
    }
    
    public function getInput($data, $keyStr,$convert) {
        $arrResult = array();
        $keyArr = explode(',', $keyStr);
        foreach ($keyArr as $value) {
            if(isset($data[$value])){
                if($convert != null) $data[$value] = $this->convertTypeInput($data[$value]);
            }
            if($value != 'content'){
                if(isset($data[$value])){
                    $arrResult[$value] = strip_tags($data[$value]);
                    /*
                     * trim() loại bỏ khoảng trắng 2 đầu data
                     * tránh trường hợp nhập space
                     */
                    $arrResult[$value] = trim($arrResult[$value]);
                }
            }else{
                if(isset($data[$value]))
                    $arrResult[$value] = $data[$value];
            }
           
        }
        return $arrResult;
    }
    
    public function convertInput($value) {
        $value = str_replace(array('&quot;', '"', "'", "\\"), '', $value);
        return $value;
    }
    
    public function convertTypeInput($value) {
        $value = str_replace(array('/', "'", "\\","`","^","$","#"), '', $value);
        return $value;
    }
    
    public function logs_custom($content){
        error_log(date(env('DATE_FORMAT_Y_M_D'))." : ".$content." "."\n", 3, base_path('storage/logs/custom.log'));
    }
    
    public function getLatLong(Request $request){
        try {
            $libService = new LibService();
            $data = $request->input();
            return $libService->getLatLong($data['address']);
        } catch (Exception $ex) {
            $this->logs_custom("\nController ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            $error = $ex->getMessage();
            return view('errors.503',  compact('error')); 
        }
    }
    
}
