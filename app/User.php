<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'info',
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

    public function hasPermission($object, $permission) {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($object, $permission)) {
                return true;
            }
        }
        return false;
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

    public function campaigns() {
        return $this->hasMany('App\Campaign');
    }

    public function flows() {
        return $this->hasMany('App\Flows');
    }

    public function forms() {
        return $this->hasMany('App\Form');
    }

    public function reports() {
        return $this->hasMany('App\UserReport');
    }

    public function roles() {
        return $this->belongsToMany('App\Role', 'role_user');
    }

    public function invoices() {
        return $this->hasMany('App\Invoice');
    }
}
