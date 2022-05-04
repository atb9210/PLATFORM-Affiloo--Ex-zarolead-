<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class DropinLead extends Model
{
    protected $table = 'third_dropin_leads';

    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email', 'status', 'info', 'notes', 'hash',
    ];

    protected $casts = [
        'info' => 'array',
    ];

    public static $statuses = [
        'complaint',
        'refunded',
        'unprocessed',
        'shipped',
        'delivered',
        'return',
        'stock',
        'confirmed',
        'no answer',
        'cancelled',
        'fake',
        'duplicate',
        'to recall',
        'to do',
        'waiting payment',
        'abandoned',
    ];

    public static function findByHash(string $hash)
    {
        $results = DropinLead::where('hash', $hash)->get();
        if ($results->count() > 0) {
            return $results[0];
        }
        return null;
    }

    private function getChatAPIClient()
    {
        $settings = \App\Settings::get();
        $apiInstance = env('CHATAPI_INSTANCE', '');
        $apiToken = env('CHATAPI_TOKEN', '');
        if (isset($settings['chatapi']['api_instance'])) {
            $apiInstance = $settings['chatapi']['api_instance'];
        }
        if (isset($settings['chatapi']['api_token'])) {
            $apiToken = $settings['chatapi']['api_token'];
        }
        $chatapi = new \Driverfury\ChatAPI\ChatAPI($apiInstance, $apiToken);
        return $chatapi;
    }

    public function refreshWhatsappInfo()
    {
        $messages = [];
        $lastMessage = null;
        if ($this->phone !== null && $this->phone != '') {
            $chatapi = $this->getChatAPIClient();
            $messages = $chatapi->getMessages($this->phone);
        }
        $messagesCount = count($messages);
        if ($messagesCount > 0) {
            $lastMessage = $messages[$messagesCount - 1];
        }
        if ($lastMessage !== null) {
            $info = $this->info;
            if ($info === null) {
                $info = [];
            }
            if (!isset($info['whatsapp'])) {
                $info['whatsapp'] = [
                    'last_message' => null,
                    'last_message_read' => null,
                ];
            }
            $info['whatsapp']['last_message'] = $lastMessage['id'];
            $this->info = $info;
            $this->save();
        }
    }

    public function sendMessage(string $message): array|null
    {
        $object = null;
        if ($this->phone !== null && $this->phone != '' && $message != '') {
            $chatapi = $this->getChatAPIClient();
            $object = $chatapi->sendMessage($this->phone, $message);
        }
        return $object;
    }

    public function getMessages(): array
    {
        $objects = [];
        $chatapi = $this->getChatAPIClient();
        if ($this->phone !== null && $this->phone != '') {
            $chatapi = $this->getChatAPIClient();
            $tmp = $chatapi->getMessages($this->phone);
            if ($tmp !== null) {
                $objects = $tmp;
            }
        }
        return $objects;
    }

    public function getOrderInfoForCSV($dropinProducts = null): array
    {
        $order = [];

        if ($dropinProducts === null) {
            $dropinProducts = DropinProduct::all();
        }

        $lead = $this;

        $info = $lead->info;
        if ($info === null) {
            $info = [];
        }

        $sku = '';
        if (isset($info['sku']) && $info['sku'] !== null && $info['sku'] != '') {
            $sku = $info['sku'];
        }

        $quantity = 0;
        if (isset($info['quantity']) && $info['quantity'] !== null && $info['quantity'] != '') {
            $quantity = intval($info['quantity']);
        }

        $email = '';
        if (isset($info['shipping']['email']) && $info['shipping']['email'] !== null && $info['shipping']['email'] != '') {
            $email = $info['shipping']['email'];
        } else if ($lead->email !== null && $lead->email != '') {
            $email = $lead->email;
        }
        $phone = '';
        if (isset($info['shipping']['phone']) && $info['shipping']['phone'] !== null && $info['shipping']['phone'] != '') {
            $phone = $info['shipping']['phone'];
        } else if ($lead->phone !== null && $lead->phone != '') {
            $phone = $lead->phone;
        }

        $products = [];
        if (isset($info['products']) && !empty($info['products'])) {
            $products = $info['products'];
        } else {
            // DEBUG TODO: Something wrong with this code
            // the products array results empty
            $price = 0.0;
            if ($sku !== null && $sku != '') {
                $p = null;
                foreach ($dropinProducts as $dropinProduct) {
                    if ($sku == $dropinProduct->sku) {
                        $p = $dropinProduct;
                        break;
                    }
                }

                if ($p) {
                    $price = floatval($p->sale_price);
                }
            }
            $products = [
                [
                    'sku' => $sku,
                    'quantity' => $quantity,
                    'price' => $price,
                ],
            ];
        }

        $total = 0.0;
        if (isset($info['total'])) {
            $total = $info['total'];
        } else {
            foreach ($products as $p) {
                if (isset($p['price'])) {
                    $total += floatval($p['price']);
                }
            }
        }
        $total = strval($total);

        $gateway = 'cashondelivery';
        if (isset($info['gateway'])) {
            $street_address = $info['gateway'];
        }

        $street_address = '';
        if (isset($info['shipping']['street_address'])) {
            $street_address = $info['shipping']['street_address'];
        }
        $city = '';
        if (isset($info['shipping']['city'])) {
            $city = $info['shipping']['city'];
        }
        $province = '';
        if (isset($info['shipping']['province'])) {
            $province = $info['shipping']['province'];
        }
        $postcode = '';
        if (isset($info['shipping']['postcode'])) {
            $postcode = $info['shipping']['postcode'];
        }
        $country = '';
        if (isset($info['shipping']['country'])) {
            $country = $info['shipping']['country'];
        }
        $notes = '';
        if (isset($info['shipping']['notes'])) {
            $notes = $info['shipping']['notes'];
        }

        $order = [
            'id' => $lead->id,
            'date' => $lead->created_at,
            'gateway' => $gateway,
            'total' => $total,
            'products' => $products,
            'phone' => $phone,
            'email' => $email,
            'full_name' => $lead->first_name . ' ' . $lead->last_name,
            'street_address' => $street_address,
            'city' => $city,
            'province' => $province,
            'postcode' => $postcode,
            'country' => $country,
            'notes' => $notes,
        ];

        return $order;
    }
}
