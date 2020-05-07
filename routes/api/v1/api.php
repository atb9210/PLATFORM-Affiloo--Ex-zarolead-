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
Route::post('/login', 'api\v1\Auth\LoginController@login');
Route::post('/register', 'api\v1\Auth\RegisterController@register');

/* Users routes */
Route::get('/users', 'api\v1\UserController@index')->middleware('auth:api');
Route::get('/user/{id}', 'api\v1\UserController@show')->middleware('auth:api');
Route::put('/user/{id}', 'api\v1\UserController@update')->middleware('auth:api');
Route::delete('/user/{id}', 'api\v1\UserController@destroy')->middleware('auth:api');
