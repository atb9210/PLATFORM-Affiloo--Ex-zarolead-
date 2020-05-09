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
            'name' => 'Testeron dei Testeroni',
            'email' => 'test@test.com',
            'password' => bcrypt('test123'),
        ]);
        \App\User::create([
            'name' => 'Testeron dei Testeroni 2',
            'email' => 'test2@test.com',
            'password' => bcrypt('test123'),
        ]);
        \App\User::create([
            'name' => 'Testeron dei Testeroni 3',
            'email' => 'test3@test.com',
            'password' => bcrypt('test123'),
        ]);
    }
}
