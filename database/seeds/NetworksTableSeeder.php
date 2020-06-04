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
        \App\Network::create([
            'name' => 'Worldfilia',
            'api' => [
                'store' => [
                    'request' => [
                        'url' => '{form.api_call_url}',
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
                                'phone' => 'required|min:8',
                                'address' => 'required|min:4',
                                'quantity' => 'required|min:1',
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
                    'redirect' => [
                        [
                            'on' => 'double',
                            'url' => '{response.check_url}',
                        ],
                    ],
                ],
                'form' => [
                    'input' => [
                        'name' => [
                            'type' => 'text',
                            'required' => '',
                            'label' => 'Nome e cognome',
                        ],
                        'phone' => [
                            'type' => 'text',
                            'required' => '',
                            'label' => 'Telefono (meglio cellulare)',
                        ],
                        'address' => [
                            'type' => 'text',
                            'required' => '',
                            'label' => 'Città e indirizzo di consegna'
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
                            'phone' => 'required|string|min:8',
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
        \App\Network::create([
            'name' => 'Amsped',
            'api' => [
                'store' => [
                    'request' => [
                        'url' => 'https://manager.amsped.it/web/service/affiliateNetworkAPI/publisherCreateOrder',
                        'method' => 'post',
                        'type' => 'json',
                        'bodyParams' => [
                            'token' => 'HReYRJSUJ77XSm8Aga2bkezMm4udCB',
                            'publisher_id' => '8469',
                            'company_id' => '5',
                            'source' => 'achraf',
                            'name' => '{input.name}',
                            'phone' => '{input.phone}',
                            'address' => '{input.address}',
                            'quantity' => '{input.quantity}',
                            'price' => '{input.price}',
                            'payment_type' => '{form.payment_type}',
                            'city' => '',
                            'state' => '',
                            'zip' => '',
                            'country' => 'IT',
                            'external_notes' => '',
                            'product_reference' => '{form.product_reference}',
                            'product_name' => '{form.product_id}',
                            'ip_address' => '127.0.0.1',
                        ],
                        'validation' => [
                            'urlParams' => [
                                'api_key' => 'required|min:1',
                            ],
                            'bodyParams' => [
                                'token' => 'required|string|min:1',
                                'publisher_id' => 'required|string|min:1',
                                'company_id' => 'required|numeric|min:1',
                                'source' => 'required|string',
                                'name' => 'required|string|min:3',
                                'phone' => 'required|string|min:8',
                                'address' => 'required|string|min:4',
                                'quantity' => 'required|string|min:1',
                                'price' => 'required|numeric',
                                'payment_type' => '{form.payment_type}', // @TODO:
                                'city' => 'string',
                                'state' => 'string',
                                'zip' => 'string',
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
                                    'error' => 'required|boolean|in:false',
                                ],
                            ],
                            'error' => [
                                'validation' => [
                                    'error' => 'required|boolean|in:true',
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
                                'payout' => '{form.commission}',
                            ],
                        ],
                    ],
                ],
                'form' => [
                    'validation' => [
                        'params' => [
                            'api_key' => 'required|string|min:1',
                            'source_id' => 'required|string|min:1',
                            'aff_sub1' => 'string',
                            'aff_sub2' => 'string',
                            'api_call_url' => 'required|string',
                        ],
                    ],
                ],
            ],
        ]);
        \App\Network::create([
            'name' => 'Affiliation Park',
            'api' => [
                'success_response' => [
                    'status' => true,
                    'response' => 'Order added'
                ],
            ],
        ]);
    }
}
