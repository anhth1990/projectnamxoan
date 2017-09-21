<?php
namespace App\Http\Controllers;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\Order;
use App\Http\Models\User;
use App\Http\Models\Survey;
use App\Http\Models\SurveyUser;
use App\Http\Models\Notice;
use App\Http\Models\Fcm;

class SurveyController extends Controller {

    public function __construct() {
        $this->admin_local = trans('layout.local_admin');
        $this->id_shop = Session::get('login_adpc')['id'];
		$this->page = $this->numberPage();
		$this->order_model = new Order();
		$this->user_model = new User();
		$this->survey_model = new Survey();
		$this->survey_user_model = new SurveyUser();
		$this->notice_model = new Notice();
		$this->fcm_model = new Fcm();
    }

	public function index(){
		$admin_local = $this->admin_local;
		$surveys = $this->survey_model->pagingSearch($this->page,$title='',$this->id_shop);
		return view('Admin.survey.index', compact('admin_local','surveys'));
	}
	
	public function create(){
		$admin_local = $this->admin_local;
		return view('Admin.survey.create', compact('admin_local'));
	}	
	public function postCreate(Request $request){
		$input = $request->input();
		$list_id_user = $this->order_model->getListIdUser($this->id_shop);//mảng các user_id đã mua hàng của Shop
		$arr_id_user = array(); //mảng các id_user của shop, lấy từ bảng order
        foreach($list_id_user as $user){
            $arr_id_user[] = $user['id_user'];
        }	
		$keyStr = "title,content";
        $data_insert = $this->getInput($input, $keyStr,1, time());
		$arr = array();
		$arr_user = array();//mảng lưu vào bảng campaign_user
		$k=1;
		foreach($input['title_'] as $key=>$value){
			$arr_detail = array();
			$arr_detail['title'] = $value;
			$i=1;
			foreach($input['answer_'.$key] as $key=>$value){
				if($i>5) break;//Giới hạn không quá 5 câu trả lời ở đây
				$arr_detail['answer'][$i] = $value;			
				$arr_user[$k][$i] = 0;
				$i++;
			}	
			$arr[] = $arr_detail;
			$k++;
		}
		/*
		echo "<pre>";
			print_r($arr);
		echo "</pre>";die;
		*/
		$hashcode = $this->getHashcode();
		$data_insert['hashcode'] = $hashcode;
		$data_insert['id_shop'] = $this->id_shop;
		$data_insert['count_user'] = count($list_id_user);
		$data_insert['count_rep'] = 0;
		$data_insert['data_survey'] = json_encode($arr);
		
		$this->survey_model->insertData($data_insert);//lưu vào bảng campaign
		
		
		//Lưu vào bảng survey_campaigns_user
		foreach($arr_id_user as $value){
			$data_insert_user = array();
			$data_insert_user['hashcode'] = $this->getHashcode();;
			$data_insert_user['id_user'] = $value;
			$data_insert_user['id_campaign'] = $hashcode;//hashcode của campaign vừa tạo
			$data_insert_user['data_survey'] = json_encode($arr_user);//user chưa trả lời nên mảng này có cá giá trị =0
			$data_insert_user['status'] = 0;//chưa trả lời
			$data_insert_user['created_at'] = time();
			$data_insert_user['updated_at'] = time();
			$this->survey_user_model->insertData($data_insert_user);//lưu vào bảng campaign_user
		}
		//Gửi thông báo cho User
		$tokens = $this->getListToken($list_id_user);//mảng các token của user mà sẽ gửi khi shop tạo xong thông báo
		$text = Session::get('login_adpc')['shop_name'].' vừa gửi tạo khảo sát mới';//nội dung thông báo
		$message = array("message" => $text);
		$this->sendNotification($tokens, $message);//gửi thông báo
		$admin_local = $this->admin_local;
		return view('Admin.survey.create', compact('admin_local'));
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
	public function view($hashcode){
		$admin_local = $this->admin_local;
		$survey = $this->survey_model->getSurveyByHashCode(base64_decode($hashcode));
		return view('Admin.survey.view', compact('admin_local','survey'));
	}
	
	public function viewUser($hashcode){
		$admin_local = $this->admin_local;
		$survey = $this->survey_model->getSurveyByHashCode(base64_decode($hashcode));
		$list_user = $this->survey_user_model->checkListUserByHashcode($survey->hashcode);
		$arr_rep = array();//mảng thông tin người dùng trả lời
		foreach($list_user as $value){
			$arr_user = array();
			$user_info = $this->user_model->getNameUser($value['id_user']);
			$arr_user['user']['fullname'] = $user_info->fullname;
			$arr_user['user']['mobile'] = $user_info->mobile;
			$arr_user['user']['email'] = $user_info->email;
			$arr_user['data']['status'] = $value['status'];
			$arr_user['data']['survey'] = json_decode($value['data_survey']);
			$arr_rep[] = $arr_user;
		}
		//=========================
		$question_survey = json_decode($survey['data_survey']);//mảng câu hỏi
		$n_question = count($question_survey);//số lượng câu hỏi
		$arr_survey = array();//Mảng kết quả tổng hợp số lượng lựa chọn (trả lời của user), bắt đầu tất cả mang giá trị 0
		foreach($question_survey as $key=>$question){
			$arr = array();
			$n = count(get_object_vars($question->answer))+1;
			for($i=1;$i<$n;$i++){
				$arr[$i] = 0;
			}
			$arr_survey[$key+1] = $arr;	
		}

		$data_survey = json_decode($survey->data_survey);	
		foreach($data_survey as $key=>$value){
			$temp = get_object_vars($arr_rep[$key]['data']['survey']);
			$n_question = count($temp);//số lượng câu hỏi
			foreach($temp as $keyStr=>$valueStr){
				$arr_value = get_object_vars($valueStr);
				$n_value = count($arr_value)+1;
				foreach($arr_value as $keyV=>$valueV){
					$arr_survey[$keyStr][$keyV] += $valueV;
				}
			}
		}

		$result_char = array();//Mảng chuyền dữ liệu vào biểu đồ
		foreach($arr_survey as $key=>$value){
			$arr_result_2 = array();
			foreach($value as $i=>$j){
				$arr = array();
				$arr['country'] = $question_survey[$key-1]->answer->$i;
				$arr['litres'] = $j;
				$arr_result_2[] = $arr;
			}
			$result_char[] = json_encode($arr_result_2);
		}
		/*foreach($result_char as $key=>$value){
			echo $value."<br>";
		}
		echo "<pre>";
		print_r($result_char);die;
*/
		return view('Admin.survey.view_user', compact('admin_local','survey','arr_rep','result_char'));
	}
	
	private function updateReadSurvey($id){
		$arr_update_sr = array();
		$arr_update_sr['shop_check'] = 1;
		$this->survey_user_model->UpdateData($id, $arr_update_sr);
	}
	
	public function detailUser($hashcode){
		$view = null;
		$selected_id = 0;
		if(isset($_GET['user'])){
			$temp_sr = $this->survey_user_model->getIdByHashcode(base64_decode($_GET['user']));
			if(count($temp_sr)>0){
				$view = $this->getFormUser(base64_decode($_GET['user']),base64_decode($hashcode));	
				$selected_id = $temp_sr->id;
				if($temp_sr->shop_check == 0){//cập nhật trạng thái là đã xem
					$this->updateReadSurvey($selected_id);
				}
				//đánh dấu là đã đọc (hiển thị trên thanh thông báo)
				$url = "nguoi-dung-tra-loi-khoa-sat/".$hashcode."?user=".$_GET['user'];
				$arr_update = array();
				$arr_update['status'] = 1;
				$this->notice_model->UpdateData($url, $arr_update);
			}
		}
		$admin_local = $this->admin_local;
		$survey = $this->survey_model->getSurveyByHashCode(base64_decode($hashcode));

		$list_user = $this->survey_user_model->checkListUserByHashcode($survey->hashcode);
		$arr_rep = array();//mảng thông tin người dùng đã trả lời
		$arr_name = array();//mảng thông tin fullname/sđt (để select)
		$arr_name[0] = "Khách hàng";
		foreach($list_user as $value){
			if($value['status'] == 1){
				$arr_user = array();
				$user_info = $this->user_model->getNameUser($value['id_user']);
				$arr_user['user']['fullname'] = $user_info->fullname;
				$arr_user['user']['mobile'] = $user_info->mobile;
				$arr_user['user']['email'] = $user_info->email;
				$arr_user['data']['status'] = $value['status'];
				$arr_user['data']['survey'] = json_decode($value['data_survey']);
				$arr_rep[] = $arr_user;
				$arr_name[$value['id']] = $user_info->fullname? $user_info->fullname:$user_info->mobile;
			}
		}
		
		//Tính toán % tổng các câu trả lời của khách hàng
		$question_survey = json_decode($survey['data_survey']);//mảng câu hỏi
		$n_question = count($question_survey);//số lượng câu hỏi
		$arr_survey = array();//Mảng kết quả tổng hợp số lượng lựa chọn (trả lời của user), bắt đầu tất cả mang giá trị 0
		foreach($question_survey as $key=>$question){
			$arr = array();
			$n = count(get_object_vars($question->answer))+1;
			for($i=1;$i<$n;$i++){
				$arr[$i] = 0;
			}
			$arr_survey[$key+1] = $arr;	
		}

		foreach($arr_rep as $value){//duyệt mảng thông tin người dùng đã trả lời
			$reps = get_object_vars($value['data']['survey']);
			foreach($reps as $key=>$rep){
				$rep = get_object_vars($rep);
				foreach($arr_survey[$key] as $tk=>$temp_sr){
					$arr_survey[$key][$tk] += $rep[$tk];
				}
			}
		}
		return view('Admin.survey.detail_user', compact('admin_local','survey','arr_rep','result_char','arr_name','hashcode','arr_survey','view','selected_id'));
	}	
	public function detailUser_bk($hashcode){//2017-0-2-23: trước khi thay đổi format mà Client trả về khi trả lời
		$view = null;
		$selected_id = 0;
		if(isset($_GET['user'])){
			$temp_sr = $this->survey_user_model->getIdByHashcode(base64_decode($_GET['user']));
			if(count($temp_sr)>0){
				$view = $this->getFormUser(base64_decode($_GET['user']),base64_decode($hashcode));	
				$selected_id = $temp_sr->id;
				if($temp_sr->shop_check == 0){//cập nhật trạng thái là đã xem
					$this->updateReadSurvey($selected_id);
				}
				//đánh dấu là đã đọc (hiển thị trên thanh thông báo)
				$url = "nguoi-dung-tra-loi-khoa-sat/".$hashcode."?user=".$_GET['user'];
				$arr_update = array();
				$arr_update['status'] = 1;
				$this->notice_model->UpdateData($url, $arr_update);
			}
		}
		$admin_local = $this->admin_local;
		$survey = $this->survey_model->getSurveyByHashCode(base64_decode($hashcode));
		$list_user = $this->survey_user_model->checkListUserByHashcode($survey->hashcode);
		$arr_rep = array();//mảng thông tin người dùng đã trả lời
		$arr_name = array();//mảng thông tin fullname/sđt (để select)
		$arr_name[0] = "Khách hàng";
		foreach($list_user as $value){
			if($value['status'] == 1){
				$arr_user = array();
				$user_info = $this->user_model->getNameUser($value['id_user']);
				$arr_user['user']['fullname'] = $user_info->fullname;
				$arr_user['user']['mobile'] = $user_info->mobile;
				$arr_user['user']['email'] = $user_info->email;
				$arr_user['data']['status'] = $value['status'];
				$arr_user['data']['survey'] = json_decode($value['data_survey']);
				$arr_rep[] = $arr_user;
				$arr_name[$value['id']] = $user_info->fullname? $user_info->fullname:$user_info->mobile;
			}
		}
		
		//Tính toán % tổng các câu trả lời của khách hàng
		$question_survey = json_decode($survey['data_survey']);//mảng câu hỏi
		$n_question = count($question_survey);//số lượng câu hỏi
		$arr_survey = array();//Mảng kết quả tổng hợp số lượng lựa chọn (trả lời của user), bắt đầu tất cả mang giá trị 0
		foreach($question_survey as $key=>$question){
			$arr = array();
			$n = count(get_object_vars($question->answer))+1;
			for($i=1;$i<$n;$i++){
				$arr[$i] = 0;
			}
			$arr_survey[$key+1] = $arr;	
		}

		foreach($arr_rep as $value){//duyệt mảng thông tin người dùng đã trả lời
			$reps = get_object_vars($value['data']['survey']);
			foreach($reps as $key=>$rep){
				$rep = get_object_vars($rep);
				foreach($arr_survey[$key] as $tk=>$temp_sr){
					$arr_survey[$key][$tk] += $rep[$tk];
				}
			}
		}
		return view('Admin.survey.detail_user', compact('admin_local','survey','arr_rep','result_char','arr_name','hashcode','arr_survey','view','selected_id'));
	}
	
	public function getFormUser($id,$hashcode){//lấy view trả lời câu hỏi của 1 cụ thể
		$survey = $this->survey_model->getSurveyByHashCode($hashcode);
		$user_rep = $this->survey_user_model->checkUserByHashcodeCode($survey->hashcode,$id);
		$feedback = $user_rep['content'];
		$user_survey = json_decode($user_rep['data_survey']);
		$data_survey = json_decode($survey['data_survey']);
		return view('Admin.survey.form_user_answer', compact('data_survey','user_survey','feedback'));
	}
	
	public function getCountSurvey(){//lấy ra số lượng người dùng vừa trả lời khảo sát
		$count = $this->survey_model->getCountSurvey($this->id_shop);
		return $count;
	}
	public function getSurvey(){//lấy ra số lượng người dùng vừa trả lời khảo sát
		$survey = $this->survey_model->getSurvey($this->id_shop);
		return $survey;
	}
	
	public function loadFormUser(Request $request){//lấy view trả lời câu hỏi của từng người
		$input = $request->input();
		if($input['id'] != 0){//có xác định 1 người dùng nào đó
			$survey = $this->survey_model->getSurveyByHashCode(base64_decode($input['code']));
			$user_rep = $this->survey_user_model->checkUserByHashcodeId($survey->hashcode,$input['id']);		
			if($user_rep->shop_check == 0){//cập nhật trạng thái là đã xem
				$this->updateReadSurvey($input['id']);
			}
			$feedback = $user_rep['content'];
			$user_survey = json_decode($user_rep['data_survey']);
			$data_survey = json_decode($survey['data_survey']);
			return view('Admin.survey.form_user_answer', compact('data_survey','user_survey','feedback'));
		}else{//chọn xem tất cả
			$survey = $this->survey_model->getSurveyByHashCode(base64_decode($input['code']));
			$list_user = $this->survey_user_model->checkListUserByHashcode($survey->hashcode);
			$arr_rep = array();//mảng thông tin người dùng đã trả lời
			foreach($list_user as $value){
				if($value['status'] == 1){
					$arr_user = array();
					$arr_user['data']['survey'] = json_decode($value['data_survey']);
					$arr_rep[] = $arr_user;
				}
			}
			//Tính toán % tổng các câu trả lời của khách hàng
			$question_survey = json_decode($survey['data_survey']);//mảng câu hỏi
			$n_question = count($question_survey);//số lượng câu hỏi
			$arr_survey = array();//Mảng kết quả tổng hợp số lượng lựa chọn (trả lời của user), bắt đầu tất cả mang giá trị 0
			foreach($question_survey as $key=>$question){
				$arr = array();
				$n = count(get_object_vars($question->answer))+1;
				for($i=1;$i<$n;$i++){
					$arr[$i] = 0;
				}
				$arr_survey[$key+1] = $arr;	
			}

			foreach($arr_rep as $value){//duyệt mảng thông tin người dùng đã trả lời
				$reps = get_object_vars($value['data']['survey']);
				foreach($reps as $key=>$rep){
					$rep = get_object_vars($rep);
					foreach($arr_survey[$key] as $tk=>$temp_sr){
						$arr_survey[$key][$tk] += $rep[$tk];
					}
				}
			}
			return view('Admin.survey.form_default_answer', compact('survey','arr_survey'));
		}

	}
	
	public function allNotification(){//danh sách các tin nhắn báo có người trả lời khảo sát
		$admin_local = $this->admin_local;
		$list_survey = $this->survey_model->getListSurvey($this->page,$this->id_shop);
		return view('Admin.survey.all_notification', compact('admin_local','list_survey'));
	}
	
	public function loadForm(){
		return view('Admin.survey.form_insert');
	}	
	public function loadFormAnswer(){
		$i = $_GET['id'];
		return view('Admin.survey.form_answer',compact('i'));
	}
}

?>