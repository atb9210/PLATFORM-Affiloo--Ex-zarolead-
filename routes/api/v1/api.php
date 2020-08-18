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
Route::post('/login', 'api\v1\Auth\LoginController@login')->name('login')->name('auth.login');
Route::post('/refresh-token', 'api\v1\Auth\LoginController@refreshToken')->name('auth.refresh-token');
Route::post('/register', 'api\v1\Auth\RegisterController@register')->name('auth.register');

/* Users routes */
Route::apiResource('users', 'api\v1\UserController')->except([
    'create', 'edit',
])->middleware('auth:api');
Route::post('/users/{id}/change-password', 'api\v1\UserController@changePassword')->middleware('auth:api')
    ->name('users.change-password');
Route::post('/users/{id}/change-roles', 'api\v1\UserController@changeRoles')->middleware('auth:api')
    ->name('users.change-roles');
Route::get('my-reports', 'api\v1\UserController@getReports')->middleware('auth:api')
    ->name('users.reports.index');
Route::put('my-reports', 'api\v1\UserController@updateReport')->middleware('auth:api')
    ->name('users.reports.update');

/* Orders routes */
Route::apiResource('orders', 'api\v1\OrderController')->except([
    'create', 'edit', 'store', 'update',
])->middleware('auth:api');
Route::post('orders', 'api\v1\OrderController@store')->name('orders.store');
Route::put('orders/{id}', 'api\v1\OrderController@update')->name('orders.update');

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

/* Traffic sources routes */
Route::apiResource('traffic-sources', 'api\v1\TrafficSourceController')->except([
    'create', 'edit',
])->middleware('auth:api');

/* Landings routes */
Route::apiResource('landings', 'api\v1\LandingController')->except([
    'create', 'edit',
])->middleware('auth:api');
Route::get('landings/{id}/stats', 'api\v1\LandingController@stats')->middleware('auth:api')
    ->name('landings.stats');

/* Forms routes */
Route::apiResource('forms', 'api\v1\FormController')->except([
    'create', 'edit',
])->middleware('auth:api');
Route::post('forms/{id}/clone', 'api\v1\FormController@clone')->middleware('auth:api')
    ->name('forms.clone');
Route::get('forms/{id}/visited', 'api\v1\FormController@visited')->name('forms.visited');
Route::get('forms/{id}/code', 'api\v1\FormController@code')->middleware('auth:api')
    ->name('forms.code');
Route::get('forms/{id}/stats', 'api\v1\FormController@stats')->middleware('auth:api')
    ->name('forms.stats');
Route::get('forms/{id}/landings/stats', 'api\v1\FormController@landingsStats')->middleware('auth:api')
    ->name('forms.landings.stats');
Route::put('forms/{id}/stats', 'api\v1\FormController@updateStats')->middleware('auth:api')
    ->name('forms.stats.update');
Route::post('forms/{id}/notes', 'api\v1\FormController@createNote')->middleware('auth:api')
    ->name('forms.notes.store');
Route::put('forms/notes/{id}', 'api\v1\FormController@editNote')->middleware('auth:api')
    ->name('forms.notes.update');
Route::delete('forms/notes/{id}', 'api\v1\FormController@deleteNote')->middleware('auth:api')
    ->name('forms.notes.destroy');
Route::get('forms/{id}/initialize', 'api\v1\FormController@initialize')->name('forms.initialize');
Route::post('forms/{id}/input/validate', 'api\v1\FormController@validateInput')->name('forms.input.validate');

/* Invoices routes */
Route::apiResource('invoices', 'api\v1\InvoiceController')->except([
    'create', 'edit',
])->middleware('auth:api');
Route::get('invoices/{id}/download', 'api\v1\InvoiceController@download')->middleware('auth:api')
    ->name('invoices.download');
Route::post('invoices/download-zip', 'api\v1\InvoiceController@downloadZip')->middleware('auth:api')
    ->name('invoices.download-zip');
Route::post('invoices/download-summary', 'api\v1\InvoiceController@downloadSummary')->middleware('auth:api')
    ->name('invoices.download-summary');

/* Networks routes */
Route::apiResource('networks', 'api\v1\NetworkController')->except([
    'create', 'edit',
])->middleware('auth:api');
Route::post('networks/{id}/params/validate', 'api\v1\NetworkController@validateParams')->middleware('auth:api')
    ->name('networks.params.validate');

/* Campaigns routes */
Route::apiResource('campaigns', 'api\v1\CampaignController')->except([
    'create', 'edit',
])->middleware('auth:api');

/* Reports routes */
Route::group(['prefix' => 'reports', 'middleware' => 'auth:api'], function() {
    Route::get('users', 'api\v1\ReportController@users')->name('reports.users');
    Route::get('products', 'api\v1\ReportController@products')->name('reports.products');
});
