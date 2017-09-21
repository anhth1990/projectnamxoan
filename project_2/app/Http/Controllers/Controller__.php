<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Models\Config;
use App\Http\Models\Campaign;
use App\Http\Forms\Configurations;
class Controller____ extends BaseController {
    
    public $configuration;
    
    public function __construct() {
        $this->configuration = new Configurations();
        
    }

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    public function convertInput($value) {
        $value = str_replace(array('&quot;', '"', "'", "\\"), '', $value);
        return $value;
    }
    public function convertTypeInput($value) {
        $value = str_replace(array('/', "'", "\\","`","^","$","#"), '', $value);
        return $value;
    }
    public function getHashcode(){
        /*$rand = rand(10000,99999);
        $rand2 = rand(10000,99999); 
        $hashcode =  substr( strtoupper(md5($rand)),  0, 5).''.time().''.substr( strtoupper(md5($rand2)),  0, 5);
        return $hashcode;*/
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
    
    public function getInput($data, $keyStr, $status = null, $create = null,$convert=null) {
        $arrResult = array();
        $keyArr = explode(',', $keyStr);
        foreach ($keyArr as $value) {
			if($convert != null) $data[$value] = $this->convertTypeInput($data[$value]);
            if($value != 'content'){
                $arrResult[$value] = strip_tags($data[$value]);
                /*
                 * trim() loại bỏ khoảng trắng 2 đầu data
                 * tránh trường hợp nhập space
                 */
                $arrResult[$value] = trim($arrResult[$value]);
            }else{
                $arrResult[$value] = $data[$value];
            }
           
        }
        if ($status != null)
            $arrResult['status'] = $status;
        if ($create != null)
            $arrResult['created_at'] = time(); //nếu có truyền vào time thì lấy
        $arrResult['updated_at'] = time();
        return $arrResult;
    }   
    public function getInput_bk($data, $keyStr, $status = null, $create = null) {
        $arrResult = array();
        $keyArr = explode(',', $keyStr);
        foreach ($keyArr as $value) {
            if($value != 'content'){
                $arrResult[$value] = strip_tags($data[$value]);
            }else{
                $arrResult[$value] = $data[$value];
            }
           
        }
        if ($status != null)
            $arrResult['status'] = $status;
        if ($create != null)
            $arrResult['created_at'] = time(); //nếu có truyền vào time thì lấy
        $arrResult['updated_at'] = time();
        return $arrResult;
    }

    public function hashMd5($md5) {
		$temp = substr( strtoupper(base64_encode(md5($md5))),  0, 30);
        return $temp.''.strtoupper(md5("yupax_" . $md5));
        //return md5("yupax_" . $md5);
    }

    public function typeConfig(){
        $result = array();
        $result[1] = trans('config.bonus_type_1');
        $result[2] = trans('config.bonus_type_2');
        $result[3] = trans('config.bonus_type_3');
        return $result;
    }
    
    public function numberPage() {//số record trên 1 trang
        return 20;
    }
    public function checkUsingCampaign(){//Lấy cấu hình áp dụng cho Shop tại thời điểm hiện tại
        $this->config_model = new Config();
        $this->campaign_model = new Campaign();
        $campaign = $this->campaign_model->checkConfig($this->id_shop);
        $defaul = $this->config_model->checkConfig($this->id_shop);//thông  tin cấu hình mặc định của Shop
        if(count($campaign)>0){//có bản ghi trong bảng config (lưu campaign)
            $time_now = time();
            if($time_now > $campaign['start_date'] && $time_now < $campaign['finish_date']){//hiện tại nằm trong khoảng thời gian của chính dịch
                return $campaign;
            }else{
                return $defaul;
            }
        }else{//không có trong bảng config=>chắc chắn dùng bảng default
            return $defaul;
        }
    }
	
	//CODE gửi thông báo tới cho APP-Client
	public function sendNotification($tokens, $message){//Gửi đến sever FCM
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields = array(
			 'registration_ids' => $tokens,
			 'data' => $message
			);
		$headers = array(
			'Authorization:key = AIzaSyBy8q6gMa45RXC28qhhAUnruGWNjBowBUI ',
			'Content-Type: application/json'
			);
	   $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
       return $result;
	}
}
