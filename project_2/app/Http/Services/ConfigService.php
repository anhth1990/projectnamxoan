<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use DB;
use Exception;
use App\Http\Models\ConfigDAO;
use App\Http\Forms\ConfigForm;

class ConfigService extends BaseService {
    
    private $configDao;
    public function __construct() {
        parent::__construct();
        $this->configDao = new ConfigDAO();
    }
    
    public function searchListData(ConfigForm $searchForm){
        return $this->configDao->getList($searchForm);
    }
    
    public function insertOrUpdate(ConfigForm $configForm){
        $merchantId =$configForm->getMerchantId();
        // rank user
        $rankUserDao = $this->configDao->getDataByKey('rank_user',$merchantId);
        if($rankUserDao==null){
            $rankUserDao = new ConfigDAO();
        }
        $rankUserDao->key = 'rank_user';
        $rankUserDao->value = $configForm->getRankUser();
        $rankUserDao->status = env('COMMON_STATUS_ACTIVE');
        $rankUserDao->merchantId = $merchantId;
        $rankUserDao = $this->configDao->saveResultId($rankUserDao);
        // recensy
        $recensyDao = $this->configDao->getDataByKey('recensy',$merchantId);
        if($recensyDao==null){
            $recensyDao = new ConfigDAO();
        }
        $recensyDao->key = 'recensy';
        $recensyDao->value = $configForm->getRecensy();
        $recensyDao->status = env('COMMON_STATUS_ACTIVE');
        $recensyDao->merchantId = $merchantId;
        $recensyDao = $this->configDao->saveResultId($recensyDao);
        // frequency
        $frequencyDao = $this->configDao->getDataByKey('frequency',$merchantId);
        if($frequencyDao==null){
            $frequencyDao = new ConfigDAO();
        }
        $frequencyDao->key = 'frequency';
        $frequencyDao->value = $configForm->getFrequency();
        $frequencyDao->status = env('COMMON_STATUS_ACTIVE');
        $frequencyDao->merchantId = $merchantId;
        $frequencyDao = $this->configDao->saveResultId($frequencyDao);
        // monetary
        $monetaryDao = $this->configDao->getDataByKey('monetary',$merchantId);
        if($monetaryDao==null){
            $monetaryDao = new ConfigDAO();
        }
        $monetaryDao->key = 'monetary';
        $monetaryDao->value = $configForm->getMonetary();
        $monetaryDao->status = env('COMMON_STATUS_ACTIVE');
        $monetaryDao->merchantId = $merchantId;
        $monetaryDao = $this->configDao->saveResultId($monetaryDao);
    }
    
    public function getDataByKey($key,$merchantId){
        $data = $this->configDao->getDataByKey($key, $merchantId);
        if($data!=null){
            if($data->value==null || $data->value =="")
                return null;
            else
                return $data->value;
        }
        return null;
    }
    
}

