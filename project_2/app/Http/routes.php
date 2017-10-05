<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */ 
/*
 * ********* ADMIN PORTAL
 */
Route::filter("check-login-admin-portal", function() {
    if (!Session::has("login_admin_portal")) {
        return redirect("/portal/login"); 
    }
});
Route::get('/portal/login', '\App\Http\AdminPortal\Controllers\AuthController@getLogin');
Route::post('/portal/login', '\App\Http\AdminPortal\Controllers\AuthController@postLogin');
Route::get('/portal/logout', '\App\Http\AdminPortal\Controllers\AuthController@getLogout');
Route::group(array('prefix' => 'portal', 'before' => 'check-login-admin-portal'), function() {
    // Dashboard
    Route::get('/', '\App\Http\AdminPortal\Controllers\DashboardController@getIndex');
    // Manager
    Route::get('/manager-id', '\App\Http\AdminPortal\Controllers\ManagerIdController@getList');
    Route::post('/manager-id', '\App\Http\AdminPortal\Controllers\ManagerIdController@postList');
    Route::get('/manager-id/edit/{hashcode}', '\App\Http\AdminPortal\Controllers\ManagerIdController@getEdit');
    Route::post('/manager-id/edit', '\App\Http\AdminPortal\Controllers\ManagerIdController@postEdit');
    Route::get('/manager-id/deleteSearch', '\App\Http\AdminPortal\Controllers\ManagerIdController@getClearSearch');
    
    Route::get('/manager-id/upload-file', '\App\Http\AdminPortal\Controllers\ManagerIdController@getUploadFile');
    Route::post('/manager-id/upload-file', '\App\Http\AdminPortal\Controllers\ManagerIdController@postUploadFile');
    
    Route::get('/manager-id/log/{hashcode}', '\App\Http\AdminPortal\Controllers\ManagerIdController@getLog');
    
    // User Admin
    Route::get('/user-admin', '\App\Http\AdminPortal\Controllers\UserAdminController@getList');
    Route::post('/user-admin', '\App\Http\AdminPortal\Controllers\UserAdminController@postList');
    Route::get('/user-admin/deleteSearch', '\App\Http\AdminPortal\Controllers\UserAdminController@getClearSearch');
    Route::get('/user-admin/add', '\App\Http\AdminPortal\Controllers\UserAdminController@getAdd');
    Route::post('/user-admin/add-confirm', '\App\Http\AdminPortal\Controllers\UserAdminController@postAddConfirm');
    Route::get('/user-admin/add-finish', '\App\Http\AdminPortal\Controllers\UserAdminController@getAddFinish');
    Route::get('/user-admin/edit/{hashcode}', '\App\Http\AdminPortal\Controllers\UserAdminController@getEdit');
    Route::post('/user-admin/edit-confirm', '\App\Http\AdminPortal\Controllers\UserAdminController@postEditConfirm');
    Route::get('/user-admin/edit-finish', '\App\Http\AdminPortal\Controllers\UserAdminController@getEditFinish');
    Route::get('/user-admin/change-password', '\App\Http\AdminPortal\Controllers\UserAdminController@getChangePassword');
    Route::post('/user-admin/change-password', '\App\Http\AdminPortal\Controllers\UserAdminController@postChangePassword');
    
    //Route::get('/user-admin/add-finish', '\App\Http\AdminPortal\Controllers\ManagerIdController@getClearSearch');
});
/*
 * FrontEnd
 */
Route::get('/', '\App\Http\FrontEnd\Controllers\IndexController@getIndex');
Route::get('/activeCode', '\App\Http\FrontEnd\Controllers\IndexController@getActiveCode');

Route::get('/getLatLong', '\App\Http\Controllers\Controller@getLatLong');

/*
 * Api
 */
Route::post('stripe/api/auth', '\App\Http\Api\Controllers\ApiController@postAuth');
Route::post('stripe/api/auth-merchant', '\App\Http\Api\Controllers\ApiController@postAuthMerchant');
Route::post('stripe/api/unauth', '\App\Http\Api\Controllers\ApiController@postUnauth');
Route::post('stripe/api/service', '\App\Http\Api\Controllers\ApiController@postService');
Route::post('stripe/api/manager-id', '\App\Http\AdminPortal\Controllers\ManagerIdController@getData');
Route::post('stripe/manager-id/get-data', '\App\Http\AdminPortal\Controllers\ManagerIdController@getDataList');
Route::post('stripe/manager-id/delete', '\App\Http\AdminPortal\Controllers\ManagerIdController@postDelete');
Route::post('stripe/user-admin/refresh-password', '\App\Http\AdminPortal\Controllers\UserAdminController@postRefreshPassword');



