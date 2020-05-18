<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'user_id',
        'order_status_id',
        'name', 'phone', 'address',
        'product_id', 'quantity',
        'commission', 'sub_commission',
        'network_id',
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

    public function sendToNetwork($network_data, $network_params) {
        $server_output = [];

        $api_call_url = $network_params['api_call_url'];
        $api_call_method = $network_params['api_call_method'];
        if ($api_call_url != '') {
            $ch = curl_init();

            $requestMethod = "post";
            if (isset($api_call_method) && $api_call_method != "") {
                $requestMethod = strtolower($api_call_method);
            }

            if ($this->network->name == "Affiliation Park") {
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
