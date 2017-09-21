<?php

namespace App\Http\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ShopContactRequest ;
use App\Http\Models\ShopContact;
use App\Http\Models\ShopContactDetail;
use App\Http\Models\NoticeRep;
use App\Http\Models\Notice;
use App\Http\Models\User;
use App\Http\Models\Order;
use App\Http\Models\NoticeUser;
use App\Http\Models\Fcm;

class ShopContactController extends Controller {

    public function __construct() {
        $this->shop_contact_model = new ShopContact();
        $this->shop_contact_detail_model = new ShopContactDetail();
        $this->user_model = new User();
        $this->order_model = new Order();
        $this->notice_rep_model = new NoticeRep();
        $this->notice_model = new Notice();
        $this->notice_user_model = new NoticeUser();
        $this->fcm_model = new Fcm();
        $this->admin_local = trans('layout.local_admin');
        $this->id_shop = Session::get('login_adpc')['id'];
		$this->page = $this->numberPage();
    }

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
	
	public function viewRep($id,$user){//Xem chi tiết Khi user trả lời 1 thông báo của shop
		$admin_local = $this->admin_local;
		//đánh dấu là đã đọc thông báo này
		$url = "nguoi-dung-tra-loi-thong-bao/".$id."/".$user;
		$arr_update = array();
		$arr_update['status'] = 1;
		$this->notice_model->UpdateData($url, $arr_update);
		
		$id_contact = $id;
		$id_detail = $user;
		$reply = $this->notice_rep_model->getNoticeByHashcodeIdUser(base64_decode($id),base64_decode($user));//danh sách trả lời
		$hash = base64_decode($id);
		$notice = $this->shop_contact_model->getContactByHashcode($hash);
		$report = $this->shop_contact_detail_model->getSendRead($notice->id);
		$arr_name = array();
		foreach($reply as $value){
			$user = $this->user_model->getNameUser($value['id_user']);
			$arr_name[$value['id_user']] = $user->fullname;
			break;//vì tất cả đều của 1 user nên chỉ cần get 1 lần
		}
		$shop_name = Session::get('login_adpc')['shop_name'];
		return view('Admin.contactshop.view_rep', compact('admin_local','notice','report','reply','arr_name','id_contact','id_detail','shop_name'));
	}
	
	public function postRep(Request $request){
		$input = $request->input();
		$keyStr = "id_detail,id_contact,content";
		$data = $this->getInput($input, $keyStr,1, time(),true);
		$data['id_detail'] = base64_decode($data['id_detail']);
		$data['id_contact'] = base64_decode($data['id_contact']);
		$data['type'] = 1;
		$data['id_user'] = 0;
		
		if($this->notice_rep_model->insertData($data)){
			//thêm vào bảng "bn_notice_user" để user nhận trong mục "Hộp thư"
			$detail_nocice = $this->shop_contact_detail_model->getDataById($data['id_detail']);
			$arr_insert = array();
			$arr_insert['id_notice'] = $data['id_contact'];
			$arr_insert['id_user'] = $detail_nocice->id_user;
			$arr_insert['type'] = 2;//Shop trả lời trong thông báo
			$arr_insert['title'] = Session::get('login_adpc')['shop_name']." vừa trả lời phản hồi của bạn";
			$arr_insert['description'] = $data['content'];
			$arr_insert['id_shop'] = Session::get('login_adpc')['id'];
			$arr_insert['status'] = 0;//User chưa xem
			$arr_insert['api'] = 'user/viewNotice';//Shop tạo thông báo, người dùng và shop trả lời qua lại
			$arr_insert['created_at'] = time();
			$arr_insert['updated_at'] = time();
			$this->notice_user_model->insertData($arr_insert);
			return redirect("/" . $this->admin_local."/nguoi-dung-tra-loi-thong-bao/".$input['id_contact']."/".$input['id_detail']);
		}else{
			echo "<script>alert('".trans('contact.create_error')."');</script>"; 
			  echo "<script>location.href='".Asset("/" . $this->admin_local."/")."'</script>";
		}
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
	}

}

?>