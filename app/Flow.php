<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;

use App\Landing;

class Flow extends Model
{
    protected $fillable = [
        'user_id', 'name', 'type', 'rules', 'path_name',
    ];

    protected $casts = [
        'rules' => 'array',
    ];

    protected static $directory = 'flo';

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public static function buildFullPathname($path_name)
    {
        return public_path(self::$directory . '/' . $path_name);
    }

    public function getFullPathname()
    {
        return self::buildFullPathname($this->path_name);
    }

    public function getPathnameUrl()
    {
        return URL::to(self::$directory . '/' . $this->path_name);
    }

    public function getLandingUrl()
    {
        $result = '';

        $rules = $this->rules;
        if (isset($rules['landings'])) {
            $landings = $rules['landings'];
            if(is_array($landings) && count($landings) > 0) {
                $totalWeight = 0;
                $intervals = [];
                $intervalsCount = 0;
                foreach ($landings as $landing) {
                    if (isset($landing['weight']) && is_numeric($landing['weight'])) {
                        $intervals[] = [
                            'from' => $totalWeight + 1,
                            'to' => $totalWeight + $landing['weight'],
                        ];
                        $totalWeight += $landing['weight'];
                    } else {
                        $intervals[] = [
                            'from' => 0,
                            'to' => 0,
                        ];
                    }
                }

                if ($totalWeight > 0) {
                    $randomNumber = mt_rand(1, $totalWeight);
                }

                $winner = -1;
                for ($i = 0; $i < count($intervals); $i += 1) {
                    if ($randomNumber >= $intervals[$i]['from'] && $randomNumber <= $intervals[$i]['to']) {
                        $winner = $i;
                        break;
                    }
                }

                if ($winner >= 0 && isset($landings[$winner]['id'])) {
                    $landingId = $landings[$winner]['id'];
                    $landingObj = Landing::find($landingId);
                    if ($landingObj != null) {
                        $result = $landingObj->url;
                    }
                }
            }
        }

        return $result;
    }

    public function createFileScript($oldPathName = '')
    {
        if ($oldPathName != '') {
            File::delete(self::buildFullPathname($oldPathName));
        }

        $src = self::buildFullPathname('redirect.php');
        if ($this->type == 'redirect') {
            $src = self::buildFullPathname('redirect.php');
        } elseif ($this->type == 'hotload') {
            $src = self::buildFullPathname('hotload.php');
        }
        $dest = self::buildFullPathname($this->path_name);
        File::copy($src, $dest);

        return true;
    }
}
