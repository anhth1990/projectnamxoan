<?php

namespace App\Http\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Http\Models\Contact;
use App\Http\Models\User;
use App\Http\Models\Order;

class ContactController extends Controller {

    public function __construct() {
        $this->contact_model = new Contact();
        $this->user_model = new User();
        $this->order_model = new Order();
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
		$contact = $this->contact_model->getContactByHashcode($hashcode);
		$insert = array();
		$insert['id_user'] = $contact->id_user;
		$insert['hashcode'] = $this->getHashcode();
		$insert['id_shop'] = $contact->id_shop;
		$insert['id_rep'] = $contact->id;
		$insert['title'] = '';
		$insert['type'] = 1;
		$insert['content'] = $input['content'];
		$insert['status'] = 0;
		$insert['time_send'] = time();
		$insert['created_at'] = time();
		$insert['updated_at'] = time();
		if($this->contact_model->insertData($insert)){
			return redirect('/'.$this->admin_local."/view-notification/".$input['c_id']); 
		}
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
			$string = date("dd-MM-yyyy",$time);
			
		}
		return $string;
	}
}

?>