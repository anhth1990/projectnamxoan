<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\NotiMobileForm;
use App\Http\Models\NotiMobileDAO;
use Session;
class NotiMobileService extends BaseService {
    public function __construct() {
        $this->notiMobileDao = new NotiMobileDAO();
    }
    
    /*
     * insert noti mobile
     */
    public function insertNotiMobile(NotiMobileForm $insertForm){
        $objDao = new NotiMobileDAO();
        $objDao->type = $insertForm->getType();
        $objDao->content = $insertForm->getContent();
        $objDao->status = $insertForm->getStatus();
        $objDao->merchantId = $insertForm->getMerchantId();
        return $this->notiMobileDao->saveResultId($objDao);
    }
    
}

