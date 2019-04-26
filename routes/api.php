<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'ApiController@register');
Route::post('login', 'ApiController@login');
Route::post('viewProfile', 'ApiController@viewProfile');
Route::post('dashboard', 'ApiController@dashboard');
Route::post('forgotpassword','ApiController@forgotpassword');


Route::post('active_pickup_list','ApiController@active_pickup_list');
Route::post('past_pickup_list','ApiController@past_pickup_list');
Route::post('pickup_details','ApiController@pickup_details');
Route::post('accept_pickup','ApiController@accept_pickup');
Route::post('pickup_upload','ApiController@pickup_upload');
Route::post('pickup_upload2','ApiController@pickup_upload2');
Route::post('pickup_submit','ApiController@pickup_submit');
Route::post('pickup_submit2','ApiController@pickup_submit2');
Route::post('redeem_history','ApiController@redeemHistory');
Route::post('request_redeem','ApiController@requestRedeem');
Route::post('updateProfile','ApiController@updateProfile');

Route::post('fcm', 'ApiController@fcm');
Route::post('PickupPdf','ApiController@PickupPdf');
Route::post('notificationHistory','ApiController@notificationHistory');
Route::post('deleteNotification','ApiController@deleteNotification');
Route::post('assignPickupList','ApiController@assignPickupList');
Route::post('reschedule','ApiController@Reschedule');
Route::post('pickup_decline','ApiController@pickup_decline');
Route::post('mail_links','ApiController@mail_links');
Route::post('lookup_data','ApiController@lookup_data');




