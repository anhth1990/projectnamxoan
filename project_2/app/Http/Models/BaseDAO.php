<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Services\LibService;
use Session;
class BaseDAO extends Model {
    protected $table;
    public $merchantFormSession;


    public function __construct($modelTable) {
        $this->table = $modelTable;
        if(Session::has("login_admin_merchant")){
            $this->merchantFormSession = Session::get("login_admin_merchant");
        }
    }

    public function findById($id) {
        try {
            return BaseDAO::select("*")->where("id", "=", $id)->first();
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }
    
    public function findByHashcode($hashcode,$status = null) {
        try {
            $data = BaseDAO::select("*")->where("hashcode", "=", $hashcode);
            if($status!=null){
                $data = $data->where("status", "=", $status);
            }
            $data = $data->where("status", "!=", env('COMMON_STATUS_DELETED'));
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }
    
    /*
     * eloquent save
     */
    public function saveResultId($data){
        $this->libService = new LibService();
        try {
            if(isset($data->id)&&$data->id!=null){
                $data->updated_at = time();
                $data->update();
            }else{
                $data->hashcode = $this->libService->getHashcode();
                $data->created_at = time();
                //$data->updated_at = null;
                $data->save();
            }
            
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }
    
    /*
     * eloquent save
     */
    public function deleteData($data){
        try {
            return $data->delete();;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }
    
    public function getObjByHashcode($hashcode){
        try {
            $data = BaseDAO::select('*');
            $data = $data->where('hashcode',  $hashcode);
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }
    
    public function logs_custom($content){
        error_log(date(env('DATE_FORMAT_Y_M_D'))." : ".$content." "."\n", 3, base_path('storage/logs/custom.log'));
    }

}

?>