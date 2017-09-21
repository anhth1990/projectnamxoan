<?php

namespace App\Http\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ConfigRequest;
use App\Http\Requests\ShopInfoRequest;
use App\Http\Requests\CampaignRequest;
use App\Http\Models\Config;
use App\Http\Models\Rating;
use App\Http\Models\ConfigInfo;
use App\Http\Models\ConfigHistory;
use App\Http\Models\Campaign;
use App\Http\Models\ShopConfigRatings;
use App\Http\Forms\ConfigForm;
use App\Http\Forms\Configurations;
use App\Http\Services\ConfigService;
use DB;
use App\Http\Requests\ConfigRatingRequest;
use App\Http\Services\RatingConfigService;
use App\Http\Forms\RatingConfigForm;
use App\Http\Forms\RatingTypeForm;
use App\Http\Requests\RatingTypeVatidate;
use App\Http\Services\RatingTypeService;
/*
 * form
 */
use App\Http\Forms\RatingForm;
/*
 * service
 */


class ConfigController extends Controller {

    public function __construct() {
        $this->config_model = new Config();
        $this->rating_model = new Rating();
        $this->config_info_model = new ConfigInfo();
        $this->config_history_model = new ConfigHistory();
        $this->campaign_model = new Campaign();
        $this->config_ratings_model = new ShopConfigRatings();
        $this->admin_local = trans('layout.local_admin');
        $this->id_shop = Session::get('login_adpc')['id'];
        parent::__construct();
        $this->configService = new ConfigService();
        /*
         * rating config
         */
        $this->ratingTypeValidate = new RatingTypeVatidate();
        $this->ratingTypeService = new RatingTypeService();
    }
    
    /*
     * list status rating
     */
    public function listStatusRating(){
        return array(
            env('COMMON_STATUS_ACTIVE'),
            env('COMMON_STATUS_INACTIVE')
        );
    }

        public function config() {
        $admin_local = $this->admin_local;
        $arr_type = array();
        $arr_type['1'] = trans('config.scoring_type_1');
        $arr_type['2'] = trans('config.scoring_type_2');

        $arr_bonus = array();
        $arr_bonus['1'] = trans('config.bonus_type_1');
        $arr_bonus['2'] = trans('config.bonus_type_2');
        $arr_bonus['3'] = trans('config.bonus_type_3');
        $config = $this->config_model->checkConfig($this->id_shop);
        if (count($config) > 0) {//vào để cập nhật dữ liệu
            return view('Admin.config.edit', compact('admin_local', 'arr_type', 'config', 'arr_bonus'));
        } else {//lần đầu vào 
            return view('Admin.config.index', compact('admin_local', 'arr_type', 'arr_bonus'));
        }
    }

