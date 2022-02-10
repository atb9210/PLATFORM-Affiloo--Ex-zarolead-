<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
// DEBUG
use Illuminate\Support\Facades\Log;

use App\third\FacebookMonitorReport;

class FacebookMonitor extends Model
{
    protected $table = 'third_facebook_monitors';

    protected $fillable = [
        'name', 'advertiser_id', 'form_id', 'campaigns',
    ];

    protected $casts = [
        'campaigns' => 'array',
    ];

    public function advertiser()
    {
        return $this->belongsTo('App\third\FacebookAdvertiser', 'advertiser_id');
    }

    public function form()
    {
        return $this->belongsTo('App\Form', 'form_id');
    }

    public function plusOneDay($day)
    {
        return date('Y-m-d', strtotime('+1 day', strtotime($day)));
    }

    public function minusOneDay($day)
    {
        return date('Y-m-d', strtotime('-1 day', strtotime($day)));
    }

    private function logError($err)
    {
        // DEBUG TODO: to delete
        //dd('ERR: ' . $err);

        Log::channel('facebook.monitor')->info('[!MON:' . strval($this->id) . '] ' . $err);
    }

    private function logMessage($msg)
    {
        // DEBUG TODO: to delete
        //dd('i: ' . $msg);

        Log::channel('facebook.monitor')->info('[iMON:' . strval($this->id) . '] ' . $msg);
    }

    public function getReports($from_date = '', $to_date = '')
    {
        $stats = DB::table('third_facebook_monitor_reports')->where('monitor_id', $this->id);
        if ($from_date != '') {
            $stats = $stats->where('day', '>=', $from_date);
        }
        if ($to_date != '') {
            $stats = $stats->where('day', '<=', $to_date);
        }

        $views = 0;
        $spent = 0.0;
        $conversions = 0;
        $conversions_value = 0;
        if($stats->get()->count() > 0) {
            $views = intval($stats->sum('views'));
            $spent = floatval($stats->sum('spent'));
            $conversions = intval($stats->sum('conversions'));
            $conversions_value = floatval($stats->sum('conversions_value'));
        }
        return [
            'views' => $views,
            'conversions' => $conversions,
            'spent' => $spent,
            'conversions_value' => $conversions_value,
        ];
    }

    public function createReport($day = null): bool
    {
        if ($day === null) {
            $day = $this->minusOneDay(date('Y-m-d'));
        }

        // Create a facebook client instance
        $accessToken = $this->advertiser->access_token;
        $fb = new \Driverfury\Facebook\Facebook($accessToken);

        // Check if access token is valid
        $me = $fb->getMe();
        if ($me === null || empty($me)) {
            // DEBUG
            $this->logError('Invalid access token');
            return false;
        }

        // Fetch stats
        $views = 0;
        $spent = 0.0;
        $conversions = 0;
        $conversions_value = 0.0;

        $adAccountId = null;
        $campaignIds = null;
        foreach ($this->campaigns as $adAccId => $campIds) {
            $adAccountId = $adAccId;
            $campaignIds = $campIds;
            // TODO: Only for one ad account for now
            break;
        }
        if ($adAccountId === null || $campaignIds === null) {
            // DEBUG
            $this->logError('Invalid ad account and/or campaigns');
            return false;
        }

        if (empty($campaignIds)) {
            // DEBUG
            $this->logError('Invalid campaigns');
            return false;
        }

        $reportsFilters = [
            'start_date' => $day,
            'end_date' => $this->plusOneDay($day),
            'metrics' => [
                'spend',
            ],
        ];
        $campaigns = $fb->getCampaigns($adAccountId, $reportsFilters);

        foreach ($campaigns as $campaign) {
            if (!in_array($campaign['id'], $campaignIds)) {
                continue;
            }
            if (isset($campaign['spend'])) {
                $spent += floatval($campaign['spend']);
            }
        }

        if ($this->form === null) {
            // DEBUG
            $this->logError('Invalid form');
            return false;
        }
        $formStats = $this->form->getStats($day, $day);
        $views = intval($formStats['visits']);
        $conversions = intval($formStats['leads']);
        $conversions_value = intval($formStats['total_commission']);

        // Update stats
        $stats = [
            'views' => $views,
            'spent' => $spent,
            'conversions' => $conversions,
            'conversions_value' => $conversions_value,
        ];
        $report = FacebookMonitorReport::getObject($this->id, $day);
        $report->update($stats);

        // DEBUG
        $this->logMessage('Report created');

        return true;
    }
}
