<?php

namespace App;

use App\Landing;
use Illuminate\Database\Eloquent\Model;

class LandingStats extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'landing_id', 'day', 'visits', 'leads', 'total_commission', 'total_cost',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
    
    protected $casts = [
        'info' => 'array'
    ];

    public function landing()
    {
        return $this->belongsTo('App\Landing', 'landing_id');
    }

    public static function getObject($landing_id, $date)
    {
    	$stats = LandingStats::where('landing_id', '=', $landing_id)
    		->where('day', '=', $date);

    	if ($stats->count() == 0) {
    		$stats = LandingStats::create([
    			'landing_id' => $landing_id,
    			'day' => $date
    		]);
    	} else {
    		$stats = $stats->first();
    	}

    	return $stats;
    }
}
