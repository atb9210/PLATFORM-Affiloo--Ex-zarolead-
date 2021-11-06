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

/* Fb ad templates routes */
Route::apiResource('fb-ad-templates', 'api\third\FbAdTemplateController')->except([
    'create', 'edit',
])->middleware('auth:api');

/* Fb ad rules routes */
Route::apiResource('fb-ad-rules', 'api\third\FbAdRuleController')->except([
    'create', 'edit',
])->middleware('auth:api');

/* Fb campaigns routes */
Route::apiResource('fb-campaigns', 'api\third\FbCampaignController')->except([
    'create', 'store', 'edit', 'update',
])->middleware('auth:api');
Route::post('/fb-campaigns/associate', 'api\third\FbCampaignController@associate')->middleware('auth:api')
    ->name('fb-campaigns.associate');

/* Finapsy routes */
Route::get('/finapsy', 'api\third\FinapsyController@index')->name('finapsy.index');
Route::post('/finapsy', 'api\third\FinapsyController@store')->name('finapsy.store');

/* Marketplace routes */
Route::get('/marketplace/me', 'api\third\MarketplaceController@me')->middleware('auth:api')
    ->name('marketplace.me');
Route::get('/marketplace/campaigns', 'api\third\MarketplaceController@campaigns')->middleware('auth:api')
    ->name('marketplace.campaigns');
Route::get('/marketplace/campaigns/{id}/landings', 'api\third\MarketplaceController@landings')->middleware('auth:api')
    ->name('marketplace.campaigns.landings');
Route::get('/marketplace/campaigns/{id}/product-json', 'api\third\MarketplaceController@productJson')->middleware('auth:api')
    ->name('marketplace.campaigns.product-json');

/* Facebook routes */
Route::get('/facebook/token', 'api\third\FacebookController@getToken')->middleware('auth:api')
    ->name('facebook.token.get');
Route::delete('/facebook/token', 'api\third\FacebookController@removeToken')->middleware('auth:api')
    ->name('facebook.token.remove');
Route::get('/facebook/token/refresh', 'api\third\FacebookController@refreshToken')->middleware('auth:api')
    ->name('facebook.token.refresh');
Route::get('/facebook/rules', 'api\third\FacebookController@rules')->middleware('auth:api')
    ->name('facebook.rules');
Route::post('/facebook/rules', 'api\third\FacebookController@createRule')->middleware('auth:api')
    ->name('facebook.rules.store');
Route::put('/facebook/rules/{id}', 'api\third\FacebookController@updateRule')->middleware('auth:api')
    ->name('facebook.rules.update');
Route::delete('/facebook/rules/{id}', 'api\third\FacebookController@deleteRule')->middleware('auth:api')
    ->name('facebook.rules.destroy');
Route::post('/facebook/rules/{id}/run', 'api\third\FacebookController@runRule')->middleware('auth:api')
    ->name('facebook.rules.run');

/*
Route::get('/facebook/advertisers', 'api\third\FacebookController@advertisers')->middleware('auth:api')
    ->name('facebook.advertisers');
Route::post('/facebook/advertisers', 'api\third\FacebookController@storeAdvertiser')->middleware('auth:api')
    ->name('facebook.advertisers.store');
Route::put('/facebook/advertisers/{id}', 'api\third\FacebookController@updateAdvertiser')->middleware('auth:api')
    ->name('facebook.advertisers.update');
Route::delete('/facebook/advertisers/{id}', 'api\third\FacebookController@destroyAdvertiser')->middleware('auth:api')
    ->name('facebook.advertisers.destroy');
Route::get('/facebook/campaigns', 'api\third\FacebookController@campaigns')->middleware('auth:api')
    ->name('facebook.campaigns');
Route::get('/facebook/adsets', 'api\third\FacebookController@adsets')->middleware('auth:api')
    ->name('facebook.adsets');
Route::get('/facebook/ads', 'api\third\FacebookController@ads')->middleware('auth:api')
    ->name('facebook.ads');
Route::put('/facebook/campaigns/{id}', 'api\third\FacebookController@updateCampaign')->middleware('auth:api')
    ->name('facebook.campaigns.update');
Route::put('/facebook/adsets/{id}', 'api\third\FacebookController@updateAdset')->middleware('auth:api')
    ->name('facebook.adsets.update');
Route::put('/facebook/ads/{id}', 'api\third\FacebookController@updateAd')->middleware('auth:api')
    ->name('facebook.ads.update');
*/
Route::post('/facebook/events', 'api\third\FacebookController@sendEvent')
    ->name('facebook.events.trigger');

/* TikTok routes */
Route::get('/tiktok/advertisers', 'api\third\TikTokController@advertisers')->middleware('auth:api')
    ->name('tiktok.advertisers');
Route::post('/tiktok/advertisers', 'api\third\TikTokController@storeAdvertiser')->middleware('auth:api')
    ->name('tiktok.advertisers.store');
Route::put('/tiktok/advertisers/{id}', 'api\third\TikTokController@updateAdvertiser')->middleware('auth:api')
    ->name('tiktok.advertisers.update');
Route::delete('/tiktok/advertisers/{id}', 'api\third\TikTokController@destroyAdvertiser')->middleware('auth:api')
    ->name('tiktok.advertisers.destroy');
Route::get('/tiktok/campaigns', 'api\third\TikTokController@campaigns')->middleware('auth:api')
    ->name('tiktok.campaigns');
Route::get('/tiktok/adgroups', 'api\third\TikTokController@adgroups')->middleware('auth:api')
    ->name('tiktok.adgroups');
