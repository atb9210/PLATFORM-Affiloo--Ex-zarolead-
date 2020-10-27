<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'type', 'pathname', 'original_file_name', 'pathname_preview', 'original_file_name_preview',
    ];

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function ads()
    {
        return $this->hasMany('App\Ad');
    }

    public function getStoragePath() : string
    {
        $pathname = env('DROPBOX_FOLDER') . '/product_medias/product_' . $this->product_id;
        return $pathname;
    }

    public function getFilename($what = 'main')
    {
        $file_name = '';
        $to_find_extension = '';
        if ($what == 'preview') {
            $to_find_extension = $this->original_file_name_preview;
        } else {
            $what = 'main';
            $to_find_extension = $this->original_file_name;
        }

        $productMedias = ProductMedia::where('product_id', $this->product_id)->where('type', $this->type);
        $num = $productMedias->count() + 1;
        if ($what == 'main') {
            if ($this->type == 'image') {
                $file_name .= 'F';
            } elseif ($this->type == 'video') {
                $file_name .= 'V';
            } else {
                $file_name .= 'U';
            }
            $file_name .= $num;
        } else {
            $file_name = 'preview' . $num;
        }

        $i = pathinfo($to_find_extension);
        $ext = $i['extension'];
        return $file_name . '.' . $ext;
    }

    public function getStoragePathname($what = 'main')
    {
        return $this->getStoragePath() . '/' . $this->getFilename($what);
    }
}
