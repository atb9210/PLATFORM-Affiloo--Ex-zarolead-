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
                            'token' => 'HReYRJSUJ77XSm8Aga2bkezMm4udCB', //@TODO
                            'publisher_id' => '8469', //@TODO
                            'company_id' => '5', //@TODO
                            'source' => 'achraf', //@TODO
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
                                'phone' => 'required|string|min:8',
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
                            'phone' => 'required|string|min:8',
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
        \App\Network::create([
            'name' => 'Affiliation Park',
            'api' => [
                'store' => [
                    'request' => [
                        'url' => 'https://new.affiliationpark.com/api/public/603b1940cd40a3e/addorder.php', // TODO: string after public/
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
                            'ip' => '{input.ip}',
                        ],
                        'validation' => [
                            'urlParams' => [
                                'user' => 'required|email',
                                'password' => 'required',
                                'offer' => 'required',
                                'phone' => 'required|string|min:8',
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
                        'surname' => [
                            'type' => 'hidden',
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
                            'phone' => 'required|string|min:8',
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
    }
}
