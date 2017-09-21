<?php

namespace App\Http\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Models\User;
use App\Http\Models\UserPoint;
use App\Http\Models\Order;
use App\Http\Models\Config;
use App\Http\Models\Campaign;
use App\Http\Models\ConfigHistory;
use App\Http\Models\Point;
use App\Http\Controllers\ConfigController;
use App\Http\Models\PointUserShop;
class UserController extends Controller {

    public function __construct() {
        $this->user_model = new User();
        $this->user_point_model = new UserPoint();
        $this->order_model = new Order();
        $this->config_model = new Config();
        $this->campaign_model = new Campaign();
        $this->config_controller = new ConfigController();    
        $this->config_history_model = new ConfigHistory();
        $this->point_model = new Point();
        $this->point_user_shop_model = new PointUserShop();
        $this->admin_local = trans('layout.local_admin');
        $this->id_shop = Session::get('login_adpc')['id'];
    }

    public function index() {
        $list_id_user = $this->order_model->getListIdUser($this->id_shop);
        $arr_id_user = array(); //mảng các id_user của shop, lấy từ bảng order
        foreach($list_id_user as $user){
            $arr_id_user[] = $user['id_user'];
        }
        $list_user = $this->user_model->pagingSearch( $this->numberPage(), $name = '', $arr_id_user);
        $admin_local = $this->admin_local;
        
        $arr_status = array();//mảng giá trị cho trạng thái của người dùng
        $arr_status['-1'] = trans('user.user_status-1');
        $arr_status['0'] = trans('user.user_status0');
        $arr_status['1'] = trans('user.user_status1');
        return view('Admin.user.index', compact('admin_local','list_user','arr_status'));
    }


    
    public function getCreate() {
        $admin_local = $this->admin_local;
		$date_buy = date( 'd-m-Y', time());
        $list_id_user = $this->order_model->getListIdUser($this->id_shop);
        $arr_id_user = array(); //mảng các id_user của shop, lấy từ bảng order
        foreach($list_id_user as $user){
            $arr_id_user[] = $user['id_user'];
        }
        $list_mobile = $this->user_model->getMobile($arr_id_user);
        $arr_mobile = array(); //mảng các số điện thoại (của khách hàng) đã từng mua hàng của Shop
        $arr_mobile[0] = trans('user.search');
        foreach($list_mobile as $mobile){
            $arr_mobile[$mobile['mobile']] = $mobile['mobile'];
        }
        
        $point = array();
        $point[1] = "Giá trị đơn hàng";
        $point[2] = "Số lượng sản phẩm";
        $point[3] = "Số lần sử dụng dịch vụ";
        
        $sl = array();
        $sl[1] = '';
        $sl[2] = trans('user.sl_mh');
        $sl[3] = trans('user.sl_sd');
        
        $config = $this->config_controller->checkUsingCampaign();//Lấy thông tin cấu hình được sử dụng
        $arr_point = array();
        $arr_point[0] = $point[$config['scoring']];
        
        $text_sl = $sl[$config['scoring']];
        return view('Admin.user.create', compact('admin_local','arr_point','text_sl','arr_mobile','date_buy'));
    }
    public function getInfo(Request $request){
        $input = $request->input();
        $user = $this->user_model->checkUserByMobile($input['mobile']);
        $result = array();
        $result['email'] = $user['email'];
        $result['fullname'] = $user['fullname'];
        $result['age'] = $user['age'];
        $result['address'] = $user['address'];
        echo json_encode($result);
        
    }
    public function postCreate(UserRequest $request) {
        $input = $request->input();
        if($input['date_buy'] == "") $input['date_buy'] = date( 'd-m-Y', time());
        $input['date_buy'] = strtotime($input['date_buy']);

        $config = $this->config_controller->checkUsingCampaign();//Lấy thông tin cấu hình được sử dụng
        $keyStrOrder = "price,address_shop,profit,date_buy";
        if ($config['scoring'] != 1) {
            $keyStrOrder .=",count_product";
        }
        $data_order = $this->getInput($input, $keyStrOrder,1, time());//dữ liệu sẽ lưu vào bảng Order
        $data_order['id_user'] = '';
        $data_order['id_campaign'] = $config['hashcode'];//id campaign / cấu hình áp dụng
        $data_order['id_shop'] = $this->id_shop;//id của shop
        $data_order['type_campaign'] = 1;//giá trị của cấu hình mặc định
        $data_order['hashcode'] = $this->getHashcode();
        //Kiểm tra xem có sử dụng mã giảm giá không?
        if($input['_isPromotion'] != 0){//có mã
            $code = $input['promotion'];
            $point = $this->point_model->getPoint($code);
            $id_point = $point['id'];
            $update_code = array();
            $update_code['status'] = 1;
            $update_code['updated_at'] = time();
            $this->point_model->UpdateData($id_point, $update_code);//cập nhật trạng thái cho code là đã sử dụng
            $data_order['bonus_points'] = 0;//không tính điểm thưởng cho lần thanh toán này
            $data_order['code_promotion'] = $code;//lưu trạng thái là lần thanh toán này có sử dụng điểm thưởng
        }else{
            //Tính điểm thưởng
            if ($config['scoring'] == 1) {//1: Tích điểm theo giá trị đơn hàng
                $data_order['bonus_points'] = $this->getBonusPoint($data_order['price'], $config['block']);
            } else if ($config['scoring'] == 2) {//2: Tích điểm theo số lượng sản phẩm
                $data_order['bonus_points'] = $this->getBonusPoint($data_order['count_product'], $config['block']);;
            } else if($config['scoring'] == 3){//3: Tích điểm theo số lần sử dụng sản phẩm
                $data_order['bonus_points'] = $this->getBonusPoint($data_order['count_product'], $config['block']);;
            }
            //End Tính điểm thưởng  
        }
        if(isset($config['campaign'])){//nếu cấu hình là campaign
            $data_order['type_campaign'] = 2;
        }     

        $checkIssetUser = $this->user_model->checkUserByMobile($input['mobile']);//kiểm tra xem đã có thành viên lưu trong bảng User chưa?
        $id_user = 0;//Giá trị ID của USER, sẽ thay đổi bên dưới
        if (count($checkIssetUser) > 0) {//Đã có trong bảng User, chỉ lưu vào bảng Order
            $id_user = $checkIssetUser['id'];
            //Cộng thêm điểm thưởng/ update vào bảng User
            $point = $checkIssetUser['point'] + $data_order['bonus_points'];
            $data_point = array();
            $data_point['point'] = $point;
            $this->user_model->UpdateData($checkIssetUser['id'],$data_point);//cập nhật vào bảng User 
            //end
            $data_order['id_user'] = $checkIssetUser['id'];             
            $this->order_model->insertData($data_order); //Thêm vào bảng Order
        }else {//chưa có trong bảng user thì thêm mới vào bảng User, thêm vào bảng point_user_shop
            $keyStrUser = "fullname,email,age,address,mobile";
            $data_user = $this->getInput($input, $keyStrUser,-1, time());//dữ liệu sẽ lưu vào bảng Order
            $data_user['point'] = $data_order['bonus_points'];//tính điểm thưởng cho khách hàng vào bảng User
            $data_user['hashcode'] = $this->getHashcode();
            if ($this->user_model->insertData($data_user)) {//Đã thêm thành công vào bảng  User
                $checkIssetUserNew = $this->user_model->checkUserByMobile($data_user['mobile']);
                $id_user = $checkIssetUserNew['id'];
                $data_order['id_user'] = $id_user;     
                $this->order_model->insertData($data_order); //Thêm vào bảng Order
            }   
        }                
        //update vào bảng history point
        if($data_order['bonus_points'] != 0){
            $history = array();
            $history['id_user'] = $id_user;
            $history['id_shop'] = $this->id_shop;
            $history['type'] =1;//cộng khi mua hàng
            $history['id_type'] =$data_order['hashcode'];
            $history['point'] = $data_order['bonus_points'];//giá trị điểm thưởng
            $history['created_at'] = time();
            $history['updated_at'] = time();
            $this->user_point_model->insertData($history);
            //end             
        } 

		//Thêm / cập nhật vào bảng point_user_shop
		$checkShopUser = $this->point_user_shop_model->checkShopUser($id_user,$this->id_shop);
		if(count($checkShopUser)>0){ // Đã có thì cập nhật
			$arr_pus = array();
			$arr_pus['toltal'] = $checkShopUser['toltal'] + $data_order['bonus_points'];
			$arr_pus['availability'] = $checkShopUser['availability'] + $data_order['bonus_points'];
			$arr_pus['updated_at'] = time();
			$this->point_user_shop_model->UpdateData($checkShopUser['id'],$arr_pus);
		} else {//Chưa có thì thêm mới
			$arr_pus = array();
			$arr_pus['id_user'] = $id_user;
			$arr_pus['id_shop'] = $this->id_shop;
			$arr_pus['toltal'] = $data_order['bonus_points'];
			$arr_pus['point_using'] = 0;//vì lần đầu nên giá trị sử dụng là 0
			$arr_pus['availability'] = $data_order['bonus_points'];
			$arr_pus['created_at'] = time();
			$arr_pus['updated_at'] = time();
			$this->point_user_shop_model->insertData($arr_pus);
		}
        return redirect("/" . $this->admin_local . "/ban-hang/them-moi");

    }

