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
