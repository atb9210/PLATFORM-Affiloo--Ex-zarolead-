<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormsStyle extends Model
{
    protected $table = 'forms_styles';

    protected $fillable = [
        'name', 'type', 'code',
    ];
}
