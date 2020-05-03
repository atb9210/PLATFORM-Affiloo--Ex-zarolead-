<?php

use Illuminate\Database\Seeder;

class OrderStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('order_statuses')->insert([
        'description' => 'Attesa',
      ]);
      DB::table('order_statuses')->insert([
        'description' => 'Conferma',
      ]);
      DB::table('order_statuses')->insert([
        'description' => 'Non risponde',
      ]);
      DB::table('order_statuses')->insert([
        'description' => 'Rifiuto',
      ]);
    }
}
