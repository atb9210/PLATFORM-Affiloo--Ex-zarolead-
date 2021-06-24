<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'info',
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

    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    public function campaigns()
    {
        return $this->hasMany('App\Campaign');
    }

    public function flows()
    {
        return $this->hasMany('App\Flows');
    }

    public function domains()
    {
        return $this->hasMany('App\Domain');
    }

    public function landings()
    {
        return $this->hasMany('App\Landing');
    }

    public function forms()
    {
        return $this->hasMany('App\Form');
    }

    public function reports()
    {
        return $this->hasMany('App\UserReport');
    }

    public function monthly_reports()
    {
        return $this->hasMany('App\UserMonthlyReport');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_user');
    }

    public function ads()
    {
        return $this->hasMany('App\Ad');
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    public function hasRole(string $roleName): bool
    {
        $result = false;
        foreach ($this->roles as $role) {
            if ($role->name == $roleName) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    public function setOnlyRole(string $roleName): bool
    {
        $roles = Role::all();

        foreach ($roles as $role) {
            if ($this->roles()->where('id', $role->id)->exists()) {
                $this->roles()->detach($role->id);
            }
        }

        $role = Role::where('name', $roleName)->first();
        if ($role === null) {
            return false;
        }
        $this->roles()->attach($role->id);

        return true;
    }

    public function addRole(string $roleName): bool
    {
        $role = Role::where('name', $roleName)->first();
        if ($role === null) {
            return false;
        }
        if (!$this->roles()->where('name', $role->name)->exists()) {
            $this->roles()->attach($role->id);
        }
        return true;
    }

    public function hasPermission(string $object, string $permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($object, $permission)) {
                return true;
            }
        }
        return false;
    }

    public function setPermission(string $permission, string $value): void
    {
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
}
