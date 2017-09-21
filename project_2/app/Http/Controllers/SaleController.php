<?php

namespace App\Http\Controllers;
use Excel;
use PDF;
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
class SaleController extends Controller {

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
        $admin_local = $this->admin_local;
		$start_date = date( 'Y-m-d', strtotime('-1 month'));
		if(isset($_GET['start_date'])){
			$start_date = $_GET['start_date'];
		}	
		$end_date = date( 'Y-m-d', strtotime('today'));
		if(isset($_GET['end_date'])){
			$end_date = $_GET['end_date'];
		}
		$mobile_search = null;
		if(isset($_GET['mobile_search']) && $_GET['mobile_search'] !=0){
			$mobile_search = $_GET['mobile_search'];
		}
		
		$list_id_user = $this->order_model->getListIdUser($this->id_shop);
        $arr_id_user = array(); //mảng các id_user của shop, lấy từ bảng order
        foreach($list_id_user as $user){
            $arr_id_user[] = $user['id_user'];
        }
		$list_mobile = $this->user_model->getMobile($arr_id_user);
		$arr_mobile = array(); //mảng các số điện thoại (của khách hàng) đã từng mua hàng của Shop
        $arr_mobile[0] = trans('user.mobile');
        foreach($list_mobile as $mobile){
            $arr_mobile[$mobile['mobile']] = $mobile['mobile'];
        }
		$start = strtotime($start_date);
		$end = strtotime($end_date);//echo $start."<br>".$end;die;
		$orders = $this->order_model->getOrderByIdShop($this->numberPage(),$this->id_shop,$start,$end,$mobile_search);

		//$arr_user_info = $this->arrUserInfo();//Mảng chứa email, sđt, tên của các user đã mua hàng của Shop
		
