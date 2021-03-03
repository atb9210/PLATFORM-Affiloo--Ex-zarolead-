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

/* Internal network routes */
Route::apiResource('network', 'api\third\NetworkController')->except([
    'create', 'store', 'edit',
])->middleware('auth:api');
Route::post('/network', 'api\third\NetworkController@store')->name('network');
Route::get('/network/orders/{id}/update-status', 'api\third\NetworkController@updateStatus')->name('network.update-status');

/* Fb accounts routes */
Route::apiResource('fb-accounts', 'api\third\FbAccountController')->except([
    'create', 'edit',
])->middleware('auth:api');
Route::post('/fb-accounts/import', 'api\third\FbAccountController@import')->middleware('auth:api')
    ->name('fb-accounts.import');
Route::post('/fb-accounts/multiple', 'api\third\FbAccountController@storeMultiple')->middleware('auth:api')
    ->name('fb-accounts.store.multiple');

/* Fb ad rules routes */
Route::apiResource('fb-ad-rules', 'api\third\FbAdRuleController')->except([
    'create', 'edit',
])->middleware('auth:api');

/* Finapsy routes */
Route::get('/finapsy', 'api\third\FinapsyController@index')->name('finapsy.index');
Route::post('/finapsy', 'api\third\FinapsyController@store')->name('finapsy.store');
