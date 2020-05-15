<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use App\Order;
use App\Http\Resources\Order as OrderResource;
use Illuminate\Support\Facades\Auth;

/* Test routes */

/*
Route::get('/mobile-form', function () {
	return view('mobile-form');
})->name('mobile-form');

Route::get('/mobile-form-amsped', function () {
	return view('mobile-form-amsped');
})->name('mobile-form-amsped');

Route::get('/mobile-form-worldfilia', function () {
	return view('mobile-form-worldfilia');
})->name('mobile-form-worldfilia');

Route::get('/mobile-form-affiliationpark', function () {
	return view('mobile-form-affiliationpark');
})->name('mobile-form-affiliationpark');

Route::get('/test', function () {
    return view('application');
})->name('test');


/*
Route::post('/test', 'TestController@post')->name('test');

Route::get('/test2', function () {
	return view('test2');
})->name('test2');

Route::get('/test_app', function() {
	return view('dash');
});

/* Auth routes */

/*
//Auth::routes(['register' => false]); // Closed registration
Auth::routes(); // Open registration
Route::get('/logout', 'Auth\LoginController@logout')->name('logout'); // GET method logout

/* Home routes */

/*
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', function () {
    return view('welcome');
})->name('home');

/* Dashboard */

/*
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::post('/dashboard', 'DashboardController@filter')->name('dashboard');

/* Orders routes */

/*
Route::get('/orders', 'OrderController@ordersIndex')->name('orders');
Route::post('/orders', 'OrderController@ordersFilter')->name('orders');
Route::get('/order/{id}', 'OrderController@show')->name('order');
Route::post('/createOrder', 'OrderController@storeAndReturnView')->name('createOrder');
Route::delete('/order/{id}', 'OrderController@delete')->name('order');

/* Forms routes */

/*
Route::get('/form-builder', 'FormBuilderController@index')->name('form-builder');
Route::post('/form-builder', 'FormBuilderController@index')->name('form-builder');

Route::get('/forms', 'FormController@index')->name('forms');
Route::post('/forms', 'FormController@index')->name('forms');

Route::get('/form/{id}', 'FormController@dailyReports')->name('form');
Route::post('/form/{id}', 'FormController@dailyReports')->name('form');
Route::delete('form/{id}', 'FormController@delete')->name('form');
Route::get('form/{id}/code', 'FormController@getCode')->name('form_get_code');
Route::get('form/{id}/edit', 'FormController@edit')->name('form_edit');
Route::post('form/{id}/edit', 'FormController@update')->name('form_edit');

/* Invoice routes */

/*
Route::get('/invoice/upload', 'InvoiceController@uploadIndex')->name('invoice/upload');
Route::post('/invoice/upload', 'InvoiceController@upload')->name('invoice/upload');

Route::get('/invoices', 'InvoiceController@index')->name('invoices');
Route::post('/invoices', 'InvoiceController@index')->name('invoices');

Route::delete('/invoice/{id}', 'InvoiceController@delete')->name('invoice');
Route::get('/invoice/{id}/download', 'InvoiceController@download')->name('invoice_download');
Route::post('/invoices/download', 'InvoiceController@downloadMultipleInvoices')->name('invoices/download');

Route::post('/invoice/{id}/set_auto_invoice', 'InvoiceController@setAutoInvoice')->name('set_auto_invoice');

Route::get('/invoices/transactions/upload', 'InvoiceController@uploadTransactionsIndex')->name('invoices/transactions/upload');
Route::post('/invoices/transactions/upload', 'InvoiceController@uploadTransactions')->name('invoices/transactions/upload');

Route::post('/invoices/summary/download', 'InvoiceController@invocesSummaryDownoad')->name('invoices/summary/download');
Route::post('/auto-invoices/summary/download', 'InvoiceController@autoInvocesSummaryDownoad')->name('auto-invoices/summary/download');

/* Advertiser routes */

/*
Route::get('/advertisers', 'AdvertiserController@index')->name('advertisers');
Route::get('/advertiser/create', 'AdvertiserController@createIndex')->name('advertiser/create');
Route::post('/advertiser/create', 'AdvertiserController@store')->name('advertiser/create');
Route::get('/advertiser/{id}/edit', 'AdvertiserController@edit')->name('advertiser/edit');
Route::post('/advertiser/{id}/edit', 'AdvertiserController@update')->name('advertiser/edit');
Route::delete('/advertiser/{id}', 'AdvertiserController@delete')->name('advertiser');

/* Tag routes */