    public function postConfig(ConfigRequest $request) {
        $data = $request->input();
        $keyStr = "scoring,block,exchange,pointShare";
        $config = $this->config_model->checkConfig($this->id_shop);
        $hashcode =  $this->getHashcode();
        if (count($config) > 0) {//vào để cập nhật dữ liệu
            $data_update = $this->getInput($data, $keyStr, $status = 1);
            $data_update['hashcode'] = $hashcode;
            $this->config_model->UpdateData($this->id_shop, $data_update);
            $data_update['id_shop'] = $this->id_shop;          
            $data_update['created_at'] = time();
            $data_update['type'] = 1; //cấu hình mặc định
            $this->config_history_model->insertData($data_update);
        } else {//lần đầu vào 
            $data_insert = $this->getInput($data, $keyStr, $status = 1, time());
            $data_insert['id_shop'] = $this->id_shop;
            $data_insert['hashcode'] = $hashcode;
            $this->config_model->insertData($data_insert);
            $data_insert['type'] = 1; //cấu hình mặc định
            $this->config_history_model->insertData($data_insert);
        }
        return redirect('/' . $this->admin_local . '/cau-hinh-mac-dinh');
    }
	
//Rating
    public function rating() {
        $admin_local = $this->admin_local;
        $arr_type = array();
        $arr_type['1'] = trans('config.scoring_type_1');
        $arr_type['2'] = trans('config.scoring_type_2');

        $arr_bonus = array();
        $arr_bonus['1'] = trans('config.bonus_type_1');
        $arr_bonus['2'] = trans('config.bonus_type_2');
        $arr_bonus['3'] = trans('config.bonus_type_3');
        $config = $this->config_model->checkConfig($this->id_shop);
        if (count($config) > 0) {//vào để cập nhật dữ liệu
            return view('Admin.config.rating', compact('admin_local', 'arr_type', 'config', 'arr_bonus'));
        } else {//lần đầu vào 
            return view('Admin.config.rating', compact('admin_local', 'arr_type', 'arr_bonus'));
        }
    }
	public function postRating(Request $request) {
		$data = $request->input();
		$keyStr = "rating_recensy_day_2,rating_recensy_day_3,rating_recensy_day_4,rating_frequency_day,rating_frequency_count_2,rating_frequency_count_3,rating_frequency_count_4,rating_monetary_day,rating_monetary_count_2,rating_monetary_count_3,rating_monetary_count_4";

		$data_rating = $this->getInput($data, $keyStr);
		$data_rating['rating_recensy_day_1'] = 0;
		$data_rating['rating_frequency_count_1'] = 0; 
		$data_rating['rating_monetary_count_1'] = 0;
		unset($data_rating['updated_at']);
		$config_rating = array();
		$config_rating['id_shop'] = $this->id_shop;
		$config_rating['hashcode'] = $this->getHashcode();
		$config_rating['value'] = json_encode($data_rating);
		$config_rating['created_at'] = time();
		$config_rating['updated_at'] = time();
		if($this->rating_model->insertData($config_rating)){
			return redirect('/' . $this->admin_local . '/cau-hinh-thanh-vien');
		}
	}
//END Rating
    public function info() {
        $admin_local = $this->admin_local;
        $config = $this->config_info_model->checkInfo($this->id_shop);
        if (count($config) > 0) {//vào để cập nhật dữ liệu
            return view('Admin.config.info-edit', compact('admin_local', 'config'));
        } else {//lần đầu vào 
            return view('Admin.config.info-create', compact('admin_local'));
        }
    }

