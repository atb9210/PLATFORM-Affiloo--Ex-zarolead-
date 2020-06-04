<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        $this->call(UsersTableSeeder::class);
        $this->call(NetworksTableSeeder::class);
        $this->call(TagsTableSeeder::class);
        $this->call(AdvertisersTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(OrdersTableSeeder::class);
        $this->call(RoleUserTableSeeder::class);
        $this->call(CampaignsTableSeeder::class);
        $this->call(TrafficSourcesTableSeeder::class);
        $this->call(FormsTableSeeder::class);
    }
}
