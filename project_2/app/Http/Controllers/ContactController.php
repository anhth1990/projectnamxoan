<?php

namespace App\Http\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Http\Models\ShopContactDetail;
use App\Http\Models\NoticeUser;
use App\Http\Models\Contact;
use App\Http\Models\User;
use App\Http\Models\Order;
use App\Http\Models\Notice;
use App\Http\Models\Fcm;

class ContactController extends Controller {

    public function __construct() {
        $this->contact_model = new Contact();
        $this->user_model = new User();
        $this->order_model = new Order();
		$this->notice_model = new Notice();
		$this->fcm_model = new Fcm();
		$this->shop_contact_detail_model = new ShopContactDetail();
		$this->notice_user_model = new NoticeUser();
        $this->admin_local = trans('layout.local_admin');
        $this->id_shop = Session::get('login_adpc')['id'];
		$this->page = $this->numberPage();
    }

	public function index(){
		$admin_local = $this->admin_local;
		$full_name = $this->getNameUser();	
		$contacts = $this->contact_model->pagingSearch($this->page, $this->id_shop);
		$status = array();
		$status[0] = trans('contact.status_0');
		$status[1] = trans('contact.status_1');
		return view('Admin.contact.index', compact('admin_local','contacts','full_name','status'));
	}
	
	public function view($hashcode){
		$admin_local = $this->admin_local;
		$hash = $hashcode;
		$hashcode = base64_decode($hashcode);
		$user = $this->user_model->getUserByHashcode($hashcode);
		$user_name = $user['fullname'];
		$contacts = $this->contact_model->getContactByUserShopId($user['id'],$this->id_shop);
		
		//update trạng thái là đã đọc
		$arr_update = array();
		$arr_update['status'] = 1;
		$arr_update['time_read'] = time();
		$this->contact_model->UpdateDataByShopUserId($user['id'],$this->id_shop, $arr_update);
		
		//đánh dấu là đã đọc (hiển thị trên thanh thông báo)
			$url = "nguoi-dung-phan-hoi/".base64_encode($hashcode);
			$arr_update = array();
			$arr_update['status'] = 1;
			$this->notice_model->UpdateData($url, $arr_update);
		
		
		$shop_name = Session::get('login_adpc')['shop_name'];
		return view('Admin.contact.view', compact('admin_local','contacts','user','hash','shop_name','user_name'));
	}
	
	
	public function view_bk($hashcode){
		$hash = $hashcode;
		$hashcode = base64_decode($hashcode);
		$contacts = $this->contact_model->getContactByHashcode($hashcode);
		$admin_local = $this->admin_local;
		$user = $this->user_model->checkUserById($contacts->id_user);
		//update trạng thái là đã đọc
		$arr_update = array();
		$arr_update['status'] = 1;
		$arr_update['time_read'] = time();
		$arr_update['updated_at'] = time();
		$this->contact_model->UpdateData($contacts->id, $arr_update);
		
		//đánh dấu là đã đọc (hiển thị trên thanh thông báo)
			$url = "nguoi-dung-phan-hoi/".base64_encode($hashcode);
			$arr_update = array();
			$arr_update['status'] = 1;
			$this->notice_model->UpdateData($url, $arr_update);
		
		//Lấy những tin trả lời
		$reps = $this->contact_model->getRepContact($contacts->id);
		$arr_name = array();
		foreach($reps as $value){
			$user = $this->user_model->getNameUser($value['id_user']);
			$arr_name[$value['id_user']] = $user->fullname;
		}
		
		$shop_name = Session::get('login_adpc')['shop_name'];
		return view('Admin.contact.view', compact('admin_local','contacts','user','hash','reps','shop_name','arr_name'));
	}
	
