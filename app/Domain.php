<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Domain extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain', 'path',
    ];

    public function flows()
    {
        return $this->hasMany('App\Flow');
    }

    public function uploadFile($path, $file, $file_pathname)
    {
        Storage::disk('landings')->putFileAs('apps/' . $path, $file, $file_pathname);
    }

    public function createFile($path, $contents = '')
    {
        Storage::disk('landings')->put('apps/' . $path, $contents);
    }
}
