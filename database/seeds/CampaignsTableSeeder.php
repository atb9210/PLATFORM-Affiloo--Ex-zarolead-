<?php

use Illuminate\Database\Seeder;

class CampaignsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Campaign::create([
            'name' => 'New campaign',
            'user_id' => 1,
        ]);
        \App\Campaign::create([
            'name' => 'New campaign #2',
            'user_id' => 1,
        ]);
    }
}
