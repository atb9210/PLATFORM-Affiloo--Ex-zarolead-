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
Route::post('orders/double-recovery', 'api\v1\OrderController@doubleRecovery')->name('orders.double.recovery');
Route::get('orders-log', 'api\v1\OrderController@getLog')->middleware('auth:api')->name('orders.log'); // NOTE: orders/log does not work

/* Products routes */
Route::apiResource('products', 'api\v1\ProductController')->except([
    'create', 'edit',
])->middleware('auth:api');

/* Tags routes */
Route::apiResource('tags', 'api\v1\TagController')->except([
    'create', 'edit',
])->middleware('auth:api');

/* Traffic sources routes */
Route::apiResource('traffic-sources', 'api\v1\TrafficSourceController')->except([
    'create', 'edit', 'update',
])->middleware('auth:api');
Route::post('traffic-sources/{id}/update', 'api\v1\TrafficSourceController@update')->middleware('auth:api')
    ->name('traffic-sources.update');
Route::post('traffic-sources/{id}/ad-model/validate', 'api\v1\TrafficSourceController@validateAdModel')->middleware('auth:api')
    ->name('traffic-sources.ad-model.validate');

/* Landings routes */
Route::apiResource('landings', 'api\v1\LandingController')->except([
    'create', 'edit',
])->middleware('auth:api');
Route::get('landings/{id}/stats', 'api\v1\LandingController@stats')->middleware('auth:api')
    ->name('landings.stats');
Route::get('landings/{id}/code', 'api\v1\LandingController@code')->middleware('auth:api')
    ->name('landings.code');

/* Flows routes */
Route::apiResource('flows', 'api\v1\FlowController')->except([
    'create', 'edit',
])->middleware('auth:api');
Route::get('flows/{id}/landings/get', 'api\v1\FlowController@getLandingUrl')->name('flows.landings.get');
Route::post('flows/{id}/hotload', 'api\v1\FlowController@hotload')->name('flows.hotload');

/* Forms routes */
Route::apiResource('forms', 'api\v1\FormController')->except([
    'create', 'edit',
])->middleware('auth:api');
Route::get('forms/{id}/restore', 'api\v1\FormController@restore')->middleware('auth:api')
    ->name('forms.restore');
Route::post('forms/{id}/clone', 'api\v1\FormController@clone')->middleware('auth:api')
    ->name('forms.clone');
Route::get('forms/{id}/visited', 'api\v1\FormController@visited')->name('forms.visited');
/*
Route::get('forms/{id}/code', 'api\v1\FormController@code')->middleware('auth:api')
    ->name('forms.code');
*/
Route::get('forms/{id}/stats', 'api\v1\FormController@stats')->middleware('auth:api')
    ->name('forms.stats');
Route::get('forms/{id}/landings/stats', 'api\v1\FormController@landingsStats')->middleware('auth:api')
    ->name('forms.landings.stats');
Route::put('forms/{id}/stats', 'api\v1\FormController@updateStats')->middleware('auth:api')
    ->name('forms.stats.update');
Route::put('forms/{id}/stats-add', 'api\v1\FormController@addToCostStats')->middleware('auth:api')
    ->name('forms.stats.add');
Route::put('forms/{id}/stats-edit', 'api\v1\FormController@editCostStats')->middleware('auth:api')
    ->name('forms.stats.edit');
Route::post('forms/{id}/notes', 'api\v1\FormController@createNote')->middleware('auth:api')
    ->name('forms.notes.store');
Route::put('forms/notes/{id}', 'api\v1\FormController@editNote')->middleware('auth:api')
    ->name('forms.notes.update');
Route::delete('forms/notes/{id}', 'api\v1\FormController@deleteNote')->middleware('auth:api')
    ->name('forms.notes.destroy');
Route::get('forms/{id}/initialize', 'api\v1\FormController@initialize')->name('forms.initialize');
Route::post('forms/{id}/input/validate', 'api\v1\FormController@validateInput')->name('forms.input.validate');
Route::get('forms-styles', 'api\v1\FormController@getStyles')->middleware('auth:api')
    ->name('forms-styles.index');
