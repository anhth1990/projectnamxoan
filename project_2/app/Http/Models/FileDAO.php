<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\FileForm;

class FileDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_files");
    }
    
    public function getList(FileForm $searchForm){
        try {
            $data = FileDAO::select('*');
            if($searchForm->getMerchantId()!=null)
                $data = $data->where('merchantId',  $searchForm->getMerchantId());
            if($searchForm->getName()!=null){
                $data = $data->where('name', 'like', '%' . $searchForm->getName() . '%');
            }
            if($searchForm->getStatus()!=null){
                $data = $data->where('status',  $searchForm->getStatus());
            }
            if($searchForm->getType()!=null){
                $data = $data->where('type',  $searchForm->getType());
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