    public function viewUser($hashcode){
        $admin_local = $this->admin_local;
        if(isset($hashcode)){
            $user = $this->user_model->getUserByHashcode(base64_decode($hashcode)); 
            if(count($user)>0){
                $orders = $this->order_model->getOrderByIdUser($this->numberPage(),$user['id'],$this->id_shop);
                $total = $this->order_model->getTotalPrice($user['id'],$this->id_shop);
                $point = $this->point_user_shop_model->checkShopUser($user['id'],$this->id_shop);
                return view('Admin.user.report', compact('admin_local','orders','user','total','point'));
            }else{
                return \Response::view('Admin.Block.505');
            }            
        }else {
            return \Response::view('Admin.Block.505');
        }
        
    }
    
    public function viewCampaignUsing($hashcode){//hiển thị cách tính điểm cộng (khi xem danh sách mua hàng)
        $type_config = $this->typeConfig();
        $order = $this->order_model->getOrderByHashcode($hashcode);
        if($order['type_campaign'] == 1){//cấu hình mặc định
            $config = $this->config_history_model->getConfigByHashcode($order['id_campaign']);
			$name_config = trans('user.config_default');
            return view('Admin.user.report.campaign',compact('type_config','config','name_config'));
        }else if($order['type_campaign'] == 2){//dùng campaign
			$config = $this->campaign_model->getCampaignByHashcode($order['id_campaign'],$this->id_shop);
			$name_config = trans('user.config_campaign');
			return view('Admin.user.report.campaign',compact('type_config','config','name_config'));
        }
    }
    