Route::post('forms-styles', 'api\v1\FormController@createStyle')->middleware('auth:api')
    ->name('forms-styles.store');
Route::put('forms-styles/{id}', 'api\v1\FormController@updateStyle')->middleware('auth:api')
    ->name('forms-styles.update');
Route::delete('forms-styles/{id}', 'api\v1\FormController@deleteStyle')->middleware('auth:api')
    ->name('forms-styles.destroy');

/* Invoices routes */
Route::apiResource('invoices', 'api\v1\InvoiceController')->except([
    'create', 'edit',
])->middleware('auth:api');
Route::post('invoices/store-ex', 'api\v1\InvoiceController@storeEx')->middleware('auth:api')
    ->name('invoices.store-ex');
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

/* Domains routes */
Route::apiResource('domains', 'api\v1\DomainController')->except([
    'create', 'edit',
])->middleware('auth:api');
Route::get('domains/{id}/ssl', 'api\v1\DomainController@activateSSL')->middleware('auth:api')
    ->name('domains.ssl');
Route::post('domains/{id}/dns-records', 'api\v1\DomainController@addDNSRecord')->middleware('auth:api')
    ->name('domains.dns-records');

// TODO: Change path?
Route::get('domains/{id}/ls', 'api\v1\DomainController@listFilesAndDirectories')->middleware('auth:api')
    ->name('domains.ls');
Route::get('domains/{id}/cat', 'api\v1\DomainController@getFileContent')->middleware('auth:api')
    ->name('domains.cat');
Route::post('domains/{id}/touch', 'api\v1\DomainController@createFile')->middleware('auth:api')
    ->name('domains.touch');
Route::post('domains/{id}/mkdir', 'api\v1\DomainController@createDirectory')->middleware('auth:api')
    ->name('domains.mkdir');
Route::post('domains/{id}/upload', 'api\v1\DomainController@uploadFile')->middleware('auth:api')
    ->name('domains.upload');
Route::post('domains/{id}/rm', 'api\v1\DomainController@deleteFileOrDirectory')->middleware('auth:api')
    ->name('domains.rm');
Route::post('domains/{id}/clone-webpage', 'api\v1\DomainController@cloneWebpage')->middleware('auth:api')
    ->name('domains.clone-webpage');

Route::get('domains-namecheap', 'api\v1\DomainController@getNamecheapDomains')->middleware('auth:api')
    ->name('domains-namecheap');

// TODO: Where to put this???
Route::get('zarocloner/jobs/{id}', 'api\v1\DomainController@getJob')->middleware('auth:api')
    ->name('zarocloner.jobs');

/* Product medias routes */
Route::apiResource('product-medias', 'api\v1\ProductMediaController')->except([
    'create', 'edit', 'update',
])->middleware('auth:api');
/*
Route::post('product-medias/{id}/update', 'api\v1\ProductMediaController@update')->middleware('auth:api')
    ->name('product-medias.update'); // This must be declared this way because the put method
                                     // has some problems when uploading a file
*/

/* Ads routes */
Route::apiResource('ads', 'api\v1\AdController')->except([
    'create', 'edit',
])->middleware('auth:api');

/* Reports routes */
Route::group(['prefix' => 'reports', 'middleware' => 'auth:api'], function() {
    Route::get('users', 'api\v1\ReportController@users')->name('reports.users');
    Route::get('products', 'api\v1\ReportController@products')->name('reports.products');
    Route::get('traffic-sources', 'api\v1\ReportController@trafficSources')->name('reports.traffic-sources');
    Route::get('advertisers', 'api\v1\ReportController@advertisers')->name('reports.advertisers');
    Route::put('advertisers/{user_id}', 'api\v1\ReportController@advertisersUpdate')->name('reports.advertisers.update');
});

/* Storage routes */
Route::get('storage', 'api\v1\StorageController@index')->middleware('auth:api')->name('storage');

/* Postback URL */
Route::get('postback', 'api\v1\PostbackController@index')->name('postback');
