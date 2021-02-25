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

/* Network routes */
Route::post('/network/worldfilia', 'api\fake\NetworkController@worldfilia')->name('network.worldfilia');
Route::post('/network/amsped', 'api\fake\NetworkController@amsped')->name('network.amsped');
Route::get('/network/affiliation-park', 'api\fake\NetworkController@affiliationPark')->name('network.affiliation-park');
Route::post('/network/zarolead', 'api\fake\NetworkController@zarolead')->name('network.zarolead');
Route::post('/network/finapsy', 'api\fake\NetworkController@finapsy')->name('network.finapsy');

/* Logistic routes */
Route::post('/logistic/momoka', 'api\fake\LogisticController@momoka')->name('logistic.momoka');
Route::get('/logistic/momoka/find-order/{id}', 'api\fake\LogisticController@momokaFindOrder')->name('logistic.momoka.find-order');