    public function postInfo(ShopInfoRequest $request) {
        $data = $request->input();
        $keyStr = "sort_name,full_name,boss_name,email,mobile,address,introduct,price";
		       
        $config = $this->config_info_model->checkInfo($this->id_shop);
        if (count($config) > 0) {//vào để cập nhật dữ liệu
            $data_update = $this->getInput($data, $keyStr, $status = 1);
			if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK){
				$data_update['image'] = $this->uploadImages($_FILES);
			} 
            $this->config_info_model->UpdateData($this->id_shop, $data_update);
        } else {//lần đầu vào 
            $data_insert = $this->getInput($data, $keyStr, $status = 1, time());
            $data_insert['id_shop'] = $this->id_shop;
            $data_insert['hashcode'] = Session::get('login_adpc')['hashcode'];
			if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK){echo '123';
				$data_insert['image'] = $this->uploadImages($_FILES);
			}  
            $this->config_info_model->insertData($data_insert);
        }
        return redirect('/' . $this->admin_local . '/thong-tin-cua-hang');
    }

    public function campaign() {
        $admin_local = $this->admin_local;
        $arr_bonus = array();
        $arr_bonus['1'] = trans('config.bonus_type_1');
        $arr_bonus['2'] = trans('config.bonus_type_2');
        $arr_bonus['3'] = trans('config.bonus_type_3');
        return view('Admin.config.campaign-create', compact('admin_local', 'arr_bonus'));
    }

    public function postCampaign(CampaignRequest $request) {
        $data = $request->input();
        $keyStr = "campaign,start_date,finish_date,scoring,block,exchange,pointShare";
        $data_insert = $this->getInput($data, $keyStr, $status = 1, time());
        $data_insert['id_shop'] = $this->id_shop;
        $data_insert['hashcode'] =  $this->getHashcode();
        $data_insert['start_date'] = strtotime($data_insert['start_date']);
        $data_insert['finish_date'] = strtotime($data_insert['finish_date']);
        //check xem thời gian có phù hợp không?
        $listCampaignActive = $this->campaign_model->getListCampaignActive($this->id_shop);
        $check = true;
        foreach($listCampaignActive as $value){
            if(($data_insert['start_date'] > $value['start_date'] && $data_insert['start_date'] < $value['finish_date']) || ($data_insert['finish_date'] > $value['start_date'] && $data_insert['finish_date'] < $value['finish_date'])){
                $check = false;//thời gian đã chọn trong khoảng thời gian đã tồn tại chiến dịch trước đó
            }
        }
        if($check == false){
            echo "<script>alert('".trans('config.duplicate_campaign')."');</script>"; 
            echo "<script>location.href='".Asset('/'. $this->admin_local .'/tao-moi-chien-dich')."'</script>";
        }else{
            $this->campaign_model->insertData($data_insert);
            return redirect('/' . $this->admin_local . '/campaign');
        }
    }

    public function indexCampaign() {
        $admin_local = $this->admin_local;
        $campaign = $this->campaign_model->pagingSearch($this->numberPage(),$name='',$this->id_shop);
        return view('Admin.config.campaign-index', compact('admin_local', 'campaign'));
    }
    
    public function campaignView($hashcode){
        $admin_local = $this->admin_local;
        $campaign = $this->campaign_model->getCampaignByHashcode(base64_decode($hashcode),$this->id_shop);
		if(count($campaign) >0 ){
			$campaign['start_date'] = date("m/d/Y",$campaign['start_date']);
			$campaign['finish_date'] = date("m/d/Y",$campaign['finish_date']);
			$arr_bonus = array();
			$arr_bonus['1'] = trans('config.bonus_type_1');
			$arr_bonus['2'] = trans('config.bonus_type_2');
			$arr_bonus['3'] = trans('config.bonus_type_3');
			return view('Admin.config.campaign-view', compact('admin_local', 'campaign','arr_bonus'));
		}else{
			return view('errors.404');
		}

    }
    
    public function checkUsingCampaign(){//Lấy cấu hình áp dụng cho Shop tại thời điểm hiện tại
        $campaign = $this->campaign_model->getCampaignUsing($this->id_shop);
        $defaul = $this->config_model->checkConfig($this->id_shop);//thông  tin cấu hình mặc định của Shop
        if(count($campaign)>0){//có bản ghi trong bảng config (lưu campaign)
            $time_now = time();
            if($time_now > $campaign['start_date'] && $time_now < $campaign['finish_date']){//hiện tại nằm trong khoảng thời gian của chính dịch
                return $campaign;
            }else{//hiện tại không còn áp dụng campaign nữa, dùng cấu hình mặc định
                return $defaul;
            }
        }else{//không có trong bảng config=>chắc chắn dùng bảng default
            return $defaul;
        }
    }
	
	private function uploadImages($FILE) {
        if (isset($FILE["image"]) && $FILE["image"]["error"] == UPLOAD_ERR_OK) {
            $UploadDirectory = 'public/uploads/shop/avatar/';

            if ($FILE["image"]["size"] > 5242880) {
                return 'error2'; //die("File size is too big!");
            }

            //allowed file type Server side check
            switch (strtolower($FILE['image']['type'])) {
                //allowed file types
                case 'image/png':
                case 'image/gif':
                case 'image/jpeg':
                case 'image/pjpeg':
                    break;
                default:
                    return 'error3'; //die('Unsupported File!'); //output error
            }
            $File_Name = strtolower($FILE['image']['name']);
            $File_Ext = substr($File_Name, strrpos($File_Name, '.')); //get file extention
            $Random_Number = md5(time()); //Random number to be added to name.
            $NewFileName = $Random_Number . $File_Ext; //new file name
            if (move_uploaded_file($FILE['image']['tmp_name'], $UploadDirectory . $NewFileName)) {
                return $UploadDirectory . $NewFileName;
            } else {
                return 'error4';
            }
        }
    }
	
    public function getUsingCampaign(){
        echo "<pre>";
            print_r($this->checkUsingCampaign());
    }
    
    /*
     * *** RATING
     */
    
    /*
     * rating
     */
    public function getRatingList(){
        $admin_local = $this->admin_local;
        $searchForm = new RatingForm();
        // status
        $listStatus = $this->listStatusRating();
        return view('Admin.config.rating.list',  compact('admin_local','listStatus','searchForm'));
    }


    /*
     * anhth
     * get rating level
     */
    public function getRatingLevel(){
        $admin_local = $this->admin_local;
        $ratingTypeForm = new RatingTypeForm();
        /*
         * Danh sách các kiểu định mức level ( recensy , frequensy , monetary value )
         */
        $listTypeConfigRating =$this->configuration->listTypeRating;
        $commonStatus =$this->configuration->commonStatus;
        return view('Admin.config.ratingCustomer.level.view-rating-level',  compact('admin_local','listTypeConfigRating','commonStatus','ratingTypeForm'));
    }
    
    /*
     * anhth
     * post rating level
     */
    public function postRatingLevel(Request $request){
        $admin_local = $this->admin_local;
        $listTypeConfigRating =$this->configuration->listTypeRating;
        $commonStatus =$this->configuration->commonStatus;
        //$data = $request->input();
        $ratingTypeForm = new RatingTypeForm();
        /*
         * dữ liệu sau khi gán vào object
         */
        $ratingTypeForm = $this->copyDataRatingType($request,$ratingTypeForm);
        $error = $this->ratingTypeValidate->validate($ratingTypeForm);
        
        /*
         * Nếu validate có lỗi
         */
        if($error!=""){
            return view('Admin.config.ratingCustomer.level.view-rating-level',  compact('admin_local','listTypeConfigRating','commonStatus','ratingTypeForm','error'));
        }
        /*
         * Validate success
         */
        if($this->ratingTypeService->insertData($ratingTypeForm)){
            /*
             * Thêm mới thành công reset object
             */
            $success = trans('error.alert_insert_success');
            $ratingTypeForm = new RatingTypeForm();
            return view('Admin.config.ratingCustomer.level.view-rating-level',  compact('admin_local','listTypeConfigRating','commonStatus','ratingTypeForm','success'));
        }else{
            $error = trans('common.alert_insert_fail');
            return view('Admin.config.ratingCustomer.level.view-rating-level',  compact('admin_local','listTypeConfigRating','commonStatus','ratingTypeForm','error'));
        }
        
    }
    
    /*
     * cài đặt xếp hạng , thêm mới setup
     */
    public function getRatingSetupAdd(){
        $admin_local = $this->admin_local;
        $ratingConfigForm = new RatingConfigForm();
        // Danh sách xếp hạng
        $listRating =$this->configuration->listRating;
        return view('Admin.config.ratingCustomer.setup.add',  compact('admin_local','listRating','ratingConfigForm'));
    }

        public function copyDataRatingType(Request $request, RatingTypeForm $ratingTypeForm){
        $data = $request->input();
        $keyStr = "code,cycle,name,minValue,maxValue,status";
        $dataFormat = $this->getInput($data, $keyStr, null, time());
        $ratingTypeForm->setCode($dataFormat["code"]);
        $ratingTypeForm->setCycle($dataFormat["cycle"]);
        $ratingTypeForm->setName($dataFormat["name"]);
        $ratingTypeForm->setMinValue($dataFormat["minValue"]);
        $ratingTypeForm->setMaxValue($dataFormat["maxValue"]);
        $ratingTypeForm->setStatus($dataFormat["status"]);
        $ratingTypeForm->setCreatedDate($dataFormat["created_at"]);
        $ratingTypeForm->setUpdatedDate($dataFormat["updated_at"]);
        $ratingTypeForm->setShopId($this->id_shop);
        $ratingTypeForm->setHashcode($this->getHashcode());
        return $ratingTypeForm;
    }
}

?>