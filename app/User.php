<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'info' => 'array'
    ];

    public function hasPermission($permission) {
        return (isset($this->info['permissions'][$permission]) &&
            $this->info['permissions'][$permission] == true);
    }

    public function setPermission($permission, $value) {
        $data = array();
        if (isset($this->info) && is_array($this->info)) {
            $data = $this->info;
        }

        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $data['permissions'][$permission] = $value;
        } else {
            $data['permissions'] = array(
                $permission => $value
            );
        }
        $this->info = $data;
        $this->save();
    }

    public function orders() {
        return $this->hasMany('App\Order');
    }

    public function forms() {
        return $this->hasMany('App\Form');
    }

    public function invoices() {
        return $this->hasMany('App\Invoice');
    }
}
