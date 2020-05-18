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

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

// Orders
Route::get('orders', 'OrderController@index');
Route::get('order/{id}', 'OrderController@show');
Route::post('order', 'api\v1\OrderController@store');
Route::put('order', 'OrderController@store');
Route::delete('order/{id}', 'OrderController@delete');

Route::get('order_sent_to_network/{id}', 'OrderController@orderSentToNetwork');

// Form
Route::post('form_visited', 'FormController@visited')->middleware('cors');
Route::post('form_set_daily_cost', 'FormController@setDailyCost');