/*
Route::get('/tags', 'TagController@index')->name('tags');
Route::get('/tag/create', 'TagController@createIndex')->name('tag/create');
Route::post('/tag/create', 'TagController@store')->name('tag/create');
Route::get('/tag/{id}/edit', 'TagController@edit')->name('tag/edit');
Route::post('/tag/{id}/edit', 'TagController@update')->name('tag/edit');
Route::delete('/tag/{id}', 'TagController@delete')->name('tag');

/* Product routes */

/*
Route::get('/products', 'ProductController@index')->name('products');
Route::get('/product/create', 'ProductController@createIndex')->name('product/create');
Route::post('/product/create', 'ProductController@store')->name('product/create');
Route::get('/product/{id}/edit', 'ProductController@edit')->name('product/edit');
Route::post('/product/{id}/edit', 'ProductController@update')->name('product/edit');
Route::delete('/product/{id}', 'ProductController@delete')->name('product');

/* Traffic Sources routes */

/*
Route::get('/traffic-sources', 'TrafficSourceController@index')->name('traffic-sources');
Route::get('/traffic-source/create', 'TrafficSourceController@createIndex')->name('traffic-source/create');
Route::post('/traffic-source/create', 'TrafficSourceController@store')->name('traffic-source/create');
Route::get('/traffic-source/{id}/edit', 'TrafficSourceController@edit')->name('traffic-source/edit');
Route::post('/traffic-source/{id}/edit', 'TrafficSourceController@update')->name('traffic-source/edit');
Route::delete('/traffic-source/{id}', 'TrafficSourceController@delete')->name('traffic-source');

/* Landing routes */

/*
Route::get('/landings', 'LandingController@index')->name('landings');
Route::get('/landing/create', 'LandingController@createIndex')->name('landing/create');
Route::post('/landing/create', 'LandingController@store')->name('landing/create');
Route::get('/landing/{id}/edit', 'LandingController@edit')->name('landing/edit');
Route::post('/landing/{id}/edit', 'LandingController@update')->name('landing/edit');
Route::delete('/landing/{id}', 'LandingController@delete')->name('landing');

/* Admin routes */

/*
Route::get('/admin/dashboard', 'DashboardController@adminIndex')->name('admin/dashboard');
Route::post('/admin/dashboard', 'DashboardController@adminFilter')->name('admin/dashboard');

Route::get('/admin/users', 'UserController@show')->name('admin/users');
Route::post('/admin/users', 'UserController@show')->name('admin/users');

Route::get('/admin/orders', 'OrderController@adminOrdersIndex')->name('admin/orders');
Route::post('/admin/orders', 'OrderController@adminOrdersFilter')->name('admin/orders');

Route::get('/admin/forms', 'FormController@adminIndex')->name('admin/forms');
Route::post('/admin/forms', 'FormController@adminIndex')->name('admin/forms');

Route::get('/admin/report/users', 'ReportController@usersReport')->name('admin/report/users');
Route::post('/admin/report/users', 'ReportController@usersReport')->name('admin/report/users');
Route::get('/admin/report/products', 'ReportController@productsReport')->name('admin/report/products');
Route::post('/admin/report/products', 'ReportController@productsReport')->name('admin/report/products');
Route::get('/admin/report/networks', 'ReportController@networksReport')->name('admin/report/networks');
Route::post('/admin/report/networks', 'ReportController@networksReport')->name('admin/report/networks');

Route::get('/admin/report/mediabuyers', 'ReportController@mediabuyersReport')->name('admin/report/mediabuyers');
Route::post('/admin/report/mediabuyers', 'ReportController@mediabuyersReport')->name('admin/report/mediabuyers');
Route::get('/admin/report/affiliates', 'ReportController@affiliatesReport')->name('admin/report/affiliates');
Route::post('/admin/report/affiliates', 'ReportController@affiliatesReport')->name('admin/report/affiliates');
Route::get('/admin/report/advertisers', 'ReportController@advertisersReport')->name('admin/report/advertisers');
Route::post('/admin/report/advertisers', 'ReportController@advertisersReport')->name('admin/report/advertisers');

Route::get('/admin/invoices', 'InvoiceController@adminIndex')->name('admin/invoices');
Route::post('/admin/invoices', 'InvoiceController@adminIndex')->name('admin/invoices');

*/
Route::get('/{any}', function() {
    return view('application');
})->where('any', '.*');
