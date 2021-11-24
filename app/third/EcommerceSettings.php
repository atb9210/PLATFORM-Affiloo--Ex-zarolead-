<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class EcommerceSettings extends Model
{
    protected $table = 'third_ecommerce_settings';

    protected $fillable = [
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public static function getInstance()
    {
        $obj = EcommerceSettings::first();
        if ($obj === null) {
            $obj = EcommerceSettings::create([
                'settings' => [],
            ]);
        }
        return $obj;
    }

    public static function get()
    {
        $obj = EcommerceSettings::getInstance();
        $settings = $obj->settings;
        return $settings;
    }

    public static function set($newSettings)
    {
        $obj = EcommerceSettings::getInstance();
        $obj->settings = $newSettings;
        $obj->save();
        return $obj->settings;
    }

    public static function getK($key)
    {
        $obj = EcommerceSettings::getInstance();
        $settings = $obj->settings;
        if (isset($settings[$key]))
        {
            return $settings[$key];
        }
        return null;
    }

    public static function setK($key, $value)
    {
        $obj = EcommerceSettings::getInstance();
        $settings = $obj->settings;
        $settings[$key] = $value;
        $obj->settings = $settings;
        $obj->save();
    }
}
