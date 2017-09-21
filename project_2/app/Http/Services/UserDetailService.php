<?php
namespace App\Http\Services;
/* 
 * anhth1990 
 */
use App\Http\Forms\UserDetailForm;
use App\Http\Models\UserDetailDAO;

class UserDetailService extends BaseService {
    private $userDetailDao;
    public function __construct() {
        $this->userDetailDao = new UserDetailDAO();
    }
    
    /*
     * insert user detail
     */
    public function insertUserDetail(UserDetailForm $userDetailForm){
        $userDetailDao = new UserDetailDAO();
        $userDetailDao->email = $userDetailForm->getEmail();
        $userDetailDao->firstName = $userDetailForm->getFirstName();
        $userDetailDao->mobile = $userDetailForm->getMobile();
        $userDetailDao->status = $userDetailForm->getStatus();
        $userDetailDao->merchantId = $userDetailForm->getMerchantId();
        $userDetailDao->userId = $userDetailForm->getUserId();
        $userDetailDao->type = $userDetailForm->getType();
        $userDetailDao->lat = $userDetailForm->getLat();
        $userDetailDao->long = $userDetailForm->getLong();
        return $this->userDetailDao->saveResultId($userDetailDao);
    } 
    /*
     * update user detail
     */
    public function updateUserDetail(UserDetailForm $userDetailForm){
        $userDetailDao = $this->userDetailDao->findById($userDetailForm->getId());
        if($userDetailForm->getStatus()!=null){
            $userDetailDao->status = $userDetailForm->getStatus();
        }
        if($userDetailForm->getFirstName()!=null){
            $userDetailDao->firstName = $userDetailForm->getFirstName();
        }
        if($userDetailForm->getLastName()!=null){
            $userDetailDao->lastName = $userDetailForm->getLastName();
        }
        if($userDetailForm->getGender()!=null){
            $userDetailDao->gender = $userDetailForm->getGender();
        }
        if($userDetailForm->getProvinceId()!=null){
            $userDetailDao->provinceId = $userDetailForm->getProvinceId();
        }
        if($userDetailForm->getDistrictId()!=null){
            $userDetailDao->districtId = $userDetailForm->getDistrictId();
        }
        if($userDetailForm->getAddress()!=null){
            $userDetailDao->address = $userDetailForm->getAddress();
        }
        if($userDetailForm->getDateOfBirth()!=null){
            $userDetailDao->dateOfBirth = $userDetailForm->getDateOfBirth();
        }
        return $this->userDetailDao->saveResultId($userDetailDao);
    } 
    
    public function updateUserDetailActive(UserDetailForm $userDetailForm){
        $userDetailDao = $this->userDetailDao->searchDataFirst($userDetailForm);
        if($userDetailForm->getStatus()!=null){
            $userDetailDao->status = $userDetailForm->getStatus();
        }
        if($userDetailForm->getFirstName()!=null){
            $userDetailDao->firstName = $userDetailForm->getFirstName();
        }
        if($userDetailForm->getLastName()!=null){
            $userDetailDao->lastName = $userDetailForm->getLastName();
        }
        if($userDetailForm->getGender()!=null){
            $userDetailDao->gender = $userDetailForm->getGender();
        }
        if($userDetailForm->getProvinceId()!=null){
            $userDetailDao->provinceId = $userDetailForm->getProvinceId();
        }
        if($userDetailForm->getDistrictId()!=null){
            $userDetailDao->districtId = $userDetailForm->getDistrictId();
        }
        if($userDetailForm->getAddress()!=null){
            $userDetailDao->address = $userDetailForm->getAddress();
        }
        if($userDetailForm->getDateOfBirth()!=null){
            $userDetailDao->dateOfBirth = $userDetailForm->getDateOfBirth();
        }
        return $this->userDetailDao->saveResultId($userDetailDao);
    } 
    /*
     * search first data
     */
    public function searchFirstData(UserDetailForm $searchForm){
        return $this->userDetailDao->searchDataFirst($searchForm);
    }
    /*
     * search lits data
     */
    public function searchListData(UserDetailForm $searchForm){
        return $this->userDetailDao->searchListData($searchForm);
    }
    /*
     * get Data By Id
     */
    public function getDataById($id){
        return $this->userDetailDao->findById($id);
    }
    
}