    public function viewOrderUsing($hashcode,$type){//hiển thị chi tiết hóa đơn mua hàng (khi vào xem lịch sử sử dụng điểm thưởng)
        if($type == 1){//Mua hàng
            $order = $this->order_model->getOrderByHashcode($hashcode);
            return view('Admin.user.report.order',compact('order'));
        }else if($type == 2){//Giới thiệu
            return view('Admin.user.report.code');
        }else if($type == 3){//Tạo mã giảm giá
            $point = $this->point_model->checkPoint($hashcode);
            $status_code = array();
            $status_code[0] = trans('user.status_code_0');;
            $status_code[1] = trans('user.status_code_1');
            return view('Admin.user.report.code',  compact('point','status_code'));
        }      
    }
    
    public function viewHistoryPoint($hashcode){
        $admin_local = $this->admin_local;
        $user = $this->user_model->getUserByHashcode(base64_decode($hashcode));
        $history = $this->user_point_model->pagingSearch($this->numberPage(),$user['id'],$this->id_shop);
        $total = $this->order_model->getTotalPrice($user['id'],$this->id_shop);
		$point = $this->point_user_shop_model->checkShopUser($user['id'],$this->id_shop);
        $type_point = array();
        $type_point['1'] = trans('user.type_point_1');
        $type_point['2'] = trans('user.type_point_2');
        $type_point['3'] = trans('user.type_point_3');
        $type_point['4'] = trans('user.type_point_4');
        return view('Admin.user.history', compact('admin_local','history','user','total','type_point','point'));
    }
    
    public function getImport() {
        $admin_local = $this->admin_local;
        return view('Admin.user.import', compact('admin_local'));
    }

