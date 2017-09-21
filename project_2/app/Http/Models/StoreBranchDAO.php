<?php

/*
 * anhth1990
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use App\Http\Models\StoreDAO;
use App\Http\Forms\StoreForm;

class StoreBranchDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct("tb_store_branch");
    }
    
    public function store()
    {
        return $this->belongsTo(new StoreDAO(),'storeId','id');
    }
    
    public function searchData(StoreForm $searchForm) {
        try {
            $data = StoreBranchDAO::with(['store'])->select('*');
            if($searchForm->getMerchantId()!=null){
                $data = $data->where('merchantId','=', $searchForm->getMerchantId());
            }
            if($searchForm->getStoreId()!=null){
                $data = $data->where('storeId','=', $searchForm->getStoreId());
            }
            if($searchForm->getKeySearch()!=null){
                $data = $data->where('name','like', '%'.$searchForm->getKeySearch().'%');
            }
            if($searchForm->getProvinceId()!=null){
                $data = $data->where('provinceId','=', $searchForm->getProvinceId());
            }
            if($searchForm->getStatus()!=null){
                $data = $data->where('status','=', $searchForm->getStatus());
            }
            if($searchForm->getCategoryId()!=null&& $searchForm->getCategoryId()!=0){
                $categoryId = $searchForm->getCategoryId();
                $data = $data->whereHas('store',function($q) use ($categoryId)
                {
                    $q->where('categoryId', $categoryId);
                });
            }
            $data = $data->where('status','!=', env('COMMON_STATUS_DELETED'));
            $data = $data->orderBy('id', 'desc');
            if($searchForm->getPageSize() != null){
                $data = $data->paginate($searchForm->getPageSize(), ['*'], 'page', $searchForm->getPageIndex());
            }else{
                $data = $data->get();
            }
            return $data;
        } catch (Exception $ex) {
            $this->logs_custom("\nSQL ****\nMessage : ".$ex->getMessage(). "\nFile : ".$ex->getFile() . "\nLine : ".$ex->getLine() );
            throw new Exception(trans('error.error_system'));
        }
    }
    
    public function getDataByHashcode($hashcode) {
        try {
            $data = StoreBranchDAO::with(['store'])->select('*');
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