<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Network;

class Order extends Model {
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'user_id',
        'order_status_id',
        'name', 'phone', 'address', 'email',
        'product_id', 'quantity',
        'commission', 'sub_commission',
        'network_id', 'network_data',
    ];

    protected $casts = [
        'network_data' => 'array',
    ];
    
    public function status() {
        return $this->belongsTo('App\OrderStatus', 'order_status_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function network() {
        return $this->belongsTo('App\Network', 'network_id');
    }

    public function sendToNetwork(array $input, array $form, Network $network) {
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
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postbackBodyParams));
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
        $this->order_status_id = OrderStatus::NOT_SENT;
        if ($status == 'success') {
            $this->order_status_id = OrderStatus::WAITING;
        } elseif ($status == 'double') {
            $this->order_status_id = OrderStatus::DOUBLE;
        } elseif ($status == 'error') {
            $this->order_status_id = OrderStatus::NOT_SENT;
        }
        $this->save();

        return [
            'status' => $status,
            'message' => $message,
            'postback' => $postbackFlag,
            'redirect' => $redirectFlag,
            'redirect_url' => $redirectUrl,
            'response' => $response,
        ];
    }

    public static function doubleRecovery(array $input, array $form, Network $network) {
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

    public static function interpretParams(array $params, array $context) {
        $interpretedParams = [];
        foreach ($params as $key => $value) {
            $interpretedParams[$key] = self::interpretParam($value, $context);
        }
        return $interpretedParams;
    }

    public static function interpretParam(string $param, array $context) {
        $interpreted = '';

        $matches = [];
        preg_match_all("/{(.*?)}/", $param, $matches);

        $lookupTable = [];
        for ($i = 0; $i < count($matches[1]); $i++) {
            $query = $matches[1][$i];

            $obj = explode('.', $query)[0];
            $field = explode('.', $query)[1];

            $value = '';
            if (isset($context[$obj][$field])) {
                $value = $context[$obj][$field];
            }
            $lookupTable[$matches[0][$i]] = $value;
        }

        $interpreted = $param;
        foreach ($lookupTable as $key => $value) {
            $interpreted = str_replace($key, $value, $interpreted);
        }
        return $interpreted;
    }

    public function sendToNetworkOld($network_data, $network_params) {
        $server_output = [];

        $api_call_url = $network_params['api_call_url'];
        $api_call_method = '';
        if (isset($network_params['api_call_method'])) {
            $api_call_method = $network_params['api_call_method'];
        }
        if ($api_call_url != '') {
            $ch = curl_init();

            $requestMethod = "post";
            if ($api_call_method != "") {
                $requestMethod = strtolower($api_call_method);
            }

            if ($network->name == "Worldfilia") {
                $api_call_url .= '?' . http_build_query(['api_key' => $network_params['api_key']]);
            } elseif ($network->name == "Affiliation Park") {
                $network_data["user"] = "asrafatb9210@gmail.com";
                $network_data["password"] = "1vrdNLMOjwQJIbB8Eqt0AcUgyDmp2hsC";
            }

            $getUrl = "";
            if ($requestMethod == "post") {
                curl_setopt($ch, CURLOPT_URL, $api_call_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($network_data));
            } else {
                $getUrl = $api_call_url . "?" . http_build_query($network_data);
                curl_setopt($ch, CURLOPT_URL, $getUrl);
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $server_output = curl_exec($ch);
            curl_close($ch);

            $server_output = json_decode($server_output, true);
        }

        // POSTBACK
        if ($this->network == "Amsped" || $this->network == "Affiliation Park") {
            $postback_ch = curl_init();
            $postback_data = array(
                'cid' => $request->input('sub_id'),
                'payout' => $this->commission,
            );

            $getUrl = "http://ztrk37.com/postback?" . http_build_query($postback_data);

            curl_setopt($postback_ch, CURLOPT_URL, $getUrl);
            curl_setopt($postback_ch, CURLOPT_RETURNTRANSFER, 1);

            $postback_output = curl_exec($postback_ch);

            curl_close($postback_ch);
        }

        $api_success_response = [];
        if (isset($form->network->api['success_response'])) {
            $api_success_response = $form->network->api['success_response'];
        }
        $error = false;
        foreach ($api_success_response as $key => $value) {
            if (isset($server_output[$key]) && $server_output[$key] == $value) {
                continue;
            } else {
                $error = true;
                break;
            }
        }
        if (!$error) {
            // TODO: To remove
            //$this->orderSentToNetwork($this->id);

            $this->order_status_id = OrderStatus::WAITING;
        } else {
            // TODO: Generalize the algorithm (this only works for worldfilia)
            if (isset($server_output['check_url'])) {
                $toReturn['redirect'] = $server_output['check_url'];

                // TODO: to remove
                // The order is double type
                //$info['double_type'] = true;

                $this->order_status_id = OrderStatus::DOUBLE;

                $this->info = json_encode($info);
                $this->save();
            }
        }
        return [
            'server_input' => $network_data,
            'server_output' => $server_output,
        ];
    }
}
