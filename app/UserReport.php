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

    // TODO: Transform int $user_id into User $user?? (Check AdvertiserReport)
    public static function getObject(int $user_id, string $day) : UserReport
    {
    	$report = UserReport::where('user_id', '=', $user_id)
    		->where('day', '=', $day);

    	if ($report->count() == 0) {
    		$report = UserReport::create([
    			'user_id' => $user_id,
    			'day' => $day
    		]);
    	} else {
    		$report = $report->first();
    	}

    	return $report;
    }
}
