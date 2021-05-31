<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

use Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
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
        Validator::extend('landing_path', function($attribute, $value, $parameters, $validator) {
            return preg_match('/^[A-Za-z]{1}[A-Za-z0-9-]+[A-Za-z0-9]{1}$/', $value);
        });

        // SFTP driver
        Storage::extend('landings', function ($app, $config) {
            return new Filesystem(new SftpAdapter($config));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