		//Biểu đồ
		$start_date_chart = date( 'Y-m-d', strtotime('-1 month'));
		if(isset($_GET['start_date'])){
			$start_date_chart = $_GET['start_date'];
		}	
		$end_date_chart = date( 'Y-m-d', strtotime('today'));
		if(isset($_GET['end_date'])){
			$end_date_chart = $_GET['end_date'];
		}
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
        return view('Admin.sales.index', compact('admin_local','start_date','end_date','arr_mobile','orders','mobile_search','json_price','json_profit'));
    }
	
	public function chart(){
		$admin_local = $this->admin_local;
		$start_date = date( 'Y-m-d', strtotime('-1 month'));
		if(isset($_GET['start_date'])){
			$start_date = $_GET['start_date'];
		}	
		$end_date = date( 'Y-m-d', strtotime('today'));
		if(isset($_GET['end_date'])){
			$end_date = $_GET['end_date'];
		}		
		
		$start = strtotime($start_date);
		$end = strtotime($end_date);
		$mobile_search = null;
		$charts = $this->order_model->getPageChart($this->id_shop,$start,$end,$mobile_search);
		
		$total_price = 0;
		$arr_read = array();//mảng giá
		foreach ($this->getDatesFromRange($start_date, $end_date) as $dates) {
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
		foreach ($this->getDatesFromRange($start_date, $end_date) as $dates) {
			$str_dates = strtotime($dates)."000";
			$arr_profit[$str_dates] = 0;
			foreach ($charts as $chart) {
				if (strtotime($dates) === strtotime($chart->dates)) {
					$arr_profit[$str_dates] = $chart->profit;
					$total_profit += $chart->profit;
				}
			}
		}
		$json_profit = json_encode($arr_profit);
		
		
		return view('Admin.sales.chart', compact('admin_local','json_price','json_profit','total_price','total_profit','start_date','end_date'));
	}
	
	public function exportExcelPrice(){//xuất file excel
		//=============================================================================================
		Excel::create('Yupax-Export', function ($excel) {
			$excel->sheet('Export', function ($sheet) {
				$admin_local = $this->admin_local;
				$start_date = date( 'Y-m-d', strtotime('-1 month'));
				if(isset($_GET['start_date'])){
					$start_date = $_GET['start_date'];
				}	
				$end_date = date( 'Y-m-d', strtotime('today'));
				if(isset($_GET['end_date'])){
					$end_date = $_GET['end_date'];
				}		
				
				$start = strtotime($start_date);
				$end = strtotime($end_date);
				$mobile_search = null;
				$charts = $this->order_model->getPageChart($this->id_shop,$start,$end,$mobile_search);
		
				$arr_price = array();
				$arr_date = array();
				$total_price = 0;
				foreach ($this->getDatesFromRange($start_date, $end_date) as $dates) {
					$str_dates = date('d-m-Y', strtotime($dates));
					//$str_dates = $dates;
					$arr_price[$str_dates] = 0;
					$arr_date[$str_dates] = $str_dates;
					foreach ($charts as $chart) {
						if (strtotime($dates) === strtotime($chart->dates)) {
							$arr_price[$str_dates] = number_format($chart->price);
							$total_price += $chart->price;
						}
					}
				}		
				
				$arr_profit = array();
				$total_profit = 0;
				foreach ($this->getDatesFromRange($start_date, $end_date) as $dates) {
					$str_dates = date('d-m-Y', strtotime($dates));
					//$str_dates = $dates;
					$arr_profit[$str_dates] = 0;
					foreach ($charts as $chart) {
						if (strtotime($dates) === strtotime($chart->dates)) {
							$arr_profit[$str_dates] = number_format($chart->profit);
							$total_profit += $chart->profit;
						}
					}
				}
		
				// first row styling and writing content
				$sheet->mergeCells('A1:D1');
				$sheet->row(1, function ($row) {
					$row->setFontFamily('Time new roman');
					$row->setFontSize(30);
				});
				$sheet->row(1, array(trans('sales.rep_report_by_date')));

			//hang thu 2
			$sheet->mergeCells('A2:D2');
			$sheet->row(2, function ($row) {
				$row->setFontFamily('Time new roman');
				$row->setFontSize(20);
			});
			$sheet->row(2, array($start_date." : ".$end_date));				
			// getting data to display - in my case only one record
			
			$reports = array();
			$i=1;
			foreach($arr_date as $key=>$date){
				$reports[$key]['#'] = $i;
				$reports[$key][trans('sales.date_buy')] = $key;//$chart['dates'];
				$reports[$key][trans('sales.price')] = $arr_price[$date];
				$reports[$key][trans('sales.profit')] = $arr_profit[$date];
				$i++;
			}
			$key_array = array();
			$key_array[0] = '#';
			$key_array[1] = trans('sales.date_buy');
			$key_array[2] = trans('sales.price');
			$key_array[3] = trans('sales.profit');
			// setting column names for data - you can of course set it manually
				$sheet->appendRow($key_array); // column names

				// getting last row number (the one we already filled and setting it to bold
				$sheet->row($sheet->getHighestRow(), function ($row) {
					$row->setFontWeight('bold');
				});

				// putting users data as next rows
				foreach ($reports as $report) {
					$sheet->appendRow($report);
				}
				$total_array = array();
				$total_array[0] = '';
				$total_array[1] = 'Tổng cộng';
				$total_array[2] = number_format($total_price);
				$total_array[3] = number_format($total_profit);
				$sheet->appendRow($total_array); // cột chứa tổng thu, lợi nhuận
				$sheet->row($sheet->getHighestRow(), function ($row) {
					$row->setFontWeight('bold');
				});
			});

		})->export('xls');
		//=============================================================================================
	}
	
	public function exportPdfPrice(){//xuất file pdf
		$start_date = date( 'Y-m-d', strtotime('-1 month'));
		if(isset($_GET['start_date'])){
			$start_date = $_GET['start_date'];
		}	
		$end_date = date( 'Y-m-d', strtotime('today'));
		if(isset($_GET['end_date'])){
			$end_date = $_GET['end_date'];
		}
		$start = strtotime($start_date);
		$end = strtotime($end_date);
		$mobile_search = null;
		$charts = $this->order_model->getPageChart($this->id_shop,$start,$end,$mobile_search);

		$arr_price = array();
		$arr_date = array();
		$total_price = 0;
		foreach ($this->getDatesFromRange($start_date, $end_date) as $dates) {
			$str_dates = date('d-m-Y', strtotime($dates));
			//$str_dates = $dates;
			$arr_price[$str_dates] = 0;
			$arr_date[$str_dates] = $str_dates;
			foreach ($charts as $chart) {
				if (strtotime($dates) === strtotime($chart->dates)) {
					$arr_price[$str_dates] = number_format($chart->price);
					$total_price += $chart->price;
				}
			}
		}		
		
		$arr_profit = array();
		$total_profit = 0;
		foreach ($this->getDatesFromRange($start_date, $end_date) as $dates) {
			$str_dates = date('d-m-Y', strtotime($dates));
			//$str_dates = $dates;
			$arr_profit[$str_dates] = 0;
			foreach ($charts as $chart) {
				if (strtotime($dates) === strtotime($chart->dates)) {
					$arr_profit[$str_dates] = number_format($chart->profit);
					$total_profit += $chart->profit;
				}
			}
		}
		
		$start = date('d-m-Y', strtotime($start_date));
		$end = date('d-m-Y', strtotime($end_date));
		$html = '<html lang="en" class="no-js">
                <!--<![endif]-->
                <!-- BEGIN HEAD -->
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>Yupax - Báo cáo doanh thu, lợi nhuận</title>
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta content="width=device-width, initial-scale=1" name="viewport"/>
                <meta content="" name="description"/>
                <meta content="" name="author"/>
                <style>
                table {
                    border-spacing: 0;
                    border-collapse: collapse;
                }
                .table-scrollable {
                    width: 100%;
                    overflow-x: auto;
                    overflow-y: hidden;
                    border: 1px solid #dddddd;
                    margin: 10px 0 !important;
                }
                .table-scrollable > .table-bordered {
                    border: 0;
                }
                .table-scrollable > .table {
                    width: 100% !important;
                    margin: 0 !important;
                    margin-bottom: 0;
                    background-color: #fff;
                        max-width: 100%;
                }
                .table-advance thead {
                    color: #999;
                }
                .table-scrollable > .table-bordered > thead > tr:last-child > th, .table-scrollable > .table-bordered > tbody > tr:last-child > th, .table-scrollable > .table-bordered > tfoot > tr:last-child > th, .table-scrollable > .table-bordered > thead > tr:last-child > td, .table-scrollable > .table-bordered > tbody > tr:last-child > td, .table-scrollable > .table-bordered > tfoot > tr:last-child > td {
                    border-bottom: 0;
                }
                .table-scrollable > .table-bordered > thead > tr > th:first-child, .table-scrollable > .table-bordered > tbody > tr > th:first-child, .table-scrollable > .table-bordered > tfoot > tr > th:first-child, .table-scrollable > .table-bordered > thead > tr > td:first-child, .table-scrollable > .table-bordered > tbody > tr > td:first-child, .table-scrollable > .table-bordered > tfoot > tr > td:first-child {
                    border-left: 0;
                }
                .table > caption + thead > tr:first-child > th, .table > colgroup + thead > tr:first-child > th, .table > thead:first-child > tr:first-child > th, .table > caption + thead > tr:first-child > td, .table > colgroup + thead > tr:first-child > td, .table > thead:first-child > tr:first-child > td {
                    border-top: 0;
                }
                .table-scrollable > .table > thead > tr > th, .table-scrollable > .table > tbody > tr > th, .table-scrollable > .table > tfoot > tr > th, .table-scrollable > .table > tfoot > tr > th, .table-scrollable > .table > tfoot > tr > td {
                    white-space: nowrap;
                }
                .table-advance thead tr th {
                    background-color: #DDD;
                    font-size: 10px;
                    font-weight: 400;
                    color: #666;
                }
                .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
                    padding: 8px;
                    line-height: 1.42857143;
                    vertical-align: top;
                    border-top: 1px solid #ddd;
                }
                .table > tbody > tr > td {
                        text-align: center;
                        font-size: 10px;
                }
                body { font-family: DejaVu Sans, sans-serif; }
                </style>
                </head>
                <div style="text-align:center">'
                    .trans('sales.title_report_pdf').'<br>'
                    .trans('sales.date_report_pdf').': '.$start.' : '.$end.'<br>'
                    .trans('sales.total_price').': <b>'.number_format($total_price).' vnđ</b><br>'
                    .trans('sales.total_profit').': <b>'.number_format($total_profit).' vnđ</b>
                        
                </div>
                <div class="table-scrollable">
                    
                        <table class="table table-striped table-bordered table-advance table-hover">
                                <thead>
                                        <tr>
                                                <th>#</th>
                                                <th>'.trans('sales.date_buy').'</th>
                                                <th>'.trans('sales.price').'</th>
                                                <th>'.trans('sales.profit').'</th>
                                        </tr>
                                </thead>';
							$i=0;	
                            foreach($arr_date as $key=>$date){   
								$i++;
                                $html .='<tbody>
                                        <tr class="position_148">
                                            <td>'.$i.'</td>
                                            <td>'.$date.'</td>
                                            <td>'.$arr_price[$date].'</td>
                                            <td>'.$arr_profit[$date].'</td>
                                    </tr>
                            </tbody>';
                            }
                            $html .= '<tbody class="main-id-151" style="display:none;"></tbody>
                        </table>

                </div>
                </body></html>';
                //echo $html;die;
				
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html, 'UTF-8');
				//$pdf->render();
                return $pdf->stream(); 
				
                
				/*$pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html, 'UTF-8');				
				$pdf->save('public/uploads/temp/csv/myfile.pdf');
				$pdf->download('public/uploads/temp/csv/myfile.pdf');*/
				
	}
	public function exportExcelOrder(){//xuất file excel theo những lần mua hàng
		Excel::create('Yupax-Export', function ($excel) {
			$excel->sheet('Export', function ($sheet) {
				$admin_local = $this->admin_local;
				$start_date = date( 'Y-m-d', strtotime('-1 month'));
				if(isset($_GET['start_date'])){
					$start_date = $_GET['start_date'];
				}	
				$end_date = date( 'Y-m-d', strtotime('today'));
				if(isset($_GET['end_date'])){
					$end_date = $_GET['end_date'];
				}
				
				$mobile_search = null;
				if(isset($_GET['mobile_search']) && $_GET['mobile_search'] !=0){
					$mobile_search = $_GET['mobile_search'];
				}
				
				$start = strtotime($start_date);
				$end = strtotime($end_date);
				$mobile_search = null;
				$orders = $this->order_model->getOrderByIdShop(null,$this->id_shop,$start,$end,$mobile_search);
				$arr_exports = array();
				$price = 0;
				$profit = 0;
				foreach($orders as $key=>$order){
					$key++;
					$arr = array();
					$arr['#'] = $key;
					$arr['fullname'] = $order->u_fullname;
					$arr['mobile'] = $order->u_mobile;
					$arr['date'] = date( 'd-m-Y', $order->date_buy);
					$arr['price'] = number_format($order['price']);
					$arr['profit'] = number_format($order['profit']);
					$arr['bonus_points'] = $order['bonus_points'];
					$arr_exports[] = $arr;
					$price += $order['price'];
					$profit += $order['profit'];
				}
	
				// first row styling and writing content
				$sheet->mergeCells('A1:G1');
				$sheet->row(1, function ($row) {
					$row->setFontFamily('Time new roman');
					$row->setFontSize(30);
				});
				$sheet->row(1, array(trans('sales.rep_report_by_date')));

			//hang thu 2
			$sheet->mergeCells('A2:G2');
			$sheet->row(2, function ($row) {
				$row->setFontFamily('Time new roman');
				$row->setFontSize(20);
			});
			$sheet->row(2, array($start_date." : ".$end_date));				
			// getting data to display - in my case only one record
			
			$reports = array();
			$key_array = array();
			$key_array[0] = '#';
			$key_array[1] = trans('sales.ex_fullname');
			$key_array[2] = trans('sales.ex_mobile');
			$key_array[3] = trans('sales.ex_date');
			$key_array[4] = trans('sales.ex_price');
			$key_array[5] = trans('sales.ex_profit');
			$key_array[6] = trans('sales.ex_point');

			// setting column names for data - you can of course set it manually
				$sheet->appendRow($key_array); // column names

				// getting last row number (the one we already filled and setting it to bold
				$sheet->row($sheet->getHighestRow(), function ($row) {
					$row->setFontWeight('bold');
				});

				// putting users data as next rows
				foreach ($arr_exports as $arr_export) {
					$sheet->appendRow($arr_export);
				}
				$total_array = array();
				$total_array[0] = '';
				$total_array[1] = '';
				$total_array[2] = '';
				$total_array[3] = 'Tổng cộng';
				$total_array[4] = number_format($price);
				$total_array[5] = number_format($profit);
				$sheet->appendRow($total_array); 
				$sheet->row($sheet->getHighestRow(), function ($row) {
					$row->setFontWeight('bold');
				});
			});

		})->export('xls');
	}
	
	public function exportPdfOrder(){//xuất file pdf theo những lần mua hàng
		$start_date = date( 'Y-m-d', strtotime('-1 month'));
		if(isset($_GET['start_date'])){
			$start_date = $_GET['start_date'];
		}	
		$end_date = date( 'Y-m-d', strtotime('today'));
		if(isset($_GET['end_date'])){
			$end_date = $_GET['end_date'];
		}
		$start = strtotime($start_date);
		$end = strtotime($end_date);
		$mobile_search = null;
		$orders = $this->order_model->getOrderByIdShop($this->numberPage(),$this->id_shop,$start,$end,$mobile_search);
	
		$start = date('d-m-Y', strtotime($start_date));
		$end = date('d-m-Y', strtotime($end_date));
		$html = '<html lang="en" class="no-js">
                <!--<![endif]-->
                <!-- BEGIN HEAD -->
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>Yupax - Báo cáo doanh thu, lợi nhuận</title>
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta content="width=device-width, initial-scale=1" name="viewport"/>
                <meta content="" name="description"/>
                <meta content="" name="author"/>
                <style>
                table {
                    border-spacing: 0;
                    border-collapse: collapse;
                }
                .table-scrollable {
                    width: 100%;
                    overflow-x: auto;
                    overflow-y: hidden;
                    border: 1px solid #dddddd;
                    margin: 10px 0 !important;
                }
                .table-scrollable > .table-bordered {
                    border: 0;
                }
                .table-scrollable > .table {
                    width: 100% !important;
                    margin: 0 !important;
                    margin-bottom: 0;
                    background-color: #fff;
                        max-width: 100%;
                }
                .table-advance thead {
                    color: #999;
                }
                .table-scrollable > .table-bordered > thead > tr:last-child > th, .table-scrollable > .table-bordered > tbody > tr:last-child > th, .table-scrollable > .table-bordered > tfoot > tr:last-child > th, .table-scrollable > .table-bordered > thead > tr:last-child > td, .table-scrollable > .table-bordered > tbody > tr:last-child > td, .table-scrollable > .table-bordered > tfoot > tr:last-child > td {
                    border-bottom: 0;
                }
                .table-scrollable > .table-bordered > thead > tr > th:first-child, .table-scrollable > .table-bordered > tbody > tr > th:first-child, .table-scrollable > .table-bordered > tfoot > tr > th:first-child, .table-scrollable > .table-bordered > thead > tr > td:first-child, .table-scrollable > .table-bordered > tbody > tr > td:first-child, .table-scrollable > .table-bordered > tfoot > tr > td:first-child {
                    border-left: 0;
                }
                .table > caption + thead > tr:first-child > th, .table > colgroup + thead > tr:first-child > th, .table > thead:first-child > tr:first-child > th, .table > caption + thead > tr:first-child > td, .table > colgroup + thead > tr:first-child > td, .table > thead:first-child > tr:first-child > td {
                    border-top: 0;
                }
                .table-scrollable > .table > thead > tr > th, .table-scrollable > .table > tbody > tr > th, .table-scrollable > .table > tfoot > tr > th, .table-scrollable > .table > tfoot > tr > th, .table-scrollable > .table > tfoot > tr > td {
                    white-space: nowrap;
                }
                .table-advance thead tr th {
                    background-color: #DDD;
                    font-size: 10px;
                    font-weight: 400;
                    color: #666;
                }
                .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
                    padding: 8px;
                    line-height: 1.42857143;
                    vertical-align: top;
                    border-top: 1px solid #ddd;
                }
                .table > tbody > tr > td {
                        text-align: center;
                        font-size: 10px;
                }
                body { font-family: DejaVu Sans, sans-serif; }
                </style>
                </head>
                <div style="text-align:center">'
                    .trans('sales.title_report_pdf').'<br>'
                    .trans('sales.date_report_pdf').': '.$start.' : '.$end.'<br>       
                </div>
                <div class="table-scrollable">
                    
                        <table class="table table-striped table-bordered table-advance table-hover">
                                <thead>
                                        <tr>
                                                <th>#</th>
                                                <th>'.trans('sales.ex_fullname').'</th>
                                                <th>'.trans('sales.ex_mobile').'</th>
                                                <th>'.trans('sales.ex_date').'</th>
                                                <th>'.trans('sales.ex_price').'</th>
                                                <th>'.trans('sales.ex_profit').'</th>
                                                <th>'.trans('sales.ex_point').'</th>
                                        </tr>
                                </thead>';
							$i=0;	
							$total_price = 0;
							$total_profit = 0;
                            foreach($orders as $order){  
								$total_price += $order['price'];
								$total_profit += $order['profit'];
								$i++;
                                $html .='<tbody>
                                        <tr class="position_148">
                                            <td>'.$i.'</td>
                                            <td>'.$order->u_fullname.'</td>
                                            <td>'.$order->u_mobile.'</td>
                                            <td>'.date( 'd-m-Y', $order->date_buy).'</td>
                                            <td>'.number_format($order['price']).'</td>
                                            <td>'.number_format($order['profit']).'</td>
                                            <td>'.$order['bonus_points'].'</td>
                                    </tr>
                            </tbody>';
                            }
                            $html .= '<tbody class="main-id-151" style="display:none;"></tbody>
                        </table>

                </div>
				<div style="text-align:center">'
                    .trans('sales.total_price').': <b>'.number_format($total_price).' vnđ</b><br>'
                    .trans('sales.total_profit').': <b>'.number_format($total_profit).' vnđ</b>
                        
                </div>
                </body></html>';
                //echo $html;die;
				
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html, 'UTF-8');
				//$pdf->render();
                return $pdf->stream(); 
				
                
				/*$pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($html, 'UTF-8');				
				$pdf->save('public/uploads/temp/csv/myfile.pdf');
				$pdf->download('public/uploads/temp/csv/myfile.pdf');*/
	}
	
	
	
	
	private function getDatesFromRange($start, $end){
		$dates = array($start);
		while(end($dates) < $end){
			$dates[] = date('Y-m-d', strtotime(end($dates).' +1 day'));
		}
		return $dates;
	} 
	
	private function arrUserInfo(){
		$result = array();	
		$list_id_user = $this->order_model->getListIdUser($this->id_shop);
        $arr_id_user = array(); //mảng các id_user của shop, lấy từ bảng order
        foreach($list_id_user as $user){
            $arr_id_user[] = $user['id_user'];
        }
		$list_mobile = $this->user_model->getFullname($arr_id_user);
        foreach($list_mobile as $mobile){
            $result[$mobile['id']]['mobile'] = $mobile['mobile'];
            $result[$mobile['id']]['email'] = $mobile['mobile'];
            $result[$mobile['id']]['fullname'] = $mobile['fullname'];
            $result[$mobile['id']]['hashcode'] = $mobile['hashcode'];
        }
		return $result;
	}
}

?>