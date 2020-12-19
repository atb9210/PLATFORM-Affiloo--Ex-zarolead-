<?php

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Product::create([
            'name' => 'Foltina Plus Capelli',
            'user_id' => 2,
            'tag_id' => 1,
            'advertiser_id' => 1,
            'country' => 'italy',
            'quantity' => [
                'default' => 1,
                'options' => [
                    '1' => [
                        'price' => '59.00',
                        'commission' => '16.00',
                        'sub_commission' => '00.00',
                    ],
                ],
            ],
        ]);
        \App\Product::create([
            'name' => 'Foltina Plus Barba',
            'user_id' => 2,
            'tag_id' => 2,
            'advertiser_id' => 1,
            'country' => 'italy',
            'quantity' => [
                'default' => 1,
                'options' => [
                    '1' => [
                        'price' => '59.00',
                        'commission' => '16.00',
                        'sub_commission' => '00.00',
                    ],
                ],
            ],
        ]);
        \App\Product::create([
            'name' => 'Tauro Plus',
            'user_id' => 2,
            'tag_id' => 3,
            'advertiser_id' => 2,
            'country' => 'italy',
            'quantity' => [
                'default' => 1,
                'options' => [
                    '1' => [
                        'price' => '59.00',
                        'commission' => '16.00',
                        'sub_commission' => '00.00',
                    ],
                ],
            ],
        ]);
    }
}
