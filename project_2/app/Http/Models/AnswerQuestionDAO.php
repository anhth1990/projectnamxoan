<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\AnswerQuestionForm;
use App\Http\Models\AnswerDAO;
use App\Http\Models\AnswerUserQuestionDAO;

class AnswerQuestionDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_answer_questions");
    }
    
    public function answer()
    {
        return $this->belongsTo(new AnswerDAO(),'answerId','id');
    }
    
    public function userQuestion()
    {
        return $this->hasMany(new AnswerUserQuestionDAO(),'questionId');
    }
    
    
    public function getList(AnswerQuestionForm $searchForm){
        try {
            $data = AnswerQuestionDAO::select('*');
            if($searchForm->getUserId()!=null)
                $data = $data->where('userId',  $searchForm->getUserId());
            if($searchForm->getQuestionId()!=null)
                $data = $data->where('questionId',  $searchForm->getQuestionId());
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
    
    public function getDataByHashcode($hashcode) {
        try {
            $data = AnswerQuestionDAO::with(['answer'])->select('*');
            $data = $data->where('hashcode','=', $hashcode);
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans('error.error_system'));
        }
    }
    
}

?>