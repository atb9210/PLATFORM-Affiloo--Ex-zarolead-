<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'day',
        'visits', 'leads', 'commission', 'cost', 'extra_cost', 'profit',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public static function getObject($user_id, $date)
    {
    	$report = UserReport::where('user_id', '=', $user_id)
    		->where('day', '=', $date);

    	if ($report->count() == 0) {
    		$report = UserReport::create([
    			'user_id' => $user_id,
    			'day' => $date
    		]);
    	} else {
    		$report = $report->first();
    	}

    	return $report;
    }
}
