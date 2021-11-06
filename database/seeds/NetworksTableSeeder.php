<?php

use Illuminate\Database\Seeder;

class NetworksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $phone_regex = '/^[+]{1}[(]{1}[0-9]{1,4}[)]{1}[0-9]{9,}$/';

        $app_url = env('APP_URL', '');

        $url = (env('APP_NETWORK_FAKE_API', false) ? $app_url . '/api/fake/network/worldfilia' : '{form.api_call_url}');
        \App\Network::create([
            'name' => 'Worldfilia',
            'api' => [
                'default_status' => 'confirmed',
                'store' => [
                    'request' => [
                        'url' => $url,
                        'method' => 'post',
                        'type' => 'json',
                        'urlParams' => [
                            'api_key' => '{form.api_key}',
                        ],
                        'bodyParams' => [
                            'source_id' => '{form.source_id}',
                            'aff_sub1' => '{form.aff_sub1}',
                            'aff_sub2' => '{form.aff_sub2}',
                            'name' => '{input.name}',
                            'phone' => '{input.phone}',
                            'address' => '{input.address}',
                            'quantity' => '{input.quantity}',
                            'doubleCheck' => 1,
                        ],
                        'validation' => [
                            'urlParams' => [
                                'api_key' => 'required|min:1',
                            ],
                            'bodyParams' => [
                                'source_id' => 'required|string|min:1',
                                'aff_sub1' => 'string',
                                'aff_sub2' => 'string',
                                'name' => 'required|min:3',
                                'phone' => 'required|string|min:10|regex:' . $phone_regex,
                                'address' => 'required|min:4',
                                'quantity' => 'required',
                                'doubleCheck' => 'in:0,1',
                            ],
                        ],
                    ],
                    'response' => [
                        'type' => 'json',
                        'status' => [
                            'success' => [
                                'validation' => [
                                    'status' => 'required|string|in:success',
                                ],
                            ],
                            'error' => [
                                'validation' => [
                                    'status' => 'required|string|in:error',
                                ],
                            ],
                            'double' => [
                                'validation' => [
                                    'status' => 'required|string|in:double',
                                    'check_url' => 'required|url',
                                ],
                            ],
                            'default' => 'error',
                        ],
                    ],
                    'postback' => [
                        [
                            'on' => 'success',
                            'url' => 'http://ztrk37.com/postback',
                            'method' => 'get',
                            'urlParams' => [
                                'cid' => '{input.sub_id}',
                                'payout' => '{order.commission}',
                            ],
                        ],
                    ],
                    /*
                    'redirect' => [
                        [
                            'on' => 'double',
                            'url' => '{response.check_url}',
                        ],
                    ],
                    */
                ],
                'form' => [
                    'input' => [
                        // Step 1
                        [
                            'name' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Nome e cognome',
                            ],
                            'phone' => [
                                'type' => 'phone',
                                'required' => '',
                                'label' => 'Telefono (meglio cellulare)',
                            ],
                            'address' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Città e indirizzo di consegna'
                            ],
                        ],
                    ],
                    'params' => [
                        'api_key' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'source_id' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'aff_sub1' => [
                            'type' => 'text',
                        ],
                        'aff_sub2' => [
                            'type' => 'text',
                        ],
                        'api_call_url' => [
                            'type' => 'url',
                            'required' => '',
                        ],
                    ],
                    'validation' => [
                        'input' => [
                            'name' => 'required|string|min:4',
                            'phone' => 'required|string|min:10|regex:' . $phone_regex,
                            'address' => 'required|string|min:4',
                        ],
                        'params' => [
                            'api_key' => 'required|string|min:1',
                            'source_id' => 'required|string|min:1',
                            'aff_sub1' => 'string',
                            'aff_sub2' => 'string',
                            'api_call_url' => 'required|url',
                        ],
                    ],
                ],
            ],
        ]);

        $url = (env('APP_NETWORK_FAKE_API', false) ? $app_url . '/api/fake/network/amsped' : 'https://manager.amsped.it/web/service/affiliateNetworkAPI/publisherCreateOrder');
        \App\Network::create([
            'name' => 'Amsped',
            'api' => [
                'default_status' => 'confirmed',
                'store' => [
                    'request' => [
                        'url' => $url,
                        'method' => 'post',
                        'type' => 'json',
                        'bodyParams' => [
                            'token' => 'HReYRJSUJ77XSm8Aga2bkezMm4udCB', //@TODO
                            'publisher_id' => '18456', //@TODO
                            'company_id' => '15', //@TODO
                            'source' => 'ZARO', //@TODO
                            'phone' => '{input.phone}',
                            'name' => '{input.name}',
                            'quantity' => '{input.quantity}',
                            'price' => '{input.price}',
                            'payment_type' => 'c', //@TODO
                            'city' => '',
                            'state' => '',
                            'zip' => '',
                            'address' => '{input.address}',
                            'country' => 'IT', //@TODO
                            'external_notes' => '',
                            'product_reference' => '{form.product_reference}',
                            'product_name' => '{form.product_name}',
                            'ip_address' => '{input.ip}',
                        ],
                        'validation' => [
                            'urlParams' => [
                            ],
                            'bodyParams' => [
                                'token' => 'required|string',
                                'publisher_id' => 'required|string',
                                'company_id' => 'required|numeric|min:1',
                                'source' => 'required|string',
                                'phone' => 'required|string|min:10|regex:' . $phone_regex,
                                'name' => 'required|string|min:3',
                                'quantity' => 'required',
                                'price' => 'required|numeric',
                                'payment_type' => 'required|in:c,p',
                                'city' => 'string',
                                'state' => 'string',
                                'zip' => 'string',
                                'address' => 'required|string|min:4',
                                'country' => 'string',
                                'external_notes' => 'string',
                                'product_reference' => 'required|string',
                                'product_name' => 'required|string',
                                'ip_address' => 'string',
                            ],
                        ],
                    ],
                    'response' => [
                        'type' => 'json',
                        'status' => [
                            'success' => [
                                'validation' => [
                                    'error' => 'required|boolean|is_false',
                                ],
                            ],
                            'error' => [
                                'validation' => [
                                    'error' => 'required|boolean|is_true',
                                ],
                            ],
                            'default' => 'error',
                        ],
                    ],
                    'postback' => [
                        [
                            'on' => 'success',
                            'url' => 'http://ztrk37.com/postback',
                            'method' => 'get',
                            'urlParams' => [
                                'cid' => '{input.sub_id}',
                                'payout' => '{order.commission}',
                            ],
                        ],
                    ],
                ],
                'form' => [
                    'input' => [
                        // Step 1
                        [
                            'name' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Nome e cognome',
                            ],
                            'phone' => [
                                'type' => 'phone',
                                'required' => '',
                                'label' => 'Telefono (meglio cellulare)',
                            ],
                            'address' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Città e indirizzo di consegna'
                            ],
                        ],
                    ],
                    'params' => [
                        'product_reference' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'product_name' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                    ],
                    'validation' => [
                        'input' => [
                            'name' => 'required|string|min:4',
                            'phone' => 'required|string|min:10|regex:' . $phone_regex,
                            'address' => 'required|string|min:4',
                        ],
                        'params' => [
                            'product_reference' => 'required|string|min:1',
                            'product_name' => 'required|string|min:1',
                        ],
                    ],
                ],
            ],
        ]);

        $url = (env('APP_NETWORK_FAKE_API', false) ? $app_url . '/api/fake/network/affiliation-park' : 'https://new.affiliationpark.com/api/public/603b1940cd40a3e/addorder.php');
        \App\Network::create([
            'name' => 'Affiliation Park',
            'api' => [
                'default_status' => 'confirmed',
                'store' => [
                    'request' => [
                        'url' => $url,
                        'method' => 'get',
                        'type' => 'json',
                        'urlParams' => [
                            'user' => '{form.user}',
                            'password' => '{form.password}',
                            'offer' => '{form.offer}',
                            'phone' => '{input.phone}',
                            'nome' => '{input.name}',
                            'surname' => '{input.surname}',
                            'quantity' => '{input.quantity}',
                            'price' => '{input.price}',
                            'address' => '{input.address}',
                            'userip' => '{input.ip}',
                        ],
                        'validation' => [
                            'urlParams' => [
                                'user' => 'required|email',
                                'password' => 'required',
                                'offer' => 'required',
                                'phone' => 'required|string|min:10|regex:' . $phone_regex,
                                'nome' => 'required|string|min:3',
                                'surname' => 'required|string|min:3',
                                'quantity' => 'required',
                                'price' => 'required|numeric',
                                'address' => 'required|string|min:4',
                            ],
                            'bodyParams' => [
                            ],
                        ],
                    ],
                    'response' => [
                        'type' => 'json',
                        'status' => [
                            'success' => [
                                'validation' => [
                                    'status' => 'required|boolean|is_true',
                                    'response' => 'required|string|in:Order added',
                                ],
                            ],
                            'default' => 'error',
                        ],
                    ],
                    'postback' => [
                        [
                            'on' => 'success',
                            'url' => 'http://ztrk37.com/postback',
                            'method' => 'get',
                            'urlParams' => [
                                'cid' => '{input.sub_id}',
                                'payout' => '{order.commission}',
                            ],
                        ],
                    ],
                ],
                'form' => [
                    'input' => [
                        // Step 1
                        [
                            'name' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Nome e cognome',
                            ],
                            'phone' => [
                                'type' => 'phone',
                                'required' => '',
                                'label' => 'Telefono (meglio cellulare)',
                            ],
                            'address' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Città e indirizzo di consegna'
                            ],
                            'surname' => [
                                'type' => 'hidden',
                            ],
                        ],
                    ],
                    'params' => [
                        'user' => [
                            'type' => 'email',
                            'required' => '',
                        ],
                        'password' => [
                            'type' => 'password',
                            'required' => '',
                        ],
                        'offer' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                    ],
                    'validation' => [
                        'input' => [
                            'name' => 'required|string|min:4',
                            'surname' => 'required|string|min:4',
                            'phone' => 'required|string|min:10|regex:' . $phone_regex,
                            'address' => 'required|string|min:4',
                        ],
                        'params' => [
                            'user' => 'required|email',
                            'password' => 'required|string|min:6',
                            'offer' => 'required|string',
                        ],
                    ],
                ],
            ],
        ]);

        $url = (env('APP_NETWORK_FAKE_API', false) ? $app_url . '/api/fake/network/zarolead' : $app_url . '/api/3rd/network');
        \App\Network::create([
            'name' => 'ZaroLead',
            'api' => [
                'default_status' => 'confirmed',
                'store' => [
                    'request' => [
                        'url' => $url,
                        'method' => 'post',
                        'type' => 'json',
                        'urlParams' => [
                            'order_id' => '{order.id}',
                            'email' => '{input.email}',
                            'phone' => '{input.phone}',
                            'address' => '{input.address}',
                            'city' => '{input.city}',
                            'zipcode' => '{input.zipcode}',
                            'state' => '{input.state}',
                            'country' => '{input.country}',
                            'quantity' => '{input.quantity}',
                            'price' => '{input.price}',
                            'name' => '{input.name}',
                            'product_name' => '{form.product_name}',
                            'sku' => '{form.sku}',
                            'weight' => '{form.weight}',
                            'transaction_id' => '',
                            'cross_sell' => '{input.cross_sell}',
                            'delivery' => '{input.delivery}',
                        ],
                        'validation' => [
                            'urlParams' => [
                                'order_id' => 'required|integer|exists:orders,id',
                                'email' => 'required|email',
                                'phone' => 'required|string|min:10|regex:' . $phone_regex,
                                'address' => 'required|string|min:4',
                                'city' => 'required|string',
                                'zipcode' => 'required|string',
                                'state' => 'required|string',
                                'country' => 'required|string',
                                'quantity' => 'required|string',
                                'price' => 'required|numeric|min:0',
                                'name' => 'required|string|min:3',
                                'product_name' => 'required|string',
                                'sku' => 'required|string',
                                'weight' => 'required|string',
                                'transaction_id' => 'required|string|min:0',
                                'cross_sell' => 'json',
                                'delivery' => 'required|string|min:0',
                            ],
                        ],
                    ],
                    'response' => [
                        'type' => 'json',
                        'status' => [
                            'success' => [
                                'validation' => [
                                    'status' => 'required|in:success',
                                ],
                            ],
                            'error' => [
                                'validation' => [
                                    'status' => 'required|in:error',
                                ],
                            ],
                            'default' => 'error',
                        ],
                    ],
                    /*
                    // TODO to include postback?
                    'postback' => [
                        [
                            'on' => 'success',
                            'url' => 'http://ztrk37.com/postback',
                            'method' => 'get',
                            'urlParams' => [
                                'cid' => '{input.sub_id}',
                                'payout' => '{order.commission}',
                            ],
                        ],
                    ],
                    */
                ],
                'form' => [
                    'input' => [
                        // Step 1
                        [
                            'name' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Nome e cognome',
                            ],
                            'phone' => [
                                'type' => 'phone',
                                'required' => '',
                                'label' => 'Telefono (meglio cellulare)',
                            ],
                            'address' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Indirizzo e numero civico'
                            ],
                        ],
                        // Step 2
                        [
                            'city' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Città'
                            ],
                            'state' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Provincia'
                            ],
                            'zipcode' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'CAP'
                            ],
                            'country' => [
                                'type' => 'hidden',
                                'required' => '',
                                'label' => 'Stato',
                                'value' => 'ITA',
                            ],
                            'email' => [
                                'type' => 'email',
                                'required' => '',
                                'label' => 'Email',
                            ],
                        ],
                    ],
                    'params' => [
                        'product_name' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'sku' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'weight' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                    ],
                    'validation' => [
                        'input' => [
                            'email' => 'required|email',
                            'name' => 'required|string|min:4',
                            'phone' => 'required|string|min:10|regex:' . $phone_regex,
                            'city' => 'required|string|min:2',
                            'state' => 'required|string|min:2',
                            'zipcode' => 'required|string|min:2',
                            'country' => 'required|string|min:2',
                            'address' => 'required|string|min:4',
                        ],
                        'params' => [
                            'product_name' => 'required|string|min:1',
                            'sku' => 'required|string|min:1',
                        ],
                    ],
                ],
            ],
        ]);

        $url = (env('APP_NETWORK_FAKE_API', false) ? $app_url . '/api/fake/network/finapsy' : 'https://fynapsyhost.com/api/publisher/orders/send');
        \App\Network::create([
            'name' => 'Finapsy',
            'api' => [
                'default_status' => 'confirmed',
                'store' => [
                    'request' => [
                        'url' => $url,
                        'method' => 'post',
                        'type' => 'json',
                        'headers' => [
                            'Content-Type: application/json',
                            'Accept: application/json',
                            'Authorization: Bearer 7|tIuiQ3C6GuFekt46mlAZbwrpx9KXLYYFO0xVSXSqvC2HTrYh9KNRZyCyeltBvaurg6AtWAY24OzXxYCD',
                        ],
                        'urlParams' => [
                            'offer_id' => '{form.offer_id}',
                            'nome' => '{input.name}',
                            'cognome' => '{input.name}',
                            'email' => '{input.email}',
                            'telefono' => '{input.phone}',
                            'qta' => '{input.quantity}',
                        ],
                        'bodyParams' => [
                            'offer_id' => '{form.offer_id}',
                            'nome' => '{input.name}',
                            'cognome' => '{input.name}',
                            'email' => '{input.email}',
                            'telefono' => '{input.phone}',
                            'qta' => '{input.quantity}',
                        ],
                        'validation' => [
                            'urlParams' => [
                            ],
                            'bodyParams' => [
                                'offer_id' => 'required',
                                'nome' => 'required|string|min:3',
                                'cognome' => 'required|string|min:3',
                                'email' => 'required|email',
                                'telefono' => 'required|string|min:10|regex:' . $phone_regex,
                                'qta' => 'required',
                            ],
                        ],
                    ],
                    'response' => [
                        'type' => 'json',
                        'status' => [
                            'success' => [
                                'validation' => [
                                    'message' => 'required|string|in:ordine inviato correttamente!',
                                ],
                            ],
                            'error' => [
                                'validation' => [
                                    'message' => 'required|string|not_in:ordine inviato correttamente!',
                                ],
                            ],
                            'default' => 'error',
                        ],
                    ],
                    'postback' => [
                        [
                            'on' => 'success',
                            'url' => env('APP_URL') . '/api/3rd/finapsy',
                            'method' => 'post',
                            'urlParams' => [
                                'order_id' => '{order.id}',
                                'name' => '{input.name}',
                                'email' => '{input.email}',
                                'phone' => '{input.phone}',
                                'offer_id' => '{form.offer_id}',
                                'quantity' => '{input.quantity}',
                            ],
                            'bodyParams' => [
                                'order_id' => '{order.id}',
                                'name' => '{input.name}',
                                'email' => '{input.email}',
                                'phone' => '{input.phone}',
                                'offer_id' => '{form.offer_id}',
                                'quantity' => '{input.quantity}',
                            ],
                        ],
                    ],
                ],
                'form' => [
                    'input' => [
                        // Step 1
                        [
                            'name' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Nome e cognome',
                            ],
                            'phone' => [
                                'type' => 'phone',
                                'required' => '',
                                'label' => 'Telefono (meglio cellulare)',
                            ],
                            'email' => [
                                'type' => 'email',
                                'required' => '',
                                'label' => 'Email'
                            ],
                        ],
                    ],
                    'params' => [
                        'offer_id' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                    ],
                    'validation' => [
                        'input' => [
                            'name' => 'required|string|min:4',
                            'phone' => 'required|string|min:10|regex:' . $phone_regex,
                            'email' => 'required|email',
                        ],
                        'params' => [
                            'offer_id' => 'required|string|min:1',
                        ],
                    ],
                ],
            ],
        ]);

        $url = (env('APP_NETWORK_FAKE_API', false) ? $app_url . '/api/fake/network/limitless' : 'https://app.limitl3ssnetwork.com/api/affiliate/leads/create');
        \App\Network::create([
            'name' => 'Limitless',
            'api' => [
                'default_status' => 'waiting',
                'store' => [
                    'request' => [
                        'url' => $url,
                        'method' => 'post',
                        'type' => 'json',
                        'urlParams' => [
                        ],
                        'bodyParams' => [
                            'apikey' => '{form.apikey}',
                            'sub_id' => '{order.id}',
                            'country' => '{form.country}',
                            'affiliate_id' => '{form.affiliate_id}',
                            'offer_id' => '{form.offer_id}',
                            'lid' => '{form.lid}',
                            'quantity' => '{form.quantity}',
                            'amount' => '{form.amount}',
                            'firstname' => '{input.name}',
                            'lastname' => '{input.surname}',
                            'phone' => '{input.phone}',
                            'address' => '{input.address}',
                            'city' => '{input.address}',
                        ],
                        'validation' => [
                            'urlParams' => [
                            ],
                            'bodyParams' => [
                                'apikey' => 'required|string|min:1',
                                'sub_id' => 'required',
                                'country' => 'required|string|min:1',
                                'affiliate_id' => 'required|string|min:1',
                                'offer_id' => 'required|string|min:1',
                                'lid' => 'required|string|min:1',
                                'quantity' => 'required|string|min:1',
                                'amount' => 'required|string|min:1',
                                'firstname' => 'required|min:3',
                                'lastname' => 'required|min:3',
                                'phone' => 'required|string|min:10|regex:' . $phone_regex,
                                'address' => 'required|min:4',
                                'city' => 'required|min:1',
                            ],
                        ],
                    ],
                    'response' => [
                        'type' => 'json',
                        'status' => [
                            'success' => [
                                'validation' => [
                                    'status' => 'required|string|in:ok',
                                ],
                            ],
                            'default' => 'error',
                        ],
                    ],
                ],
                'form' => [
                    'input' => [
                        // Step 1
                        [
                            'name' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Nome e cognome',
                            ],
                            'phone' => [
                                'type' => 'phone',
                                'required' => '',
                                'label' => 'Telefono (meglio cellulare)',
                            ],
                            'address' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Città e indirizzo di consegna'
                            ],
                        ],
                    ],
                    'params' => [
                        'apikey' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'country' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'affiliate_id' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'offer_id' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'lid' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'quantity' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'amount' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                    ],
                    'validation' => [
                        'input' => [
                            'name' => 'required|string|min:4',
                            'phone' => 'required|string|min:10|regex:' . $phone_regex,
                            'address' => 'required|string|min:4',
                        ],
                        'params' => [
                            'apikey' => 'required|string|min:1',
                            'country' => 'required|string|min:1',
                            'affiliate_id' => 'required|string|min:1',
                            'offer_id' => 'required|string|min:1',
                            'lid' => 'required|string|min:1',
                            'quantity' => 'required|string|min:1',
                            'amount' => 'required|string|min:1',
                        ],
                    ],
                ],
            ],
        ]);

        $url = (env('APP_NETWORK_FAKE_API', false) ? $app_url . '/api/fake/network/profitparadise' : 'https://new-tech.co/forms/api/');
        \App\Network::create([
            'name' => 'ProfitParadise',
            'api' => [
                'default_status' => 'confirmed',
                'store' => [
                    'request' => [
                        'url' => $url,
                        'method' => 'post',
                        'type' => 'json',
                        'urlParams' => [
                        ],
                        'bodyParams' => [
                            'uid' => '{form.uid}',
                            'offer' => '{form.offer}',
                            'lp' => '{form.lp}',
                            'name' => '{input.name}',
                            'tel' => '{input.phone}',
                            'street-address' => '{input.address}',
                        ],
                        'validation' => [
                            'urlParams' => [
                            ],
                            'bodyParams' => [
                                'uid' => 'required|string|min:1',
                                'offer' => 'required|string|min:1',
                                'lp' => 'required|string|min:1',
                                'name' => 'required|min:3',
                                'tel' => 'required|string|min:10|regex:' . $phone_regex,
                                'street-address' => 'required|min:4',
                            ],
                        ],
                    ],
                    'response' => [
                        'type' => 'json',
                        'status' => [
                            'success' => [
                                'validation' => [
                                    'code' => 'required|numeric|min:200|max:200',
                                    'message' => 'required|string|in:OK',
                                ],
                            ],
                            'double' => [
                                'validation' => [
                                    'code' => 'required|numeric|min:200|max:200',
                                    'message' => 'required|string|in:DOUBLE',
                                ],
                            ],
                            'default' => 'error',
                        ],
                    ],
                ],
                'form' => [
                    'input' => [
                        // Step 1
                        [
                            'name' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Nome e cognome',
                            ],
                            'phone' => [
                                'type' => 'phone',
                                'required' => '',
                                'label' => 'Telefono (meglio cellulare)',
                            ],
                            'address' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Città e indirizzo di consegna'
                            ],
                        ],
                    ],
                    'params' => [
                        'uid' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'offer' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'lp' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                    ],
                    'validation' => [
                        'input' => [
                            'name' => 'required|string|min:4',
                            'phone' => 'required|string|min:10|regex:' . $phone_regex,
                            'address' => 'required|string|min:4',
                        ],
                        'params' => [
                            'uid' => 'required|string|min:1',
                            'offer' => 'required|string|min:1',
                            'lp' => 'required|string|min:1',
                        ],
                    ],
                ],
            ],
        ]);

        $url = (env('APP_NETWORK_FAKE_API', false) ? $app_url . '/api/fake/network/ecommerce/leads' : $app_url . '/api/3rd/ecommerce/leads');
        \App\Network::create([
            'name' => 'Ecommerce',
            'api' => [
                'default_status' => 'waiting',
                'store' => [
                    'request' => [
                        'url' => $url,
                        'method' => 'post',
                        'type' => 'json',
                        'urlParams' => [
                            'first_name' => '{input.name}',
                            'last_name' => '{input.name}',
                            'phone' => '{input.phone}',
                            'order_id' => '{order.id}',
                            'sku' => '{form.sku}',
                            'product_name' => '{form.product_name}',
                            'price' => '{input.price}',
                            'total' => '{input.price}',
                            'quantity' => '{input.quantity}',
                        ],
                        'validation' => [
                            'urlParams' => [
                                'first_name' => 'required|string|min:3',
                                'last_name' => 'required|string|min:3',
                                'phone' => 'required|string|min:10|regex:' . $phone_regex,
                                'order_id' => 'required|integer|exists:orders,id',
                                'sku' => 'required|string',
                                'product_name' => 'required|string',
                                'price' => 'required|numeric|min:0',
                                'total' => 'required|numeric|min:0',
                                'quantity' => 'required|string',
                            ],
                        ],
                    ],
                    'response' => [
                        'type' => 'json',
                        'status' => [
                            'success' => [
                                'validation' => [
                                    'status' => 'required|in:success',
                                ],
                            ],
                            'default' => 'error',
                        ],
                    ],
                ],
                'form' => [
                    'input' => [
                        // Step 1
                        [
                            'name' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Nome e cognome',
                            ],
                            'phone' => [
                                'type' => 'phone',
                                'required' => '',
                                'label' => 'Telefono (meglio cellulare)',
                            ],
                            'address' => [
                                'type' => 'text',
                                'required' => '',
                                'label' => 'Indirizzo e numero civico'
                            ],
                        ],
                    ],
                    'params' => [
                        'product_name' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                        'sku' => [
                            'type' => 'text',
                            'required' => '',
                        ],
                    ],
                    'validation' => [
                        'input' => [
                            'name' => 'required|string|min:4',
                            'phone' => 'required|string|min:10|regex:' . $phone_regex,
                            'address' => 'required|string|min:4',
                        ],
                        'params' => [
                            'product_name' => 'required|string|min:1',
                            'sku' => 'required|string|min:1',
                        ],
                    ],
                ],
            ],
        ]);

    }
}
