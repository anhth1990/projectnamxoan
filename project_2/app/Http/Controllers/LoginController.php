<?php

namespace App\Http\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Models\Login;
use App\Http\Models\Log;
use App\Http\Models\Config;
/*
 * form
 */
use App\Http\Forms\ConfigForm;
/*
 * service
 */
use App\Http\Services\ConfigService;

class LoginController extends Controller {

    public function __construct() {
        $this->login_model = new Login();
        $this->log_model = new Log();
        $this->config_model = new Config();
        $this->admin_local = trans('layout.local_admin');
        $this->configService = new ConfigService();
    }

    public function getLogin() {
        if (Session::has("login_adpc")){
			return redirect("/" . $this->admin_local);
		}          
        return view('Admin.login.index');
    }

    public function postLogin(LoginRequest $request) {
        $data_input = $request->input();
        $username = $this->convertInput($data_input['username']);
        $password = $this->hashMd5($this->convertInput($data_input['password']));
        $data = $this->login_model->checkUser($username, $password);
        if (count($data) > 0) {
            /*
             * Lấy cấu hình của shop
             */
            $configForm = new ConfigForm();
            $configForm->setShopId($data['id']);
            $configData = $this->configService->getConfigByShopId($configForm);
            Session::put("config_adpc", $configData);
            /*
             * --- lấy cấu hình của shop
             */
            $data_capnhat = array(
                'last_login' => time(),
            );
            if ($this->login_model->UpdateData(intval($data->id), $data_capnhat)) {
                Session::put("login_adpc", $data);
                $arrLog = array();
                $arrLog['id_user'] = $data['id'];
                $arrLog['ip'] = $this->getClientIp();
                $arrLog['time_log'] = time();
                $arrLog['browser'] = $_SERVER['HTTP_USER_AGENT'];
                $this->log_model->insertData($arrLog);
                return redirect('/' . $this->admin_local);
            } else {
                $error .= "Có lỗi xảy ra vui lòng đăng nhập lại sau|";
                return view('Admin.login.index', compact('error'));
            }
        } else {
            return view('Admin.login.index');
        }
    }
    private function getClientIp() {
        if (getenv('HTTP_CLIENT_IP')) {
                        $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
                        $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
                        $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
                        $ip = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
                        $ip = getenv('HTTP_FORWARDED');
        } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
	public function logout(){
		Session::forget('login_adpc');
		return redirect('/' . $this->admin_local.'/login');
	}
}

?>