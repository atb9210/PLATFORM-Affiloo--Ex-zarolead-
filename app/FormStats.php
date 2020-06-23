<?php

namespace App;

use App\Form;
use Illuminate\Database\Eloquent\Model;

class FormStats extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'form_id', 'day', 'visits', 'leads', 'total_commission', 'total_cost',
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

    public function form() {
        return $this->belongsTo('App\Form', 'form_id');
    }

    public static function getObject($form_id, $date) {
    	$stats = FormStats::where('form_id', '=', $form_id)
    		->where('day', '=', $date);

    	if ($stats->count() == 0) {
    		$stats = FormStats::create([
    			'form_id' => $form_id,
    			'day' => $date
    		]);
    	} else {
    		$stats = $stats->first();
    	}

    	return $stats;
    }

}
