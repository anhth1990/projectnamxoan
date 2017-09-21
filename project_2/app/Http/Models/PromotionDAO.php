<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Forms\PromotionForm;

class PromotionDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_promotion");
    }
    
    public function getList(PromotionForm $searchForm){
        try {
            $data = PromotionDAO::select('*');
            if($searchForm->getMerchantId()!=null)
                $data = $data->where('merchantId',  $searchForm->getMerchantId());
            if($searchForm->getName()!=null){
                $data = $data->where('name', 'like' ,'%'.$searchForm->getName().'%');
            }
            if($searchForm->getDateNow()!=null){
                $data = $data->where('dateFrom', '<=' ,$searchForm->getDateNow());
                $data = $data->where('dateTo', '>=' ,$searchForm->getDateNow());
            }
            if($searchForm->getStoreId()!=null){
                $data = $data->where('idRef', '=' ,$searchForm->getStoreId());
            }
            if($searchForm->getBranchId()!=null){
                $data = $data->orWhere('idRef', '=' ,$searchForm->getBranchId());
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