<?php namespace App\Http\Controllers;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;
use App\Http\Models\Order;
use App\Http\Models\Shop;
class DashboardController extends Controller{
	public function __construct(){
		$this->order_model = new Order();
		$this->shop_model = new Shop();
		$this->admin_local = trans('layout.local_admin');
        $this->id_shop = Session::get('login_adpc')['id'];
	}
	public function index(){
		$admin_local = $this->admin_local;
		$count_order = $this->order_model->getCountOrder($this->id_shop);
		$count_user = count($this->order_model->getCountUser($this->id_shop));
		$total = $this->order_model->getTotalPriceProfit($this->id_shop);
		//Biểu đồ
		$start_date_chart = date( 'Y-m-d', strtotime('-1 week'));
		if(isset($_GET['start_date'])){
			$start_date_chart = $_GET['start_date'];
		}	
		$end_date_chart = date( 'Y-m-d', strtotime('today'));
		if(isset($_GET['end_date'])){
			$end_date_chart = $_GET['end_date'];
		}
		$start = strtotime($start_date_chart);
		$end = strtotime($end_date_chart);
		$mobile_search = null;
		$charts = $this->order_model->getPageChart($this->id_shop,$start,$end,$mobile_search);
		$total_price = 0;
		$arr_read = array();//mảng giá
		foreach ($this->getDatesFromRange($start_date_chart, $end_date_chart) as $dates) {
			$str_dates = strtotime($dates)."000";
			$arr_read[$str_dates] = 0;
			foreach ($charts as $chart) {
				if (strtotime($dates) === strtotime($chart->dates)) {
					$arr_read[$str_dates] = $chart->price;
					$total_price += $chart->price;
				}
			}
		}
		$json_price = json_encode($arr_read);

		
		$total_profit = 0;
		$arr_profit = array();//mảng lợi nhuận
		foreach ($this->getDatesFromRange($start_date_chart, $end_date_chart) as $dates) {
			$str_dates = strtotime($dates)."000";
			$arr_profit[$str_dates] = 0;
			foreach ($charts as $chart) {
				if (strtotime($dates) === strtotime($chart->dates)) {
					$arr_profit[$str_dates] = $chart->profit;
					$total_profit += $chart->profit;
				}
			}
		}
		$json_profit = json_encode($arr_profit);//echo $json_price."<br>".$json_profit;die;
		//End Biểu đồ
		return view('Admin.dashboard.index',compact('admin_local','count_order','count_user','total','json_price','json_profit'));		
	}
	private function getDatesFromRange($start, $end){
		$dates = array($start);
		while(end($dates) < $end){
			$dates[] = date('Y-m-d', strtotime(end($dates).' +1 day'));
		}
		return $dates;
	} 
	
	public function changePass(){
		$admin_local = $this->admin_local;
		return view('Admin.dashboard.changepass',compact('admin_local'));	
	}
	
	public function postChangePass(AccountRequest $request){
		$input = $request->input();
		$keyStr = "old_pass,password,password_confirmation";
        $data = $this->getInput($input, $keyStr);//dữ liệu sẽ lưu vào bảng Order
		//kiểm tra mật khẩu
		if($this->hashMd5($data['old_pass']) == Session::get('login_adpc')['password']){
			$data_update = array();
			$data_update['password'] = $this->hashMd5($data['password']);
			$this->shop_model->UpdateData(Session::get('login_adpc')['id'], $data_update);
			echo "<script>alert('".trans('dashboard.password_success')."');</script>"; 
			echo "<script>location.href='".Asset($this->admin_local."/thong-tin-cua-hang")."'</script>";
		}else{//mật khẩu không đúng
			echo "<script>alert('".trans('dashboard.password_err')."');</script>"; 
			echo "<script>location.href='".Asset($this->admin_local."/doi-mat-khau")."'</script>";
		}
	}
}

?>