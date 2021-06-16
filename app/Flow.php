<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

use App\Landing;

class Flow extends Model
{
    protected $fillable = [
        'user_id', 'name', 'type', 'rules', 'traffic_source_id', 'domain_id', 'path_name',
    ];

    protected $casts = [
        'rules' => 'array',
        'domains' => 'array',
    ];

    protected static $directory = 'flo';

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function traffic_source()
    {
        return $this->belongsTo('App\TrafficSource', 'traffic_source_id');
    }

    public function domain()
    {
        return $this->belongsTo('App\Domain', 'domain_id');
    }

    public static function buildFullPathname($path_name)
    {
        return Storage::disk('flows')->path($path_name);
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
                        // TODO: Get internal URL
                        if (isset($landings[$winner]['internal_url'])) {
                            $result = $landings[$winner]['internal_url'];
                        } else {
                            $result = $landingObj->url;
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function getSafePageUrl()
    {
        $result = '';
        if (isset($this->rules['safe_page_url'])) {
            $result = $this->rules['safe_page_url'];
        }
        return $result;
    }

    public function createFileScript($oldPathName = '')
    {
        if ($oldPathName != '') {
            Storage::disk('flows')->delete($oldPathName);

            $path = pathinfo($oldPathName);
            $dir = $path['dirname'];
            Storage::disk('flows')->deleteDirectory($dir);
        }

        $src = 'redirect.php';
        if ($this->type == 'redirect') {
            $src = 'redirect.php';
        } elseif ($this->type == 'hotload') {
            $src = 'hotload.php';
        }

        $path = pathinfo($this->path_name);
        $dir = $path['dirname'];
        Storage::disk('flows')->makeDirectory($dir);

        $contents = Storage::disk('flows')->get($src);
        $contents = str_replace('{{flow_id}}', $this->id, $contents);

        $dest = $this->path_name;
        Storage::disk('flows')->put($dest, $contents);

        return true;
    }

    public function createHotloadScript()
    {
        $src = 'hotload.php';
        $contents = Storage::disk('flows')->get($src);
        $phpCode = $contents;
        $phpCode = str_replace('[[APP_URL]]', env('APP_URL'), $phpCode);
        $phpCode = str_replace('[[FLOW_ID]]', strval($this->id), $phpCode);
        $pathname = $this->domain->path . '/public/' . $this->path_name;
        $this->domain->createFile($pathname, $phpCode);
    }

    public function isFraudFilterProtectionEnabled()
    {
        $result = (
            isset($this->rules['fraud_filter_protection']) &&
            $this->rules['fraud_filter_protection'] &&
            isset($this->rules['fraud_filter_campaign_id'])
        );
        return $result;
    }

    public function fraudFilterCampaignId()
    {
        $result = '';
        if (isset($this->rules['fraud_filter_campaign_id'])) {
            $result = $this->rules['fraud_filter_campaign_id'];
        }
        return $result;
    }

    public function fraudFilterCampaignKey()
    {
        $result = '';
        if (isset($this->rules['fraud_filter_campaign_key'])) {
            $result = $this->rules['fraud_filter_campaign_key'];
        }
        return $result;
    }

    public function urlIsExternal($url)
    {
        $result = false;

        $url_domain = parse_url($url, PHP_URL_HOST);
        $tmp = explode('.', $url_domain);
        if ($tmp[0] == 'www') {
            $url_domain = $tmp[count($tmp)-2] . '.' . $tmp[count($tmp)-1];
        }

        $result = ($url_domain != $this->domain->domain);

        return $result;
    }
}
