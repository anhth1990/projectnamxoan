<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\AccountForm;
use App\Http\Models\AccountDAO;
use Session;
class AccountService extends BaseService {
    public function __construct() {
        $this->accountDao = new AccountDAO();
    }
    
    /*
     * create account
     */
    public function insertAccount(AccountForm $accountForm){
        $accountDao = new AccountDAO();
        $accountDao->noAccount = $this->getNoAccountByType($accountForm->getGroup(), $accountForm->getType(), $accountForm->getIdRef());
        $accountDao->type = $accountForm->getType();
        $accountDao->status = $accountForm->getStatus();
        if($accountForm->getBalance()!=null){
            $accountDao->balance = $accountForm->getBalance();
        }
        $accountDao->idRef = $accountForm->getIdRef();
        return $this->accountDao->saveResultId($accountDao);
    }
    
    /*
     * get noAccount by Type
     */
    public function getNoAccountByType($group , $type,$uId){
        $noAccount = "";
        switch ($type){
            case env('ACCOUNT_TYPE_MAIN'):
                if($group==  env('TYPE_MERCHANT'))
                    $prefix = 3*1000000000;
                else if($group==  env('TYPE_USER'))
                    $prefix = 4*1000000000;
                else 
                    $prefix = 4*1000000000;
                $noAccount = $prefix+$uId;
                break;
            default :
                $noAccount = "";
        }
        return $noAccount;
    }
    
}

