<?php

namespace App\Http\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\Point;
class PromotionController extends Controller {

    public function __construct() {
        $this->point_model = new Point();
        $this->admin_local = trans('layout.local_admin');
        $this->id_shop = Session::get('login_adpc')['id'];
    }
    
    public function checkCode(Request $request){
        $data = $request->input();
        $value = $this->point_model->getPoint($data['promotion']);
        if(count($value)>0){
            //echo number_format($value['value_code'].'000');
            echo $value['value_code'].'000';
        }else{
            echo "fails";
        }
    }
}

?>