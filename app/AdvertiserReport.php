<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvertiserReport extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'month', 'year',
        'visits', 'leads', 'commission', 'approved_commission', 'total_cost', 'profit',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public static function getObject(User $user, int $month, int $year) : AdvertiserReport
    {
        if ($user == null) {
            return null;
        }

        $report = null;
        $reports = AdvertiserReport::where('user_id', $user->id)
            ->where('month', $month)->where('year', $year);
        if ($reports->count() == 0) {
            $toCreate = [
                'user_id' => $user->id,
                'month' => $month,
                'year' => $year,
                'visits' => 0,
                'leads' => 0,
                'commission' => 0.0,
                'approved_commission' => 0.0,
                'total_cost' => 0.0,
                'profit' => 0.0,
            ];
            $report = AdvertiserReport::create($toCreate);
        } else {
            $report = $reports->first();
        }
        return $report;
    }
}
