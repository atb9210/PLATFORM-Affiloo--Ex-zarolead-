<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Order::class, function (Faker $faker) {
    return [
        'user_id' => $faker->randomElement([1, 3]),
        'name' => $faker->name,
        'phone' => $faker->e164PhoneNumber,
        'address' => $faker->address,
        'product_id' => $faker->randomElement([1, 2, 3]),
        'quantity' => 1,
        'commission' => $faker->randomElement([20, 29.99]),
        'sub_commission' => $faker->randomElement([10, 12]),
        'network_id' => $faker->randomElement([1, 2, 3]),
        'order_status_id' => 1,
        'created_at' => $faker->dateTimeBetween($startDate = '-7 days'),
    ];
});
