<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class FacebookMonitorReport extends Model
{
    protected $table = 'third_facebook_monitor_reports';
    public $timestamps = false;

    protected $fillable = [
        'monitor_id', 'day', 'views', 'spent', 'conversions', 'conversions_value',
    ];

    protected $casts = [
        'day' => 'date',
        'views' => 'integer',
        'spent' => 'float',
        'conversions' => 'integer',
        'conversions_value' => 'float',
    ];

    public function monitor()
    {
        return $this->belongsTo('App\third\FacebookMonitor', 'monitor_id');
    }

    public static function getObject($monitor_id, $day): FacebookMonitorReport
    {
        $report = FacebookMonitorReport::where('monitor_id', $monitor_id)
            ->where('day', $day)->first();

        if ($report === null) {
            $report = FacebookMonitorReport::create([
                'monitor_id' => $monitor_id,
                'day' => $day,
            ]);
        }

        return $report;
    }
}