    public function postImport(Request $request) {
        $users = $this->uploadFile($_FILES);
        $config = $this->config_model->checkConfig($this->id_shop);
        if (count($users) > 0) {
            foreach ($users as $data) {
                //kiểm tra xem đã có thành viên lưu trong bảng User chưa?
                $checkIssetUser = $this->user_model->checkUser($data['email']);
                if (count($checkIssetUser) > 0) {//Đã có trong bảng User
                    $id_user = $checkIssetUser->id;
                    $keyStr2 = "address_shop,price";
                    $arr_order = $this->getInput($data, $keyStr2, 1, time());
                    $arr_order['id_shop'] = $this->id_shop;
                    $arr_order['id_user'] = $id_user;
                    //Tính điểm thưởng
                    if ($config['scoring'] == 1) {//tính theo %
                        $arr_order['bonus_points'] = $this->getBonusPoint($arr_order['price'], $config['percent']);
                    } else if ($config['scoring'] == 2) {//tính theo flat
                        $arr_order['bonus_points'] = $config['flat'];
                    }
                    //End Tính điểm thưởng         
                    $this->order_model->insertData($arr_order); //Thêm vào bảng Order
                } else {//Chưa có trong bảng User
                    $keyStr3 = "fullname,email,age,address,cmt";
                    $data_user = $this->getInput($data, $keyStr3, -1, time());
                    $data_user['hashcode'] = $this->getHashcode();
                    if ($this->user_model->insertData($data_user)) {//Đã thêm thành công vào bảng  User
                        $checkIssetUserNew = $this->user_model->checkUser($data['email']);
                        $id_user = $checkIssetUserNew['id'];
                        $keyStr2 = "address_shop,price";
                        $arr_order = $this->getInput($data, $keyStr2, 1, time());
                        $arr_order['id_shop'] = $this->id_shop;
                        $arr_order['id_user'] = $id_user;
                        //Tính điểm thưởng
                        if ($config['scoring'] == 1) {//tính theo %
                            $arr_order['bonus_points'] = $this->getBonusPoint($arr_order['price'], $config['percent']);
                        } else if ($config['scoring'] == 2) {//tính theo flat
                            $arr_order['bonus_points'] = $config['flat'];
                        }
                        //End Tính điểm thưởng         
                        $this->order_model->insertData($arr_order); //Thêm vào bảng Order
                    }
                }
            }
        }
        return redirect("/" . $this->admin_local . "/import-user");
    }

    private function uploadFile($FILES) {
        $UploadDirectory = 'public/uploads/temp/csv/';
        if (!is_dir($UploadDirectory)) {
            mkdir($UploadDirectory, 0755, true);
        } else {
            $mode = decoct(fileperms($UploadDirectory));
            $mode = substr($mode, -4);
            if ($mode != '0755') {
                chmod($UploadDirectory, 0755);
            }
        }
        $File_Name = strtolower($FILES['FileInput']['name']);
        $File_Ext = substr($File_Name, strrpos($File_Name, '.')); //get file extention
        $Random_Number = rand(0, 9999999999); //Random number to be added to name.
        $NewFileName = $Random_Number . $File_Ext; //new file name
        if (move_uploaded_file($FILES['FileInput']['tmp_name'], $UploadDirectory . $NewFileName)) {
            $urls = $this->getListUser($UploadDirectory . $NewFileName);
            return $urls;
        } else {
            return 'error';
        }
    }

    private function getListUser($files) {//toantd 20160325
        $file = fopen($files, "r");
        $arr_email = array();

        $i = 0;
        $check = false;
        while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE) {
            if ($i > 0) {
                if ($i > 100) {
                    $check = true; //vượt quá 100 user
                    Session::put("list_email_err", "1");
                    break;
                }
                $arr = array();
                $arr['fullname'] = $emapData[0];
                $arr['age'] = $emapData[1];
                $arr['address'] = $emapData[2];
                $arr['email'] = $emapData[3];
                $arr['cmt'] = $emapData[4];
                $arr['address_shop'] = $emapData[5];
                $arr['price'] = $emapData[6];
                $arr_email[] = $arr;
            }
            $i++;
        }
        //unlink($files);
        return $arr_email;
    }

    private function getBonusPoint($a, $b) {
        $c = $a / $b;
        $temp = explode(".", $c);
        return $temp[0];
    }

    public function downloadGuide() {
        $file = 'public/uploads/guide/guide.csv';
        $this->downloadF($file);
    }

    private function downloadF($file) {
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }

}

?>