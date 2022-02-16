<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
// DEBUG
use Illuminate\Support\Facades\Log;

use App\third\FacebookMonitorReport;
use App\third\FacebookMonitorAction;

class FacebookMonitor extends Model
{
    protected $table = 'third_facebook_monitors';

    protected $fillable = [
        'name', 'advertiser_id', 'form_id', 'campaigns', 'strategy', 'archived',
    ];

    protected $casts = [
        'campaigns' => 'array',
        'strategy' => 'array',
    ];

    public function advertiser()
    {
        return $this->belongsTo('App\third\FacebookAdvertiser', 'advertiser_id');
    }

    public function form()
    {
        return $this->belongsTo('App\Form', 'form_id');
    }

    public function today()
    {
        return date('Y-m-d');
    }

    public function tomorrow()
    {
        return $this->plusOneDay($this->today());
    }

    public function yesterday()
    {
        return $this->minusOneDay($this->today());
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
            'spent' => $spent,
            'conversions' => $conversions,
            'conversions_value' => $conversions_value,
        ];
    }

    public function createReport($day = null): bool
    {
        if ($day === null) {
            $day = $this->minusOneDay(date('Y-m-d'));
        }

        // Check if access token is valid
        if (!$this->advertiser->checkAccessToken()) {
            // DEBUG
            $this->logError('Invalid access token');
            return false;
        }

        // Create a facebook client instance
        $fb = $this->advertiser->getClient();

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

    private function getStatsFromObject(array $obj): array
    {
        $stats = [
            'spent' => 0.0,
            'conversions' => 0,
            'conversions_value' => 0.0,
        ];

        if (isset($obj['insights']['data']) && $obj['insights']['data'] !== null &&
            count($obj['insights']['data']) > 0) {
            $insights = $obj['insights']['data'][0];

            if (isset($insights['spend']) && $insights['spend'] !== null) {
                $stats['spent'] = floatval($insights['spend']);
            }
            if (isset($insights['actions']) && $campaigns['actions'] !== null) {
                for ($i = 0; $i < count($insights['actions']); $i += 1) {
                    $a = $insights['actions'][$i];
                    if (isset($a['action_type']) && $a['action_type'] === 'omni_purchase') {
                        if (isset($a['value']) && $a['value'] !== null) {
                            $stats['conversions'] += intval($a['value']);
                        }
                    }
                }
            }
            if (isset($insights['action_values']) && $campaigns['action_values'] !== null) {
                for ($i = 0; $i < count($insights['action_values']); $i += 1) {
                    $a = $insights['action_values'][$i];
                    if (isset($a['action_type']) && $a['action_type'] === 'omni_purchase') {
                        if (isset($a['value']) && $a['value'] !== null) {
                            $stats['conversions_value'] += floatval($a['value']);
                        }
                    }
                }
            }
        }

        return $stats;
    }

    public function getActions($dateFrom = null, $dateTo = null): array
    {
        $actions = [];

        if ($dateFrom !== null && strpos($dateFrom, ' ') === false) {
            $dateFrom .= ' 00:00:00';
        }
        if ($dateTo !== null && strpos($dateTo, ' ') === false) {
            $dateTo .= ' 23:59:59';
        }

        $query = FacebookMonitorAction::where('monitor_id', $this->id);
        if ($dateFrom !== null) {
            $query = $query->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo !== null) {
            $query = $query->where('created_at', '<=', $dateTo);
        }

        $actions = $query->get();

        return $actions;
    }

    private function logAction($campaignId, $actionType, $stats)
    {
        $params = $stats;
        $params['monitor_id'] = $this->id;
        $params['campaign_id'] = $campaignId;
        $params['action_type'] = $actionType;

        $action = FacebookMonitorAction::create($params);

        return $action;
    }

    public function runStrategy($params)
    {
        $result = true;

        // Check if access token is valid
        if (!$this->advertiser->checkAccessToken()) {
            // DEBUG
            $this->logError('Invalid access token');
            return false;
        }

        // Create a facebook client instance
        $fb = $this->advertiser->getClient();

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

        $today = $this->today();
        $tomorrow = $this->tomorrow();
        $reportsFilters = [
            'start_date' => $today,
            'end_date' => $tomorrow,
            'metrics' => [
                'spend',
                'actions',
                'action_values',
            ],
        ];
        $campaigns = $fb->getCampaigns($adAccountId, $reportsFilters);
        foreach ($campaigns as $campaign) {
            $campaign['id'] = strval($campaign['id']);

            if (!in_array($campaign['id'], $campaignIds)) {
                continue;
            }

            // Fetch stats
            $stats = $this->getStatsFromObject($campaign);

            // Check if action needs to be done
            $toDo = true;
            if (isset($params['times_per_day'])) {
                $timesPerDay = intval($params['times_per_day']);
                $todayActions = FacebookMonitorAction::where('action_type', $params['action_type'])
                    ->where('created_at', '>=', $today . ' 00:00:00');
                    ->where('created_at', '<=', $today . ' 23:59:59');
                $toDo = $toDo && ($todayActions->count() < $timesPerDay);
            }
            if (isset($params['spent'])) {
                $toDo = $toDo && ($stats['spent'] >= floatval($params['spent']));
            }
            if (isset($params['conversions'])) {
                $toDo = $toDo && ($stats['conversions'] < floatval($params['conversions']));
            }
            if ($toDo) {
                if (isset($params['lifetime_spent'])) {
                    $reportsFiltersLifetime = [
                        'metrics' => [
                            'spend',
                            'actions',
                            'action_values',
                        ],
                    ];
                    $campaignLifetime = $fb->getCampaign($adAccountId, $campaign['id'], $reportsFiltersLifetime);
                    $lifetimeStats = $this->getStatsFromObject($campaignLifetime);
                    $toDo = $toDo && ($lifetimeStats['spent'] >= floatval($params['lifetime_spent']));
                }
            }

            // Do the action if needed
            if ($toDo) {
                switch ($params['action_type']) {
                    case 'bid.stop': {
                        $this->setBid($campaign, 0.01);
                    } break;

                    case 'bid.down': {
                        $variation = floatval($params['variation']);
                        $minBid = -1.0;
                        if (isset($params['min_bid'])) {
                            try {
                                $minBid = floatval($params['min_bid']);
                            } catch (Exception $e) {
                                $minBid = -1.0;
                            }
                        }
                        $this->moveBid($campaign, $variation, $minBid);
                    } break;
                }

                $this->logAction($campaign['id'], $params['action_type'], $stats);
            }
        }

        return $result;
    }

    private function setBid(array $campaign, float $bidAmount): bool
    {
        // Check if access token is valid
        if (!$this->advertiser->checkAccessToken()) {
            // DEBUG
            $this->logError('Invalid access token');
            return false;
        }

        // Create a facebook client instance
        $fb = $this->advertiser->getClient();

        return $fb->updateCampaignBidAmount($campaign['id'], $bidAmount);
    }

    private function moveBid(array $campaign, float $variation, float $minBid = -1.0): bool
    {
        // Check if access token is valid
        if (!$this->advertiser->checkAccessToken()) {
            // DEBUG
            $this->logError('Invalid access token');
            return false;
        }

        // Create a facebook client instance
        $fb = $this->advertiser->getClient();

        // TODO: NOTE: This works only with 1 adset per campaign
        $adsets = $fb->getAdsets($campaign['id']);
        if ($adsets === null || count($adsets) == 0) {
            return false;
        }
        if (!isset($adsets[0]['bid_amount'])) {
            return false;
        }
        $bidAmount = floatval($adsets[0]['bid_amount'])/100;
        $changeBid = true;
        if ($minBid >= 0.0) {
            if ($bidAmount > $minBid) {
                if ($bidAmount + $variation < $minBid) {
                    $bidAmount = $minBid;
                } else {
                    $bidAmount = $bidAmount + $variation;
                }
            } else {
                $changeBid = false;
            }
        } else {
            $bidAmount = $bidAmount + $variation;
        }

        $result = true;
        if ($changeBid) {
            $result = $fb->updateCampaignBidAmount($campaign['id'], $bidAmount);
        }

        return $result;
    }

    public function completeStrategy(): bool
    {
        $result = false;

        $actions = $this->getActions($yesterday, $yesterday);
        if (count($actions) == 0) {
            return true;
        }

        // Check if access token is valid
        if (!$this->advertiser->checkAccessToken()) {
            // DEBUG
            $this->logError('Invalid access token');
            return false;
        }

        // Create a facebook client instance
        $fb = $this->advertiser->getClient();

        $yesterday = $this->yesterday();
        $today = $this->today();

        $reports = $this->getReports($yesterday, $yesterday);

        $spent = $reports['spent'];
        $conversionsValue = $reports['conversions_value'];
        $profit = $conversionsValue - $spent;
        $roiTotal = (($spent > 0) ? ((($profit/$spent))*100) : 0);

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

        $campaignStats = [];

        $reportsFilters = [
            'start_date' => $yesterday,
            'end_date' => $today,
            'metrics' => [
                'spend',
                'actions',
                'action_values',
            ],
        ];
        $campaigns = $fb->getCampaigns($adAccountId, $reportsFilters);
        foreach ($campaigns as $campaign) {
            // TODO: HERE
            if (!in_array($campaign['id'], $campaignIds)) {
                continue;
            }

            $stats = $this->getStatsFromObject($campaign);

            $spent = $stats['spent'];
            $conversionsValue = $stats['conversions_value'];
            $profit = $conversionsValue - $spent;
            $roi = (($spent > 0) ? ((($profit/$spent))*100) : 0);

            $campaignStats[$campaign['id']] = $stats;
            $campaignStats[$campaign['id']]['profit'] = $profit;
            $campaignStats[$campaign['id']]['roi'] = $roi;
        }

        foreach ($actions as $action) {
            $params = [
                'end_roi' => $roiTotal,
            ];
            if (isset($campaignStats[$action->campaign_id])) {
                $params['end_roi_campaign'] = $campaignStats[$action->campaign_id]['roi'];
            }
            $action->update($params);
        }

        $result = true;

        return $result;
    }
}
