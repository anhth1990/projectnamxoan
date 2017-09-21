<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Http\AdminPortal\Controllers;
use App\Http\AdminPortal\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use DB;

use App\Http\Services\ProIdentityService;
use App\Http\Forms\ProIdentityForm;

class DashboardController extends BaseController {
    private $identityService;
    public function __construct() {
        parent::__construct();
        $this->identityService = new ProIdentityService();
    }

    /*
     * Index
     */
    public function getIndex(){
        try {
            $titlePage = $this->configuration->titlePageAdminPortal.  trans('dashboard.title_page');
            $searchForm = new ProIdentityForm();
            $listObj = $this->identityService->searchListData($searchForm);
            $totalId = number_format(count($listObj));
            $totalIdOnline = 0;
            $totalIdOffline = 0;
            foreach ($listObj as $key=>$value){
                if(time()-  strtotime($value->last_login) < env('TIME_OFFLINE')){
                    $totalIdOnline++;
                }else{
                    $totalIdOffline++;
                }
            }
            
            return view('AdminPortal.Dashboard.index',  compact('titlePage','totalId','totalIdOnline','totalIdOffline'));
        } catch (Exception $ex) {
            $this->logs_custom("\nController ****\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }
    
    public function getList() {
        try {

            $searchForm = new ProIdentityForm();
            if (Session::has('SESSION_SEARCH_IDENTITY')) {
                $searchForm = Session::get('SESSION_SEARCH_IDENTITY');
            }
            // set page
            $page = 1;
            if (isset($_GET['page'])) {
                $page = intval($_GET['page']);
                if ($page == 0)
                    $page = 1;
            }
            $searchForm->setPageSize(env('PAGE_SIZE'));
            $listObj = $this->identityService->searchListData($searchForm);
            $countObj = $this->identityService->countList($searchForm);
            return view('AdminPortal.ManagerId.list', compact('listObj', 'countObj', 'searchForm', 'page'));
        } catch (Exception $ex) {
            $this->logs_custom("\nController ****\nMessage : " . $ex->getMessage() . "\nFile : " . $ex->getFile() . "\nLine : " . $ex->getLine());
            $error = $ex->getMessage();
            return view('errors.503', compact('error'));
        }
    }
    
    
}

