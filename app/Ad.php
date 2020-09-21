<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'traffic_source_id', 'form_id',
        'ctr', 'cpc', 'cpa',
        'info', 'type',
        'pathname', 'original_file_name', 'pathname_preview', 'original_file_name_preview',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $casts = [
        'info' => 'array',
    ];

    public function traffic_source() {
        return $this->belongsTo('App\TrafficSource', 'traffic_source_id');
    }

    public function form() {
        return $this->belongsTo('App\Form', 'form_id');
    }

    public function getStoragePath() {
        $pathname = env('DROPBOX_FOLDER') . '/ads/' . $this->id;
        return $pathname;
    }

    public function getFilename($what = 'main') {
        $file_name = '';
        if ($what == 'preview') {
            $file_name = $this->original_file_name_preview;
        } else {
            $what = 'main';
            $file_name = $this->original_file_name;
        }
        $i = pathinfo($file_name);
        $ext = $i['extension'];
        return $what . '.' . $ext;
    }

    public function getStoragePathname($what = 'main') {
        return $this->getStoragePath() . '/' . $this->getFilename($what);
    }
}
