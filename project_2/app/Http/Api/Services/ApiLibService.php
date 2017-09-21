<?php

namespace App\Http\Api\Services;

use Illuminate\Support\Facades\Storage;
use Exception;

/*
 * anhth1990 
 */

class ApiLibService {

    public function __construct() {
        
    }

    public function getData() {
        $DOMAIN = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'];
        $headerInfo = $this->getHeaderInfo();
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $rawData = file_get_contents("php://input");
        if (!isset($headerInfo['CONTENT_LENGTH'])) {
            die();
        }
        $length = $headerInfo['CONTENT_LENGTH'];
//==CHECK GET INPUT

        if (!isset($_GET['p']) || !isset($_GET['t'])) {
            die();
        } else {
            $key = $_GET['p'];
            $t = $_GET['t'];
            if ($this->checkTimeRequest($key, $t) == false) {
                die();
            }
        }
        return $this->decodeData($rawData, $length, $key);
    }

    function getHeaderInfo() {
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) == "HTTP_") {
                $key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
                $out[$key] = $value;
            } else {
                $out[$key] = $value;
            }
        }
        return $out;
    }

    function checkTimeRequest($key, $t) {//031701_v1 hàm kiểm tra xem trong get có p và t / hợp lệ không?
        $sizek = strlen($key);
        $timee = hex2bin($t);
        $size = strlen($timee);
        $out = "";
        for ($i = 0; $i < $size; $i++) {
            $tem = pack('c', (((ord($timee[$i]) >> 5) | (ord($timee[$i]) << 3)) ^ ord($key[$i % $sizek])));
            $out .= pack('c', (ord($tem) >> 5) | (ord($tem) << 3));
        }
        $timeclient = intval($out);
        $timeserver = time();

        if (($timeclient > $timeserver - 60) && ($timeclient < $timeserver + 60)) {
            return true; //hợp lệ
        } else {
            return false; //không hợp lệ
        }
    }

    function decodeData($contents, $length, $key) {//031701_v1 hàm giải mã mới
        $sizek = strlen($key);
        $out = "";
        for ($i = 0; $i < $length; $i++) {
            $tem = pack('c', (((ord($contents[$i]) >> 5) | (ord($contents[$i]) << 3)) ^ ord($key[$i % $sizek])));
            $out .= pack('c', (ord($tem) >> 5) | (ord($tem) << 3));
        }
        return $out;
    }

}