Route::get('/tiktok/ads', 'api\third\TikTokController@ads')->middleware('auth:api')
    ->name('tiktok.ads');
Route::put('/tiktok/campaigns/{id}', 'api\third\TikTokController@updateCampaign')->middleware('auth:api')
    ->name('tiktok.campaigns.update');
Route::put('/tiktok/adgroups/{id}', 'api\third\TikTokController@updateAdgroup')->middleware('auth:api')
    ->name('tiktok.adgroups.update');
Route::put('/tiktok/ads/{id}', 'api\third\TikTokController@updateAd')->middleware('auth:api')
    ->name('tiktok.ads.update');
Route::get('/tiktok/images', 'api\third\TikTokController@imageInfo')->middleware('auth:api')
    ->name('tiktok.images');
Route::get('/tiktok/rules', 'api\third\TikTokController@rules')->middleware('auth:api')
    ->name('tiktok.rules');
Route::post('/tiktok/rules', 'api\third\TikTokController@createRule')->middleware('auth:api')
    ->name('tiktok.rules.store');
Route::put('/tiktok/rules/{id}', 'api\third\TikTokController@updateRule')->middleware('auth:api')
    ->name('tiktok.rules.update');
Route::delete('/tiktok/rules/{id}', 'api\third\TikTokController@deleteRule')->middleware('auth:api')
    ->name('tiktok.rules.destroy');
Route::post('/tiktok/rules/{id}/run', 'api\third\TikTokController@runRule')->middleware('auth:api')
    ->name('tiktok.rules.run');
Route::post('/tiktok/events/trigger', 'api\third\TikTokController@triggerEvent')
    ->name('tiktok.events.trigger');

/* Ecommerce rules */
Route::get('/ecommerce/leads', 'api\third\EcommerceController@leads')->middleware('auth:api')
    ->name('ecommerce.leads.index');
Route::post('/ecommerce/leads', 'api\third\EcommerceController@createLead')->middleware('auth:api')
    ->name('ecommerce.leads.create');
Route::put('/ecommerce/leads/{id}', 'api\third\EcommerceController@updateLead')->middleware('auth:api')
    ->name('ecommerce.leads.update');
Route::delete('/ecommerce/leads/{id}', 'api\third\EcommerceController@destroyLead')->middleware('auth:api')
    ->name('ecommerce.leads.destroy');

Route::get('/ecommerce/orders', 'api\third\EcommerceController@orders')->middleware('auth:api')
    ->name('ecommerce.orders.index');
Route::put('/ecommerce/orders/{id}', 'api\third\EcommerceController@updateOrder')->middleware('auth:api')
    ->name('ecommerce.orders.update');
Route::post('/ecommerce/orders/{id}/push', 'api\third\EcommerceController@pushOrder')->middleware('auth:api')
    ->name('ecommerce.orders.push');

Route::get('/ecommerce/customers', 'api\third\EcommerceController@customers')->middleware('auth:api')
    ->name('ecommerce.customers.index');

Route::get('/ecommerce/products', 'api\third\EcommerceController@products')->middleware('auth:api')
    ->name('ecommerce.products.index');
Route::get('/ecommerce/products/{id}', 'api\third\EcommerceController@product')->middleware('auth:api')
    ->name('ecommerce.products.get');
Route::put('/ecommerce/products/{id}', 'api\third\EcommerceController@updateProduct')->middleware('auth:api')
    ->name('ecommerce.products.update');
Route::get('/ecommerce/products/{id}/variations', 'api\third\EcommerceController@productVariations')->middleware('auth:api')
    ->name('ecommerce.products.variations');
Route::put('/ecommerce/products/{pid}/variations/{vid}', 'api\third\EcommerceController@updateProductVariation')->middleware('auth:api')
    ->name('ecommerce.products.variations.update');

Route::get('/ecommerce/payment-methods', 'api\third\EcommerceController@paymentMethods')->middleware('auth:api')
    ->name('ecommerce.payment-methods.index');
Route::put('/ecommerce/payment-methods/{id}/fees', 'api\third\EcommerceController@updatePaymentMethodFees')->middleware('auth:api')
    ->name('ecommerce.products.payment-methods.fees');
Route::get('/ecommerce/advertising/csv', 'api\third\EcommerceController@getAdvertisingCsv')->middleware('auth:api')
    ->name('ecommerce.advertising.csv.get');
Route::put('/ecommerce/advertising/csv', 'api\third\EcommerceController@updateAdvertisingCsv')->middleware('auth:api')
    ->name('ecommerce.advertising.csv.update');
Route::get('/ecommerce/shipping/csv', 'api\third\EcommerceController@getShippingCsv')->middleware('auth:api')
    ->name('ecommerce.shipping.csv.get');
Route::put('/ecommerce/shipping/csv', 'api\third\EcommerceController@updateShippingCsv')->middleware('auth:api')
    ->name('ecommerce.shipping.csv.update');
Route::get('/ecommerce/extra-cost/csv', 'api\third\EcommerceController@getExtraCostCsv')->middleware('auth:api')
    ->name('ecommerce.extra-cost.csv.get');
Route::put('/ecommerce/extra-cost/csv', 'api\third\EcommerceController@updateExtraCostCsv')->middleware('auth:api')
    ->name('ecommerce.extra-cost.csv.update');

Route::get('/ecommerce/carts', 'api\third\EcommerceController@carts')->middleware('auth:api')
    ->name('ecommerce.carts.index');
