<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public static function getInstance()
    {
        $obj = Settings::first();
        if ($obj === null) {
            $obj = Settings::create([
                'settings' => [],
            ]);
        }
        return $obj;
    }

    public static function get()
    {
        $obj = Settings::getInstance();
        $settings = $obj->settings;
        return $settings;
    }

    public static function set($newSettings)
    {
        $obj = Settings::getInstance();
        $obj->settings = $newSettings;
        $obj->save();
        return $obj->settings;
    }

    public static function getK($key)
    {
        $obj = Settings::getInstance();
        $settings = $obj->settings;
        if (isset($settings[$key]))
        {
            return $settings[$key];
        }
        return null;
    }

    public static function setK($key, $value)
    {
        $obj = Settings::getInstance();
        $settings = $obj->settings;
        $settings[$key] = $value;
        $obj->settings = $settings;
        $obj->save();
    }
}
