<?php
namespace App\Http\Services;
use App\Http\Forms\MerchantForm;
use Session;
use Datetime;
class BaseService {
    public $PATTERN_IS_NUMBER = '/[^0-9]/';
    public $PATTERN_FORMAT_EMAIL = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
    public $PATTERN_USERNAME = '/^[A-Za-z0-9]{5,10}$/';
    public $PATTERN_PASSWORD = '/^.{8,}$/';
    public $PATTERN_IS_MOBILE = '/^\d{9,11}$/';


    public $merchantFormSession;
    
    public function __construct() {
        $this->merchantFormSession = new MerchantForm();
        if(Session::has('login_admin_merchant'))
            $this->merchantFormSession = Session::get('login_admin_merchant');
    }


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
    
    public function getToken($hashcode){
        $time = time().$hashcode;

        $str1 = substr($time, 0, 4);
        $str2 = substr($time, 2, 5);

        $rand = rand(10000,99999);
        $str3 = substr( strtoupper(md5($rand)),  0, 4);
        $rand = rand(10000,99999);
        $str4 = substr( strtoupper(md5($rand)),  0, 4);

        $token = $str3."-".$str1."-".$str2."-".$str4;
        return $token;
    }
    
    public function logs_custom($content){
        error_log(date(env('DATE_FORMAT_Y_M_D'))." : ".$content." "."\n", 3, base_path('storage/logs/custom.log'));
    }
    
    /*
     * get active code
     */
    public function getActiveCode(){
        $code = "";
        $code = substr( rand(1000,999999), 0, 4);
        return $code;
    }
    
    public function formatDate($date){
        $dateFormat = new DateTime($date);
        return $dateFormat->format("d-m-Y");
    }
    
    
}

