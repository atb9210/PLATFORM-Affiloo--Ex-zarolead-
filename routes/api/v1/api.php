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
Route::apiResource('users', 'api\v1\UserController')->except([
    'create', 'edit',
])->middleware('auth:api');
Route::post('/users/{id}/change-roles', 'api\v1\UserController@changeRoles')->middleware('auth:api');

/* Orders routes */
Route::apiResource('orders', 'api\v1\OrderController')->except([
    'create', 'edit', 'store',
])->middleware('auth:api');
Route::post('orders', 'api\v1\OrderController@store');

/* Products routes */
Route::apiResource('products', 'api\v1\ProductController')->except([
    'create', 'edit',
])->middleware('auth:api');

/* Tags routes */
Route::apiResource('tags', 'api\v1\TagController')->except([
    'create', 'edit',
])->middleware('auth:api');

/* Advertisers routes */
Route::apiResource('advertisers', 'api\v1\AdvertiserController')->except([
    'create', 'edit',
])->middleware('auth:api');
