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

Route::get('/facebook/advertisers', 'api\third\FacebookController@advertisers')->middleware('auth:api')
    ->name('facebook.advertisers');
Route::get('/facebook/advertisers/{id}', 'api\third\FacebookController@getAdvertiser')->middleware('auth:api')
    ->name('facebook.advertisers.show');
Route::post('/facebook/advertisers', 'api\third\FacebookController@storeAdvertiser')->middleware('auth:api')
    ->name('facebook.advertisers.store');
Route::put('/facebook/advertisers/{id}', 'api\third\FacebookController@updateAdvertiser')->middleware('auth:api')
    ->name('facebook.advertisers.update');
Route::delete('/facebook/advertisers/{id}', 'api\third\FacebookController@destroyAdvertiser')->middleware('auth:api')
    ->name('facebook.advertisers.destroy');

Route::get('/facebook/copys', 'api\third\FacebookController@getCopys')->middleware('auth:api')
    ->name('facebook.copys');
Route::get('/facebook/copys/{id}', 'api\third\FacebookController@getCopy')->middleware('auth:api')
    ->name('facebook.copys.show');
Route::post('/facebook/copys', 'api\third\FacebookController@createCopy')->middleware('auth:api')
    ->name('facebook.copys.store');
Route::put('/facebook/copys/{id}', 'api\third\FacebookController@updateCopy')->middleware('auth:api')
    ->name('facebook.copys.update');
Route::delete('/facebook/copys/{id}', 'api\third\FacebookController@deleteCopy')->middleware('auth:api')
    ->name('facebook.copys.delete');

Route::get('/facebook/monitors', 'api\third\FacebookController@getMonitors')->middleware('auth:api')
    ->name('facebook.monitors');
Route::get('/facebook/monitors/{id}', 'api\third\FacebookController@getMonitor')->middleware('auth:api')
    ->name('facebook.monitors.show');
Route::post('/facebook/monitors', 'api\third\FacebookController@createMonitor')->middleware('auth:api')
    ->name('facebook.monitors.store');
Route::put('/facebook/monitors/{id}', 'api\third\FacebookController@updateMonitor')->middleware('auth:api')
    ->name('facebook.monitors.update');
Route::delete('/facebook/monitors/{id}', 'api\third\FacebookController@deleteMonitor')->middleware('auth:api')
    ->name('facebook.monitors.delete');
Route::get('/facebook/monitors/{id}/actions', 'api\third\FacebookController@getMonitorActions')->middleware('auth:api')
    ->name('facebook.monitors.actions.index');


/*
Route::get('/facebook/adaccounts', 'api\third\FacebookController@adAccounts')->middleware('auth:api')
    ->name('facebook.adaccounts');
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

/* Ecommerce routes */
Route::get('/ecommerce/orders', 'api\third\EcommerceController@orders')->middleware('auth:api')
    ->name('ecommerce.orders.index');
Route::post('/ecommerce/orders', 'api\third\EcommerceController@createOrder')->middleware('auth:api')
    ->name('ecommerce.orders.create');
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
Route::put('/ecommerce/carts/{id}', 'api\third\EcommerceController@updateCart')->middleware('auth:api')
    ->name('ecommerce.carts.update');
Route::delete('/ecommerce/carts/{id}', 'api\third\EcommerceController@destroyCart')->middleware('auth:api')
    ->name('ecommerce.carts.destroy');

/* Warehouse routes */
Route::get('/warehouse/leads', 'api\third\WarehouseController@leads')->middleware('auth:api')
    ->name('warehouse.leads.index');
Route::post('/warehouse/leads', 'api\third\WarehouseController@createLead')
    ->name('warehouse.leads.create');
Route::put('/warehouse/leads/{id}', 'api\third\WarehouseController@updateLead')->middleware('auth:api')
    ->name('warehouse.leads.update');
Route::delete('/warehouse/leads/{id}', 'api\third\WarehouseController@destroyLead')->middleware('auth:api')
    ->name('warehouse.leads.destroy');

Route::get('/warehouse/leads/{lead_id}/delivery', 'api\third\WarehouseController@getDelivery')->middleware('auth:api')
    ->name('warehouse.delivery.get');
Route::post('/warehouse/delivery', 'api\third\WarehouseController@createDelivery')->middleware('auth:api')
    ->name('warehouse.delivery.create');
Route::put('/warehouse/delivery', 'api\third\WarehouseController@updateDelivery')->middleware('auth:api')
    ->name('warehouse.delivery.update');
Route::get('/warehouse/products', 'api\third\WarehouseController@products')->middleware('auth:api')
    ->name('warehouse.products.index');

Route::post('/warehouse/send-message', 'api\third\WarehouseController@sendMessage')->middleware('auth:api')
    ->name('warehouse.message.send');

Route::get('/warehouse/leads/{id}/info', 'api\third\WarehouseController@getLeadInfo')
    ->name('warehouse.leads.info');
Route::put('/warehouse/leads/{hash}/update', 'api\third\WarehouseController@publicUpdateLead')
    ->name('warehouse.leads.update.public');
Route::delete('/warehouse/leads/{hash}/delete', 'api\third\WarehouseController@publicDeleteLead')
    ->name('warehouse.leads.delete.public');
Route::post('/warehouse/leads/{lead_id}/address', 'api\third\WarehouseController@updateLeadAddress')
    ->name('warehouse.leads.address.update');
