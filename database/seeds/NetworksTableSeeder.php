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
                'success_response' => [
                    'status' => 'success',
                ],
            ],
        ]);
        \App\Network::create([
            'name' => 'Amsped',
            'api' => [
                'success_response' => [
                    'error' => false,
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
