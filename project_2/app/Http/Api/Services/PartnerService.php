<?php
namespace App\Http\Api\Services;
/* 
 * anhth1990 
 */
use Exception;
use App\Http\Services\BaseService;
use DB;
use App\Http\Models\PartnerDAO;
class PartnerService extends BaseService {
    private $partnerDao;
    public function __construct() {
        $this->partnerDao = new PartnerDAO();
    }
    
    /*
     * get partner by merchant
     */
    public function getPartnerDefaultByMerchant($merchantId){
        return $this->partnerDao->getPartnerByType($merchantId,  env('PARTNER_DEFAULT'));
    }
    
    
}

