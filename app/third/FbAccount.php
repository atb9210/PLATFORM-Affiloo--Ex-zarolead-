<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class FbAccount extends Model
{
    protected $table = '3rd_fb_accounts';

    protected $fillable = [
        'user_id', 'name', 'notes', 'token', 'status',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
