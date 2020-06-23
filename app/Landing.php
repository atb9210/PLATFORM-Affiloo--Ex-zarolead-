<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Landing extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'campaign_id', 'form_id', 'tag_id', 'url', 'country'
    ];

    public function campaign() {
        return $this->belongsTo('App\Campaign', 'campaign_id');
    }

	public function form() {
		return $this->belongsTo('App\Form', 'form_id');
	}

    public function tag() {
		return $this->belongsTo('App\Tag', 'tag_id');
	}

    public function stats() {
        return $this->hasMany('App\LandingStats');
    }

    /*
     * Dates in format YYYY-mm-dd
     */
    public function getStats($from_date = '', $to_date = '')
    {
        // TODO: Check dates format

        $stats = DB::table('landing_stats')->where('landing_id', $this->id);
        if ($from_date != '') {
            $stats = $stats->where('day', '>=', $from_date);
        }
        if ($to_date != '') {
            $stats = $stats->where('day', '<=', $to_date);
        }

        $visits = 0;
        $leads = 0;
        $total_cost = 0;
        $total_commission = 0;
        if($stats->get()->count() > 0) {
            $visits = $stats->sum('visits');
            $leads = $stats->sum('leads');
            $total_cost = $stats->sum('total_cost');
            $total_commission = $stats->sum('total_commission');
        }
        return [
            'visits' => $visits,
            'leads' => $leads,
            'total_cost' => $total_cost,
            'total_commission' => $total_commission,
        ];
    }
}
