<?php

use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Tag::create([
            'name' => 'Capelli'
        ]);
        \App\Tag::create([
            'name' => 'Barba'
        ]);
        \App\Tag::create([
            'name' => 'Adult'
        ]);
    }
}
