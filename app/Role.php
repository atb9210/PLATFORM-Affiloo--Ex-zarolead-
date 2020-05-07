<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'permissions',
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

    public function users() {
        return $this->belongsToMany('App\User', 'role_user');
    }

    public function hasPermission($object, $action) {
        if (isset($this->permissions[$object]["*"]) && $this->permissions[$object]["*"]) {
            return true;
        }

        if (isset($this->permissions[$object][$action]) && $this->permissions[$object][$action]) {
            return true;
        }

        return false;
    }
}