Route::post('/warehouse/leads/{lead_id}/complete', 'api\third\WarehouseController@completeLead')
    ->name('warehouse.leads.complete');

Route::put('/warehouse/leads/{hash}/first-update', 'api\third\WarehouseController@firstUpdateLead')
    ->name('warehouse.leads.update.first');

// TODO: Move this somewhere else???
// Also change public/js/checkout.ts
Route::get('/warehouse/products/{product_id}/payment-intent', 'api\third\WarehouseController@getPaymentIntentProduct')
    ->name('warehouse.products.payment-intent.product');
Route::get('/warehouse/products/payment-intent', 'api\third\WarehouseController@getPaymentIntent')
    ->name('warehouse.products.payment-intent');

/* Dropin routes */
Route::get('/dropin/leads', 'api\third\DropinController@leads')->middleware('auth:api')
    ->name('dropin.leads.index');
Route::get('/dropin/leads/{id}', 'api\third\DropinController@getLead')->middleware('auth:api')
    ->name('dropin.leads.show');
Route::post('/dropin/leads', 'api\third\DropinController@createLead')
    ->name('dropin.leads.create');
Route::put('/dropin/leads/{id}', 'api\third\DropinController@updateLead')->middleware('auth:api')
    ->name('dropin.leads.update');
Route::delete('/dropin/leads/{id}', 'api\third\DropinController@destroyLead')->middleware('auth:api')
    ->name('dropin.leads.destroy');

Route::get('/dropin/leads/{lead_id}/delivery', 'api\third\DropinController@getDelivery')->middleware('auth:api')
    ->name('dropin.delivery.get');
Route::post('/dropin/delivery', 'api\third\DropinController@createDelivery')->middleware('auth:api')
    ->name('dropin.delivery.create');
Route::put('/dropin/delivery', 'api\third\DropinController@updateDelivery')->middleware('auth:api')
    ->name('dropin.delivery.update');

Route::get('/dropin/products', 'api\third\DropinController@products')->middleware('auth:api')
    ->name('dropin.products.index');
Route::post('/dropin/products', 'api\third\DropinController@createProduct')->middleware('auth:api')
    ->name('dropin.products.store');
Route::put('/dropin/products/{sku}', 'api\third\DropinController@updateProduct')->middleware('auth:api')
    ->name('dropin.products.update');
Route::delete('/dropin/products/{sku}', 'api\third\DropinController@destroyProduct')->middleware('auth:api')
    ->name('dropin.products.destroy');
Route::get('/dropin/products-dropin', 'api\third\DropinController@getProductsDropin')->middleware('auth:api')
    ->name('dropin.products.dropin');

Route::get('/dropin/leads/{id}/messages', 'api\third\DropinController@getLeadMessages')->middleware('auth:api')
    ->name('dropin.leads.messages.index');
Route::post('/dropin/leads/{id}/messages', 'api\third\DropinController@sendLeadMessage')->middleware('auth:api')
    ->name('dropin.leads.messages.send');
// TODO: delete this? Do we still need it
Route::post('/dropin/send-message', 'api\third\DropinController@sendMessage')->middleware('auth:api')
    ->name('dropin.messages.send');
Route::get('/dropin/leads/messages/webhook', 'api\third\DropinController@leadMessagesWebhook')
    ->name('dropin.leads.messages.webhook');
Route::post('/dropin/leads/messages/webhook', 'api\third\DropinController@leadMessagesWebhook')
    ->name('dropin.leads.messages.webhook');

Route::get('/dropin/leads/{id}/info', 'api\third\DropinController@getLeadInfo')
    ->name('dropin.leads.info');
Route::put('/dropin/leads/{hash}/update', 'api\third\DropinController@publicUpdateLead')
    ->name('dropin.leads.update.public');
Route::delete('/dropin/leads/{hash}/delete', 'api\third\DropinController@publicDeleteLead')
    ->name('dropin.leads.delete.public');
Route::post('/dropin/leads/{lead_id}/address', 'api\third\DropinController@updateLeadAddress')
    ->name('dropin.leads.address.update');
Route::post('/dropin/leads/{lead_id}/complete', 'api\third\DropinController@completeLead')
    ->name('dropin.leads.complete');

// TODO: Move this somewhere else???
// Also change public/js/checkout.ts
Route::get('/dropin/products/{product_id}/payment-intent', 'api\third\DropinController@getPaymentIntentProduct')
    ->name('dropin.products.payment-intent.product');
Route::get('/dropin/products/payment-intent', 'api\third\DropinController@getPaymentIntent')
    ->name('dropin.products.payment-intent');

Route::put('/dropin/leads/{hash}/first-update', 'api\third\DropinController@firstUpdateLead')
    ->name('dropin.leads.update.first');

Route::get('/dropin/shippings', 'api\third\DropinController@shippings')->middleware('auth:api')
    ->name('dropin.shippings.index');
Route::post('/dropin/shippings', 'api\third\DropinController@createShippings')->middleware('auth:api')
    ->name('dropin.shippings.store');
Route::put('/dropin/shippings/{id}', 'api\third\DropinController@updateShipping')->middleware('auth:api')
    ->name('dropin.shippings.update');
Route::get('/dropin/shippings/refresh', 'api\third\DropinController@refreshShippingsTable')->middleware('auth:api')
    ->name('dropin.shippings.refresh-all');
Route::get('/dropin/shippings/{id}/refresh', 'api\third\DropinController@refreshShipping')->middleware('auth:api')
    ->name('dropin.shippings.refresh');
