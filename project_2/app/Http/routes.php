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
Route::post('stripe/manager-id/delete', '\App\Http\AdminPortal\Controllers\ManagerIdController@postDelete');



