<?php
namespace App\Http\Services;

use Illuminate\Support\Facades\Storage;
use Exception;
/* 
 * anhth1990 
 */
class LibService extends BaseService {
    public function __construct() {
        
    }
    /*
     * get lat long address
     */
    public function getLatLong($address){
        // Get JSON results from this request
        
        $geo = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false');
        //echo 'http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false';die();
        // Convert the JSON to an array
        $geo = json_decode($geo, true);

        if ($geo['status'] == 'OK') {
          // Get Lat & Long
          $latitude = $geo['results'][0]['geometry']['location']['lat'];
          $longitude = $geo['results'][0]['geometry']['location']['lng'];
        }
        $obj['lat'] = $latitude;
        $obj['long'] = $longitude;
        return json_encode($obj);
    }
    
    /*
     * upload file
     */
    public function uploadFile($file,$path,$prefix="info"){
        /*
         * Khởi tạo folder nếu chưa có
         */
        try {
            $createFolder = $this->createFolder($path);
            if($createFolder){
                $imageName = $this->getRandomName($prefix) . '.' . $file->getClientOriginalExtension();
                $file->move(base_path() . '/public/uploads/'.$path, $imageName);
            }
            return '/public/uploads/'.$path.'/'.$imageName;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
        
    }
    
    /*
     * upload images tmp
     */
    public function uploadImageTmp($file,$path,$prefix){
        /*
         * Khởi tạo folder nếu chưa có
         */
        try {
            $createFolder = $this->createFolder($path);
            if($createFolder){
                $imageName = $this->getRandomName($prefix) . '.' . $file->getClientOriginalExtension();
                $file->move(base_path() . '/public/uploads/'.$path, $imageName);
            }
            return $imageName;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
        
    }
    
    /*
     * write log csv
     */
    public function writeLog($content,$path,$name){
        //error_log(date(env('DATE_FORMAT_Y_M_D'))." : ".$content." "."\n", 3, base_path('storage/logs/custom.log'));
        /*
         * Khởi tạo folder nếu chưa có
         */
        try {
            $createFolder = $this->createFolderBasic($path);
            if($createFolder){
                $myfile = fopen(base_path() . $path.'/'.$name, "w");
                $myfile = fopen(base_path() . $path.'/'.$name, "w") or die("Unable to open file!");
                fwrite($myfile, $content);
                fclose($myfile);
            }
            return true;
        } catch (Exception $ex) {
            //return false;
            throw new Exception($ex->getMessage());
        }
    }


    /*
     * move file
     */
    public function moveFile($linkOld,$linkNew){
        try {
            
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }


    /*
     * delete file
     */
    public function deleteFile($path){
        /*
         * Khởi tạo folder nếu chưa có
         */
        try {
            $path = base_path().$path;
            if(file_exists($path)){
                unlink($path);
            }
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
        
    }
    
    /*
     * create folder upload
     */
    public function createFolder($path){
        if(Storage::disk('uploadImages')->has($path))
            return true;
        else
            return Storage::disk('uploadImages')->makeDirectory($path);
    }
    
    /*
     * create folder basic
     */
    public function createFolderBasic($path){
        if (!is_dir(base_path().$path)) {
            return mkdir(base_path().$path, 0777, true);
        }
        return is_dir(base_path().$path);
    }


    /*
     * delete folder
     */
    public function deleteFolder($path){
        if(Storage::disk('uploadImages')->has($path))
            return Storage::Delete(base_path().'/public/uploads/'.$path);
    }
    
    /*
     * get random string name
     */
    public function getRandomName($prefix){
        return $prefix.'_'.  strtolower($this->getHashcode());
    }
    
    /*
     * get hashcode
     */
    public function getHashcode(){
        $time = time();

        $str1 = substr($time, 0, 3);
        $str2 = substr($time, 4, 3);
        $str3 = substr($time, 6, 4);

        $rand = rand(10000,99999);
        $str4 = substr( strtoupper(md5($rand)),  0, 3);
        $rand = rand(10000,99999);
        $str5 = substr( strtoupper(md5($rand)),  0, 4);
        $rand = rand(10000,99999);
        $str6 = substr( strtoupper(md5($rand)),  0, 3);

        $hashcode = $str4.$str1.$str5.$str2.$str6.$str3;
        return $hashcode;
    }
    
    /*
     * get password
     */
    public function getPasswordRandom(){
        $code = "";
        $code = substr( rand(100000,999999), 0, 8);
        return $code;
    }
    
    /*
     * get coint random
     */
    public function getCointRandom(){
        return rand(0,2000);
    }
    
    /*
     * get password
     */
    public function getUsernameRandom(){
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 8; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    /*
     * exception
     */
    public function custom_include_path($file){
        return include base_path().'/config/'.$file.'.php';
    }
    
    public function setError($codeError){
        $error = $this->custom_include_path('errors');
        return $error[$codeError];
    }
    
    public function custom_throw_exception($errorCode){
        throw new Exception(trans('exception.'.$errorCode),$this->setError($errorCode));
    }
    
    public function hideInfo($string){
        $str = substr($string,5);
        $hide = "*****";
        return $hide.$str;
    }
    
    function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}

