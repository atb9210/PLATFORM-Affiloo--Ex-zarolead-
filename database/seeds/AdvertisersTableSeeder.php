<?php

use Illuminate\Database\Seeder;

class AdvertisersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Advertiser::create([
            'name' => 'Foltina'
        ]);
        \App\Advertiser::create([
            'name' => 'Tauro'
        ]);
    }
}
