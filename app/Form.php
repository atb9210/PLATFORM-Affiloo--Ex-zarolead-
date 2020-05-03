<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'network', 'product_id', 'quantity', 'price',
        'commission', 'sub_commission', 'tag_id', 'traffic_source_id',
        'info'
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

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function tag()
    {
        return $this->belongsTo('App\Tag', 'tag_id');
    }

    public function traffic_source()
    {
        return $this->belongsTo('App\TrafficSource', 'traffic_source_id');
    }

    public function stats()
    {
        return $this->hasMany('App\FormStats');
    }

    public function getTotalStats($fromDate = '', $toDate = '', $admin = false)
    {
        $stats = array(
            'visits' => 0,
            'leads' => 0,
            'total_commission' => 0.0,
            'total_sub_commission' => 0.0,
            'total_cost' => 0.0,
        );

        $admin = $admin && Auth::user()->hasPermission('admin');

        $formStats = FormStats::where('form_id', '=', $this->id);
        $date_filter = true;
        if ($fromDate == '' && $toDate == '') {
            $form = Form::findOrFail($this->id);
            $stats['visits'] = $form->visits;
            $stats['leads'] = $form->leads;
            $stats['total_commission'] = $form->total_commission;
            if (isset($form->info['total_sub_commission'])) {
                $stats['total_sub_commission'] = $form->info['total_sub_commission'];
            }
            $stats['total_cost'] = $form->total_cost;

            $date_filter = false;
        } else if ($fromDate == '') {
            $formStats = $formStats->where('day', '<=', $toDate);
        } else if ($toDate == '') {
            $formStats = $formStats->where('day', '>=', $fromDate);
        } else {
            $formStats = $formStats->whereBetween('day', [$fromDate, $toDate]);
        }

        if ($date_filter) {
            foreach ($formStats->get() as $dailyStats) {
                $stats['visits'] += $dailyStats->visits;
                $stats['leads'] += $dailyStats->leads;
                $stats['total_commission'] += $dailyStats->total_commission;
                if (isset($dailyStats->info['total_sub_commission'])) {
                    $stats['total_sub_commission'] += $dailyStats->info['total_sub_commission'];
                }

                $stats['total_cost'] += $dailyStats->total_cost;
            }
        }

        return $stats;
    }
}
