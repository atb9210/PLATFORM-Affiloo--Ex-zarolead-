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

/* Fb accounts routes */
Route::apiResource('fb-accounts', 'api\third\FbAccountController')->except([
    'create', 'edit',
])->middleware('auth:api');
