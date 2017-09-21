<?php

namespace App\Http\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\User;
use App\Http\Models\Order;
use App\Http\Models\Notice;

class NoticeController extends Controller {

    public function __construct() {
        $this->user_model = new User();
        $this->order_model = new Order();
        $this->notice_model = new Notice();
        $this->admin_local = trans('layout.local_admin');
        $this->id_shop = Session::get('login_adpc')['id'];
		$this->page = $this->numberPage();
    }

	public function index(){
		$admin_local = $this->admin_local;
		$notices = $this->notice_model->pagingSearch($this->page, $this->id_shop);
		return view('Admin.notice.index', compact('admin_local','notices'));
	}	
		
	public function getCountNotice(){//trả về số thông báo chưa được đọc của Shop
		return $this->notice_model->getCountNotice($this->id_shop);
	}
	public function getTopNotice(){//lấy 8 tin thông báo mới nhất
		return $this->notice_model->getTopNotice($this->id_shop);
	}
	/*
	public function index(){
		$admin_local = $this->admin_local;
		$notices = $this->shop_contact_model->pagingSearch($this->page, $this->id_shop);
		return view('Admin.contactshop.index', compact('admin_local','notices'));
	}
	public function create(){
		$admin_local = $this->admin_local;	
		return view('Admin.contactshop.create', compact('admin_local'));
	}	
	
	public function postCreate(ShopContactRequest $request){
		$list_id_user = $this->order_model->getListIdUser($this->id_shop);
		$admin_local = $this->admin_local;
		$input = $request->input();
		$keyStr = "title,content";
		$data = $this->getInput($input, $keyStr,1, time(),true);//dữ liệu sẽ lưu vào bảng Order
		$data['id_shop'] =  $this->id_shop;
		$data['status_view'] = 1;
		$hashcode = $this->getHashcode();
		$data['hashcode'] = $hashcode;
		$data['time_created'] = time();
		if($this->shop_contact_model->insertData($data)){
			$contact = $this->shop_contact_model->getIdByHashcode($hashcode);						
			foreach($list_id_user as $value){//$value['id_user']
				$arr_insert = array();
				$arr_insert['id_contact'] = $contact->id;
				$arr_insert['id_user'] = $value['id_user'];
				$arr_insert['id_shop'] = $this->id_shop;
				$arr_insert['status'] = 0;
				
				$arr_insert['created_at'] = time();
				$arr_insert['updated_at'] = time();
				$this->shop_contact_detail_model->insertData($arr_insert);
			}
			$tokens = $this->getListToken($list_id_user);//mảng các token của user mà sẽ gửi khi shop tạo xong thông báo
			$text = Session::get('login_adpc')['shop_name'].' vừa gửi thông báo cho bạn';//nội dung thông báo
			$message = array("message" => $text);
			$this->sendNotification($tokens, $message);//gửi thông báo
			return redirect("/" . $this->admin_local."/thong-bao-da-tao");
		}else{
			  echo "<script>alert('".trans('contact.create_error')."');</script>"; 
              echo "<script>location.href='".Asset("/" . $this->admin_local."/index")."'</script>";
		}
	}
	public function view($hashcode){
		$admin_local = $this->admin_local;
		$hash = base64_decode($hashcode);
		$notice = $this->shop_contact_model->getContactByHashcode($hash);
		$report = $this->shop_contact_detail_model->getSendRead($notice->id);
		return view('Admin.contactshop.view', compact('admin_local','notice','report'));
	}
	
	
	//CODE gửi thông báo tới cho APP-Client
	public function getListToken($list_id_user){//lấy mảng các token của user cần gửi;
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
	}*/

}

?>