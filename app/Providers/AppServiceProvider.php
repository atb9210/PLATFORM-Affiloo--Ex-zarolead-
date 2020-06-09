<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider {
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        // TODO: Is it still needed in Laravel 7.x?
        // Prevent key length error
        Schema::defaultStringLength(191);

        // Custom validator functions
        Validator::extend('is_true', function($attribute, $value, $parameters, $validator) {
            return ($value === 'true' || $value === true || $value === '1' || $value === 1);
        });
        Validator::extend('is_false', function($attribute, $value, $parameters, $validator) {
            return ($value === 'false' || $value === false || $value === '0' || $value === 0);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }
}
