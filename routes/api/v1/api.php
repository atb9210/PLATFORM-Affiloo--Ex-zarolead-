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

/* Auth routes */
Route::post('/login', 'api\v1\Auth\LoginController@login')->name('login');
Route::post('/refresh-token', 'api\v1\Auth\LoginController@refreshToken');
Route::post('/register', 'api\v1\Auth\RegisterController@register');

/* Users routes */
/*
Route::get('/users', 'api\v1\UserController@index')->middleware('auth:api');
Route::get('/users/{id}', 'api\v1\UserController@show')->middleware('auth:api');
Route::put('/users/{id}', 'api\v1\UserController@update')->middleware('auth:api');
Route::delete('/users/{id}', 'api\v1\UserController@destroy')->middleware('auth:api');
*/
Route::apiResource('users', 'api\v1\UserController')->except([
    'create', 'edit',
])->middleware('auth:api');

/* Orders routes */
/*
Route::get('/orders', 'api\v1\OrderController@index')->middleware('auth:api');
Route::get('/order/{id}', 'api\v1\OrderController@show')->middleware('auth:api');
Route::put('/order/{id}', 'api\v1\OrderController@update')->middleware('auth:api');
Route::delete('/order/{id}', 'api\v1\OrderController@destroy')->middleware('auth:api');
*/
Route::apiResource('orders', 'api\v1\OrderController')->except([
    'create', 'edit', 'store',
])->middleware('auth:api');
Route::post('orders', 'api\v1\OrderController@store');
