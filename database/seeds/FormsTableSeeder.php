<?php

use Illuminate\Database\Seeder;

class FormsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        \App\Form::create([
            'user_id' => 1,
            'name' => 'My Form',
            'campaign_id' => 1,
            'network_id' => 1,
            'product_id' => 1,
            'quantity' => [
                'options' => [
                    '1' => [
                        'price' => 99.99,
                        'commission' => 49.99,
                        'sub_commission' => 9.99,
                    ],
                    '3x2' => [
                        'price' => 199.98,
                        'commission' => 79.99,
                        'sub_commission' => 39.99,
                    ],
                ],
                'default' => '1',
            ],
            'upsell' => [
                'enabled' => true,
                'quantity' => '3x2',
            ],
            'traffic_source_id' => 1,
            'info' => json_decode('{"build_data":{"name":"My Form","campaign_id":1,"network_id":1,"product_id":1,"quantity":"1","price":"99.99","commission":"49.99","sub_commission":"9.99","traffic_source_id":1,"network_params":{"fb_pixel":"","api_call_url":"https://network.worldfilia.net/manager/inventory/buy/htf_immunodefender.json","api_call_method":"POST","api_key":"fb6e13a02e3027cba7e7fd5bf47c4eaa","source_id":"f508afaa848f"},"user_id":1,"form_id":1,"network":"Worldfilia"},"network":{"name":"Worldfilia","api_call_url":"https:\/\/network.worldfilia.net\/manager\/inventory\/buy\/htf_immunodefender.json","api_success_response":"{\"status\":\"success\"}"}}'),
            'network_params' => [
                'fb_pixel' => '',
                'api_call_url' => 'https://network.worldfilia.net/manager/inventory/buy/htf_immunodefender.json',
                'api_call_method' => 'post',
                'api_key' => 'fb6e13a02e3027cba7e7fd5bf47c4eaa',
                'source_id' => 'f508afaa848f',
                'aff_sub1' => '',
                'aff_sub2' => '',
            ],
        ]);
        */
    }
}
