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
        ]);
        \App\Network::create([
            'name' => 'Amsped',
        ]);
        \App\Network::create([
            'name' => 'Affiliation Park',
        ]);
    }
}
