<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\OrderStatus;
use App\Network;

use App\User;
use App\FormStats;
use App\LandingStats;
use App\UserReport;
use App\AdvertiserReport;

class Order extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'user_id',
        'order_status_id',
        'name', 'phone', 'address', 'email', 'info',
        'product_id', 'quantity',
        'price', 'commission', 'sub_commission',
        'network_id', 'network_data',
    ];

    protected $casts = [
        'info' => 'array',
        'network_data' => 'array',
    ];
    
    public function status()
    {
        return $this->belongsTo('App\OrderStatus', 'order_status_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function network()
    {
        return $this->belongsTo('App\Network', 'network_id');
    }

    public function sendToNetwork(array $input, array $form, Network $network)
    {
        // Variables to return
        $status = 'error';
        $postbackFlag = false;
        $redirectFlag = false;
        $redirectUrl = '';
        $message = '';

        // Define context
        $context = [
            'input' => $input,
            'form' => $form,
            'order' => $this->toArray(),
        ];

        // Params validation
        // @TODO:
        $urlParamsValidation =  $network->getApi('store.redirect.validation.urlParams');
        $bodyParamsValidation = $network->getApi('store.redirect.validation.bodyParams');

        // Form params validation
        // @TODO:

        // Make API call
        $apiRequestUrl = self::interpretParam($network->getApi('store.request.url'), $context);
        $apiRequestMethod = self::interpretParam($network->getApi('store.request.method'), $context);
        $apiRequestType = self::interpretParam($network->getApi('store.request.type'), $context);

        $headers = self::interpretParams($network->getApi('store.request.headers'), $context);
        $urlParams = self::interpretParams($network->getApi('store.request.urlParams'), $context);
        $bodyParams = self::interpretParams($network->getApi('store.request.bodyParams'), $context);

        $url = $apiRequestUrl;
        $token = (strpos($apiRequestUrl, '?') === false) ? '?' : '&';
        $url .= $token . http_build_query($urlParams);

        $response = [];
        $callUrl = $url;
        $callParams = [];
        if ($apiRequestUrl != '' && $apiRequestMethod != '') {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);

            if (!empty($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }

            if (strtolower($apiRequestMethod) == "post") {
                $bParams = http_build_query($bodyParams);
                $callParams = $bParams;
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $bParams);
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $server_output = curl_exec($ch);
            curl_close($ch);

            if($server_output == null)
            {
                Log::channel('orders')->info('(Order ID: ' . $this->id . ') Network ' . $network->name . ' sent us a void response', $context);
            }

            if (strtolower(self::interpretParam($network->getApi('store.response.type'), $context)) == "json") {
                $response = json_decode($server_output, true);
            } else {
                $response = $server_output;
            }
        }

        if ($response == null) {
            $response = [];
        }

        // Add response to the context
        $context['response'] = $response;

        // Get status
        $responseStatuses = $network->getApi('store.response.status');
        $status = (isset($responseStatuses['default'])) ? $responseStatuses['default'] : 'error';
        foreach ($responseStatuses as $key => $value) {
            if ($key == 'default') continue;

            $validator = Validator::make($response, $responseStatuses[$key]['validation']);
            if (isset($value['validation']) &&
                $validator->passes()) {
                $status = $key;
            }
        }

        // Postback
        $postbackFlag = false;
        $postback_response = "";
        $postbackArray = $network->getApi('store.postback');
        if (!empty($postbackArray)) {
            $postbackIndex = -1;
            for ($i = 0; $i < count($postbackArray); $i++) {
                $on = explode('|', self::interpretParam($postbackArray[$i]['on'], $context));
                if (in_array($status, $on)) {
                    $postbackIndex = $i;
                    break;
                }
            }

            if ($postbackIndex >= 0) {
                $postbackFlag = true;

                $postback = $postbackArray[$postbackIndex];

                $postbackUrl = self::interpretParam($postback['url'], $context);
                $postbackMethod = self::interpretParam($postback['method'], $context);
                $postbackType = '';
                if (isset($postback['type'])) {
                    $postbackType = self::interpretParam($postback['type'], $context);
                }

                $postbackUrlParams = [];
                $postbackBodyParams = [];
                if (isset($postback['urlParams'])) {
                    $postbackUrlParams = self::interpretParams($postback['urlParams'], $context);
                }
                if (isset($postback['bodyParams'])) {
                    $postbackBodyParams = self::interpretParams($postback['bodyParams'], $context);
                }

                $token = (strpos($postbackUrl, '?') === false) ? '?' : '&';
                $postbackUrl .= $token . http_build_query($postbackUrlParams);

                $postback_ch = curl_init();

                curl_setopt($postback_ch, CURLOPT_URL, $postbackUrl);
                if (strtolower($postbackMethod) == "post") {
                    curl_setopt($postback_ch, CURLOPT_POST, 1);
                    curl_setopt($postback_ch, CURLOPT_POSTFIELDS, http_build_query($postbackBodyParams));
                }
                curl_setopt($postback_ch, CURLOPT_RETURNTRANSFER, 1);

                $postback_output = curl_exec($postback_ch);
                curl_close($postback_ch);

                if (strtolower($postbackType) == "json") {
                    $postback_response = json_decode($postback_output, true);
                } else {
                    $postback_response = $postback_output;
                }
            }
        }

        // Redirect
        $redirectArray = $network->getApi('store.redirect');
        $redirectFlag = false;
        $redirectUrl = '';
        if (!empty($redirectArray)) {
            $redirectIndex = -1;
            for ($i = 0; $i < count($redirectArray); $i++) {
                $on = explode('|', self::interpretParam($redirectArray[$i]['on'], $context));
                if (in_array($status, $on)) {
                    $redirectIndex = $i;
                    break;
                }
            }

            if ($redirectIndex >= 0) {
                $redirectFlag = true;
                $redirect = $redirectArray[$postbackIndex];
                $redirectUrl = self::interpretParam($redirect['url'], $context);
            }
        }

        // Change order status
        $order_status_id = OrderStatus::WAITING;
        if (!empty($network->getApi('default_status'))) {
            $order_status = $network->getApi('default_status');
            if ($order_status != '') {
                $os = OrderStatus::where('description', $order_status);
                if ($os->count() > 0) {
                    $order_status_id = $os->first()->id;
                }
            }
        }
        $redirectArray = $network->getApi('store.redirect');
        $this->order_status_id = OrderStatus::NOT_SENT;
        if ($status == 'success') {
            $this->order_status_id = $order_status_id;
        } elseif ($status == 'double') {
            $this->order_status_id = OrderStatus::DOUBLE;
        } elseif ($status == 'error') {
            $this->order_status_id = OrderStatus::NOT_SENT;
            Log::channel('orders')->info('Order ' . $this->id . ' has not been stored by the network ' . $network->name, $context);
        } else {
            $this->order_status_id = OrderStatus::WAITING;
        }
        $this->save();

        // Send order to telegram
        $telegram = new \Longman\TelegramBot\Telegram(env('TELEGRAM_BOT_API_KEY'), env('TELEGRAM_BOT_USERNAME'));
        \Longman\TelegramBot\Request::initialize($telegram);
        $chatId = -1001319465755; // TODO: Save this into .env?
        $telegramMessage = <<<EOD
New Order: {{product}}
{{name}}
EOD;
        $telegramMessage = str_replace('{{product}}', $this->product->name, $telegramMessage);
        $telegramMessage = str_replace('{{name}}', $this->name, $telegramMessage);
        \Longman\TelegramBot\Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $telegramMessage,
        ]);

        return [
            'status' => $status,
            'message' => $message,
            'postback' => $postbackFlag,
            'postback_response' => $postback_response,
            'redirect' => $redirectFlag,
            'redirect_url' => $redirectUrl,
            'call_url' => $callUrl,
            'call_params' => $callParams,
            'response' => $response,
        ];
    }

    public static function doubleRecovery(array $input, array $form, Network $network)
    {
        // Define context
        $context = [
            'input' => $input,
            'form' => $form,
        ];

        // Params validation
        // @TODO:
        $urlParamsValidation =  $network->getApi('store.redirect.validation.urlParams');
        $bodyParamsValidation = $network->getApi('store.redirect.validation.bodyParams');

        // Form params validation
        // @TODO:

        // Make API call
        $apiRequestUrl = self::interpretParam($network->getApi('store.request.url'), $context);
        $apiRequestMethod = self::interpretParam($network->getApi('store.request.method'), $context);
        $apiRequestType = self::interpretParam($network->getApi('store.request.type'), $context);

        $urlParams = self::interpretParams($network->getApi('store.request.urlParams'), $context);
        $bodyParams = self::interpretParams($network->getApi('store.request.bodyParams'), $context);

        $url = $apiRequestUrl;
        $token = (strpos($apiRequestUrl, '?') === false) ? '?' : '&';
        $url .= $token . http_build_query($urlParams);

        $response = [];
        if ($apiRequestUrl != '' && $apiRequestMethod != '') {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            if (strtolower($apiRequestMethod) == "post") {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($bodyParams));
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $server_output = curl_exec($ch);
            curl_close($ch);

            if (strtolower(self::interpretParam($network->getApi('store.response.type'), $context)) == "json") {
                $response = json_decode($server_output, true);
            } else {
                $response = $server_output;
            }
        }

        return $response;
    }

    public static function interpretParams(array $params, array $context)
    {
        $interpretedParams = [];
        foreach ($params as $key => $value) {
            $interpretedParams[$key] = self::interpretParam($value, $context);
        }
        return $interpretedParams;
    }

    public static function interpretParam(string $param, array $context)
    {
        $interpreted = '';

        $matches = [];
        preg_match_all("/{(.*?)}/", $param, $matches);

        $lookupTable = [];
        for ($i = 0; $i < count($matches[1]); $i++) {
            $query = $matches[1][$i];

            $obj = explode('.', $query)[0];
            $fields = array_slice(explode('.', $query), 1);

            $value = $context[$obj];
            for ($fieldIndex = 0; $fieldIndex < count($fields); $fieldIndex++) {
                $field = $fields[$fieldIndex];
                if (isset($value[$field])) {
                    $value = $value[$field];
                } else {
                    $value = '';
                    break;
                }
            }
            $lookupTable[$matches[0][$i]] = $value;
        }

        $interpreted = $param;
        foreach ($lookupTable as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $interpreted = str_replace($key, $value, $interpreted);
        }
        return $interpreted;
    }

    public function updateStats(
        $commission, $sub_commission,
        $form, $query_params,
        $landing)
    {
        $timestamp = strtotime($this->created_at);
        $day = date('Y-m-d', $timestamp);

        $order = $this;

        // Increase form leads and commission
        $formStats = FormStats::getObject($form->id, $day);
        $formStats->leads += 1;
        $formStats->total_commission += $order->commission;
        $formStatsInfo = $formStats->info;
        if (!isset($formStatsInfo['total_sub_commission'])) {
            $formStatsInfo['total_sub_commission'] = 0.0;
        }
        $formStatsInfo['total_sub_commission'] += $sub_commission;
        $formStats->info = $formStatsInfo;
        if (!empty($query_params)) {
            $custom_params_stats = $formStats->custom_params_stats;
            if ($custom_params_stats === null) {
                $custom_params_stats = [];
            }
            foreach ($query_params as $k => $v) {
                if (!isset($custom_params_stats[$k])) {
                    $custom_params_stats[$k] = [];
                }
                if (!isset($custom_params_stats[$k][$v])) {
                    $custom_params_stats[$k][$v] = [
                        'visits' => 0,
                        'leads' => 0,
                        'total_commission' => 0.0,
                    ];
                }
                $custom_params_stats[$k][$v]['leads'] += 1;
                $custom_params_stats[$k][$v]['total_commission'] += $order->commission;
            }
            $formStats->custom_params_stats = $custom_params_stats;
        }
        $formStats->save();

        // Landing stats
        $stats = LandingStats::getObject($landing->id, $day);
        $stats->leads += 1;
        $stats->total_commission += $order->commission;
        $statsInfo = $stats->info;
        if (!isset($statsInfo['total_sub_commission'])) {
            $statsInfo['total_sub_commission'] = 0.0;
        }
        $statsInfo['total_sub_commission'] += $sub_commission;
        $stats->info = $statsInfo;
        $stats->save();

        // Total
        $form->leads += 1;
        $form->total_commission += $order->commission;
        $formInfo = $form->info;
        if (!isset($formInfo['total_sub_commission'])) {
            $formInfo['total_sub_commission'] = 0.0;
        }
        $formInfo['total_sub_commission'] += $sub_commission;
        $form->info = $formInfo;
        $form->save();

        // Update user's report stats
        $report = UserReport::getObject($form->user->id, $day);
        $report->leads += 1;
        $report->commission += $order->commission;
        $report->profit = $report->commission - ($report->cost + $report->extra_cost);
        $report->save();

        // Update admin's user report
        $adminRoleId = 2;
        $users = User::query();
        $users = $users->join('role_user', 'role_user.user_id', '=', 'id')
            ->whereIn('role_user.role_id', [$adminRoleId]);
        $users = $users->get();
        foreach ($users as $user) {
            // Skip if it is already updated
            if ($user->id == $form->user->id) {
                continue;
            }
            $report = UserReport::getObject($user->id, $day);
            $report->leads += 1;
            $report->commission += $order->commission;
            $report->profit = $report->commission - ($report->cost + $report->extra_cost);
            $report->save();
        }

        // Update advertiser's report stats
        $timestamp = strtotime($day);
        $month = intval(date('m', $timestamp));
        $year = intval(date('Y', $timestamp));
        $advertiser = User::findOrFail($form->product->advertiser_id);
        $advReport = AdvertiserReport::getObject($advertiser, $month, $year);
        $advReport->leads += 1;
        $advReport->commission += $order->commission;
        $advReport->profit = $advReport->commission - $advReport->cost;
        $advReport->save();
    }
}
