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
            'ad_model' => [
                'params' => [
                    'title' => [
                        'type' => 'text',
                        'required' => '',
                    ],
                    'text' => [
                        'type' => 'text',
                        'required' => '',
                    ],
                ],
                'validation' => [
                    'params' => [
                        'title' => 'required|string|min:1',
                        'text' => 'required|string|min:1',
                    ],
                ],
            ],
        ]);
        \App\TrafficSource::create([
            'name' => 'Google',
            'custom_params' => [],
            'ad_model' => [],
        ]);
    }
}
