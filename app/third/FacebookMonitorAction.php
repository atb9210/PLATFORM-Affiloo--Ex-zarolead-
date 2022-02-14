<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class FacebookMonitorAction extends Model
{
    protected $table = 'third_facebook_monitor_actions';

    protected $fillable = [
        'monitor_id', 'campaign_id', 'action_type', 'spent', 'conversions', 'conversions_value', 'end_profit',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'spent' => 'float',
        'conversions' => 'integer',
        'conversions_value' => 'float',
        'end_profit' => 'float',
    ];

    public function monitor()
    {
        return $this->belongsTo('App\third\FacebookMonitor', 'monitor_id');
    }
}
