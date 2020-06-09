<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            'name' => 'ZaroLead Developer',
            'email' => 'dev@zarolead.com',
            'password' => bcrypt('dev123'),
        ]);
        \App\User::create([
            'name' => 'ZaroLead Admin',
            'email' => 'admin@zarolead.com',
            'password' => bcrypt('admin123'),
        ]);
        factory(App\User::class, 5)->create();
    }
}
