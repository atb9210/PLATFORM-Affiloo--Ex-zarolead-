<?php

use Illuminate\Database\Seeder;

class TrafficSourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\TrafficSource::create([
            'name' => 'Facebook',
            'custom_params' => [],
        ]);
        \App\TrafficSource::create([
            'name' => 'Google',
            'custom_params' => [],
        ]);
    }
}
