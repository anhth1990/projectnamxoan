<?php

namespace App\Http\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\Order;
class RatingController extends Controller {

    public function __construct() {
		$this->order_model = new Order();
		$this->admin_local = trans('layout.local_admin');
        $this->id_shop = Session::get('login_adpc')['id'];
		
		
        $this->recensy_day_1 = 7;
        $this->recensy_day_2 = 14;
        $this->recensy_day_3 = 21;
		
		$this->frequency_day = 30;//là chu kỳ tính lượng giao dịch
		$this->frequency_count_1 = 3;//số lần mua hàng trong 1 đơn vị thời gian
		$this->frequency_count_2 = 2;//số lần mua hàng trong 1 đơn vị thời gian
		$this->frequency_count_3 = 1;//số lần mua hàng trong 1 đơn vị thời gian
		$this->frequency_count_4 = 0;//số lần mua hàng trong 1 đơn vị thời gian		
		
		$this->monetary_day = 30;//là chu kỳ tính số tiền giao dịch
		$this->monetary_count_1 = 5000000;//số tiền mua hàng trong 1 đơn vị thời gian
		$this->monetary_count_2 = 10000000;//số tiền mua hàng trong 1 đơn vị thời gian
		$this->monetary_count_3 = 20000000;//số tiền mua hàng trong 1 đơn vị thời gian
    }
    
	
    public function getRecensy($days){//định nghĩa R1,R2,R3,R4
		if($days < $this->recensy_day_1) return 'R1';//nhóm Recensy1, những thành viên mà thời gian mua gần nhất < 7 ngày
		else if($days >= $this->recensy_day_1 && $days < $this->recensy_day_2) return 'R2';//nhóm Recensy2, những thành viên mà thời gian mua gần nhất:7<= days < 14 ngày
		else if($days >= $this->recensy_day_2 && $days < $this->recensy_day_3) return 'R3';//nhóm Recensy3, những thành viên mà thời gian mua gần nhất: 14<= days < 21 ngày
		else return 'R4';//nhóm Recensy4, những thành viên mà thời gian mua gần nhất :days >= 21 ngày
	}
	
	public function getRecensyRating($shop_id, $user_id=1){// trả về hạng Recensy (R1,R2,R3,R4) cho user tương ứng với 1 shop
		$data = $this->order_model->getLastDayBuy($shop_id, $user_id);
        $today = strtotime(date( 'd-m-Y', time()));
		$result = ($today-$data['date_buy']) / 86400;
		return $this->getRecensy($result);
	}
	
	    
    public function getFrequency($count){//ĐỊnh nghĩa F1,F2,F3,F4
		if($count >= $this->frequency_count_1) return 'F1';//
		else if($count == $this->frequency_count_2) return 'F2';//
		else if($count == $this->frequency_count_3) return 'F3';//
		else return 'F4';//
	}
	
	public function getFrequencyRating($shop_id, $user_id=1){// trả về hạng Frequency (F1,F2,F3,F4) cho user tương ứng với 1 shop
		$data = $this->order_model->getCountFrequency($shop_id, $user_id, $this->frequency_day);
		return $this->getFrequency($data);
	}
	
    public function getMonetary($money){//ĐỊnh nghĩa M1,M2,M3,M4
		if($money >= $this->monetary_count_3) return 'M1';//
		else if($money >= $this->monetary_count_2 && $money < $this->monetary_count_3) return 'M2';//
		else if($money >= $this->monetary_count_1 && $money < $this->monetary_count_2) return 'M3';//
		else return 'M4';//
	}
	
	public function getMonetaryRating($shop_id, $user_id=1){// trả về hạng Monetary (M1,M2,M3,M4) cho user tương ứng với 1 shop
		$data = $this->order_model->getCountMonetary($shop_id, $user_id, $this->monetary_day);
		return $this->getMonetary($data['price']);
	}
	
	public function getRatingUser($user_id=1){
		$rating = 0;//giá trị mặc định
		$recensy = $this->getRecensyRating($this->id_shop, $user_id);
		$frequency = $this->getFrequencyRating($this->id_shop, $user_id);
		$monetary = $this->getMonetaryRating($this->id_shop, $user_id);
		if($recensy == "R1" && $frequency == "F1" && $monetary == "M1") $rating = 1;//Best Customers - Nhóm khách hàng Vip
		else if($frequency == "F1") $rating = 2;//Loyal Customers - Nhóm khách hàng trung thành
		else if($recensy == "R1" && $frequency == "F1" && $monetary == "M1") $rating = 3;//Best Customers - Nhóm khách hàng Vip
		else if($monetary == "M1") $rating = 4;//Big Spenders - Nhóm khách hào phóng
		else if($frequency == "F1" && $monetary == "M4") $rating = 5;//Loyal Jose - Nhóm khách hàng tiềm năm
		else if($recensy == "R4" && $frequency == "F1" && $monetary == "M4") $rating = 6;//Lost Customers - Nhóm khách hàng thất thoát
		else if($recensy == "R3" && $frequency == "F1" && $monetary == "M1") $rating = 7;//Almost lost - Nhóm gần thất thoát
		else if($frequency == "F4" && $monetary == "M1") $rating = 8;//Splurgers - Nhóm khách hàng xa xỉ
		else if($recensy == "R4" && $frequency == "F4" && $monetary == "M4") $rating = 9;//Deadbeats - Nhóm khách hàng Chết
		return $rating;
	}
}

?>