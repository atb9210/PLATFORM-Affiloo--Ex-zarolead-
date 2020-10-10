<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;
use App\UserReport;

class UserMonthlyReport extends Model
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

    public static function getObject(User $user, int $month, int $year) : UserMonthlyReport
    {
        if ($user == null) {
            return null;
        }

        $numOfDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Format YYYY-mm-dd
        $YYYY = str_pad($year, 4, '0', STR_PAD_LEFT);
        $mm = str_pad($month, 2, '0', STR_PAD_LEFT);
        $ddFirst = '01';
        $ddLast = str_pad($numOfDays, 2, '0', STR_PAD_LEFT);

        $from = $YYYY . '-' . $mm . '-' . $ddFirst;
        $to =   $YYYY . '-' . $mm . '-' . $ddLast;

        $reports = UserReport::where('user_id', '=', $user->id)
            ->where('day', '>=', $from)
            ->where('day', '<=', $to);

        $monthlyReport = [
            'user_id' => $user->id,
            'month' => $month,
            'year' => $year,
            'visits' => 0,
            'leads' => 0,
            'commission' => 0.0,
            'total_cost' => 0.0,
            'profit' => 0.0,
        ];

        foreach ($reports->get() as $report) {
            $monthlyReport['visits'] += $report->visits;
            $monthlyReport['leads'] += $report->leads;
            $monthlyReport['commission'] += $report->commission;
            $monthlyReport['total_cost'] += $report->cost + $report->extra_cost;
            $monthlyReport['profit'] += $report->profit;
        }

        $toReturn = null;
        $rows = UserMonthlyReport::where('user_id', $user->id)
            ->where('month', $month)
            ->where('year', $year);

        if ($rows->count() == 0) {
            $toReturn = UserMonthlyReport::create($monthlyReport);
        } else {
            $toReturn = $rows->first();
            $toReturn->update($monthlyReport);
        }

        return $toReturn;
    }
}
