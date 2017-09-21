<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\AnswerQuestionForm;
use App\Http\Models\AnswerQuestionDAO;

class AnswerDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_answers");
    }
    
    public function question()
    {
        return $this->hasMany(new AnswerQuestionDAO(),'answerId');
    }
    
    public function getList(AnswerQuestionForm $searchForm){
        try {
            $data = AnswerDAO::select('*');
            if($searchForm->getMerchantId()!=null)
                $data = $data->where('merchantId',  $searchForm->getMerchantId());
            if($searchForm->getStatus()!=null){
                $data = $data->where('status', '=' ,$searchForm->getStatus());
            }
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->orderBy('id', 'desc');
            if($searchForm->getPageSize()!=null)
                $data = $data->paginate($searchForm->getPageSize(), ['*'], 'page', $searchForm->getPageIndex());
            else
                $data = $data->get();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans("error.error_system"));
        }
    }
    
}

?>