<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\TransactionForm;

class TransactionDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_transactions");
    }
    
    public function getTransactionByIdRef($idRef){
        try {
            $data = TransactionDAO::select('*');
            $data = $data->where('idRef', $idRef);
            $data = $data->first();
            return $data;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
    
    public function getList(TransactionForm $searchForm){
        
        try {
            $data = TransactionDAO::select('*');
            if($searchForm->getType()!=null){
                $data = $data->where('type', $searchForm->getType());
            }
            if($searchForm->getStatus()!=null){
                $data = $data->where('status',  $searchForm->getStatus());
            }
            if($searchForm->getUserDetailId()!=null){
                $data = $data->where('userDetailId',  $searchForm->getUserDetailId());
            }
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            
            if($searchForm->getMerchantId()!=null)
                $data = $data->where('merchantId',  $searchForm->getMerchantId());
            $data = $data->orderBy('createdTime', 'desc');
            if($searchForm->getPageSize()!=null)
                $data = $data->paginate($searchForm->getPageSize(), ['*'], 'page', $searchForm->getPageIndex());
            else
                $data = $data->get();
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans('error.error_system'));
        }
    }
}

?>