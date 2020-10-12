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
Route::post('/network/worldfilia', 'api\fake\NetworkController@worldfilia')->name('network.worldfilia');
Route::post('/network/amsped', 'api\fake\NetworkController@amsped')->name('network.amsped');
Route::get('/network/affiliation-park', 'api\fake\NetworkController@affiliationPark')->name('network.affiliation-park');