	public function repView(ContactRequest $request){
		$input = $request->input();
		$hashcode = base64_decode($input['c_id']);
		$user = $this->user_model->getUserByHashcode($hashcode);
		
		$insert = array();
		$insert['id_user'] = $user['id'];
		$insert['hashcode'] = $this->getHashcode();
		$insert['id_shop'] = $this->id_shop;
		$insert['id_rep'] = 0;
		$insert['type'] = 1;
		$insert['content'] = $input['content'];
		$insert['status'] = 0;
		$insert['time_send'] = time();
		$insert['created_at'] = time();
		$insert['updated_at'] = time();
		if($this->contact_model->insertData($insert)){
			//thêm vào bảng "bn_notice_user" để user nhận trong mục "Hộp thư"
			$arr_insert = array();
			//$arr_insert['id_notice'] = $contact->hashcode;
			$arr_insert['id_user'] = $user['id'];
			$arr_insert['type'] = 1;//Shop trả lời Sau khi user gửi contact
			$arr_insert['title'] = Session::get('login_adpc')['shop_name']." vừa trả lời liên hệ của bạn";
			$arr_insert['description'] = $input['content'];
			$arr_insert['id_shop'] = Session::get('login_adpc')['id'];
			$arr_insert['status'] = 0;//User chưa xem
			$arr_insert['api'] = 'user/viewContact';//Shop trả lời Sau khi user gửi contact
			$arr_insert['created_at'] = time();
			$arr_insert['updated_at'] = time();
			$this->notice_user_model->insertData($arr_insert);
			
			//Gửi thông báo cho User
			$list_id_user[0]['id_user'] = $user['id'];
			$tokens = $this->getListToken($list_id_user);//mảng các token của user mà sẽ gửi khi shop tạo xong thông báo
			$text = Session::get('login_adpc')['shop_name'].' vừa trả lời liên hệ của bạn';//nội dung thông báo
			$message = array("message" => $text);
			$this->sendNotification($tokens, $message);//gửi thông báo
			return redirect('/'.$this->admin_local."/view-notification/".$input['c_id']); 
		}
	}
	
	//CODE gửi thông báo tới cho APP-Client
	private function getListToken($list_id_user){//lấy mảng các token của user cần gửi;
		$arr_id = array();
		foreach($list_id_user as $value){
			$arr_id[] = $value['id_user'];
		}
		$list_token = $this->fcm_model->getDataByArrayId($arr_id);
		$arr_token = array();
		foreach($list_token as $value){
			$arr_token[] = $value['Token'];
		}
		return $arr_token;
	}
	private function getNameUser(){//mảng các Fullname mua hàng của SHop
		$arr = array();
		$user_ids = $this->order_model->getListIdUser($this->id_shop);
		foreach($user_ids as $value){
			$arr[] = $value['id_user'];
		}
		$users = $this->user_model->getFullname($arr);
		$result = array();
		foreach($users as $value){
			$result[$value['id']] = $value['fullname'];
		}
		return $result;
	}
	
	private function getNameInfo(){//mảng các Fullname mua hàng của SHop
		$arr = array();
		$user_ids = $this->order_model->getListIdUser($this->id_shop);
		foreach($user_ids as $value){
			$arr[] = $value['id_user'];
		}
		$users = $this->user_model->getFullname($arr);
		$result = array();
		foreach($users as $value){
			
			$result[$value['id']]['fullname'] = $value['fullname'];
			$result[$value['id']]['email'] = $value['email'];
			$result[$value['id']]['mobile'] = $value['mobile'];
		}
		return $result;
	}
	
    public function getCountNotification(){
		$count = $this->contact_model->getCountNotification($this->id_shop);
		return $count;
	}    
	
	public function getListNotification(){
		$notification = $this->contact_model->getTopNotification($this->id_shop);
		//return $notification;
		$result = array();
		if(count($notification) > 0){		
			foreach($notification as $value){
				$user = $this->user_model->checkUserById($value['id_user']);
				$arr = array();
				$arr['id'] = $value['hashcode'];			
				$arr['time_send'] = $this->genTime($value['time_send']);			
				$arr['user_name'] = $user['fullname'];
				if($value['id_rep'] == 0) $arr['name_notification']  = $user['fullname'].' '.trans('contact.type_noti_1');
				else $arr['name_notification']  = $user['fullname'].' '.trans('contact.type_noti_2');
				$result[] = $arr;
			}
		}
		return $result;
	}
	public function genTime($time){
		$value = time() - $time;
		$string = '';
		if($value < 60 ) $string = trans('contact.time_1');
		else if($value < 3600){
			$minute = number_format($value / 60);
			$string = $minute.' '.trans('contact.time_2');
		}
		else if($value < 86400){
			$h = number_format($value / 3600);
			$string = $h.' '.trans('contact.time_3');
		}
		else if($value < 604800){
			$day = number_format($value / 86400);
			$string = $day.' '.trans('contact.time_4');
		}else {
			$string = date("d-m-Y",$time);
			
		}
		return $string;
	}
}

?>