<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class DropinShipping extends Model
{
    protected $table = 'third_dropin_shippings';

    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name', 'phone', 'email', 'address', 'city', 'postcode', 'notes',
        'amount', 'delivery_price', 'tax',
        'payment_method', 'status', 'tracking_url', 'info',
        'issued_at', 'created_at', 'updated_at',
    ];

    protected $casts = [
        'info' => 'array',
        'amount' => 'float',
        'delivery_price' => 'float',
        'tax' => 'float',
    ];

    private function getInnerHTML(string $html, string $tag): string
    {
        $innerHTML = '';

        $needleStart = '<'  . $tag . '>';
        $needleEnd   = '</' . $tag . '>';

        $needleStartPos = strpos($html, $needleStart);
        if ($needleStartPos !== false) {
            $html = substr($html, $needleStartPos + strlen($needleStart));
        }

        $needleEndPos = strpos($html, $needleEnd);
        if ($needleEndPos !== false) {
            $html = substr($html, 0, $needleEndPos);
        }

        $innerHTML = $html;

        return $innerHTML;
    }

    public function refreshShippingStatus()
    {
        $trackingUrl = $this->tracking_url;
        if ($trackingUrl !== null && $trackingUrl != '') {
            $requests = new \Driverfury\Requests\Requests();
            $response = $requests->get($trackingUrl);
            $html = '';
            if ($response['code'] == 200) {
                $html = $response['data'];
                $html = str_replace(["\t", "\n"], "", $html);
            }

            // Get packeta status
            $packetaStatus = '';
            if ($html != '') {
                $needle = '<h3><strong>Packet status:</strong>';
                $needlePos = strpos($html, $needle);
                if ($needlePos !== false) {
                    $tmp = substr($html, $needlePos + strlen($needle));
                    $i = 0;
                    while (ctype_space($tmp[$i])) {
                        $i += 1;
                    }
                    $tmp = substr($tmp, $i);
                    $i = 0;
                    while ($tmp[$i] != '<') {
                        $i += 1;
                    }
                    $i -= 1;
                    while (ctype_space($tmp[$i])) {
                        $i -= 1;
                    }
                    $packetaStatus = substr($tmp, 0, $i + 1);
                }
            }

            // Get status changes
            $statusChanges = [];
            $statusesUrl = $trackingUrl;
            $statusesUrl = str_replace('?id=', 'tracking/ex-track?id=', $statusesUrl);
            $response = $requests->get($statusesUrl);
            $html = '';
            if ($response['code'] == 200) {
                $html = $response['data'];
                $html = str_replace(["\t", "\n"], "", $html);
            }
            if ($html != '') {
                $endLoop = false;
                $tmp = $html;
                while (!$endLoop) {
                    $startRowNeedle = '<tr>';
                    $startRowPos = strpos($tmp, $startRowNeedle);
                    if ($startRowPos === false) {
                        $endLoop = true;
                        continue;
                    }
                    $row = $this->getInnerHTML($tmp, 'tr');
                    if ($row != '') {
                        $date = trim($this->getInnerHTML($row, 'th'));
                        $status = trim($this->getInnerHTML($row, 'td'));
                        $statusChanges[] = [
                            'date' => $date,
                            'status' => $status,
                        ];
                        $endRowNeedle = '</tr>';
                        $endRowPos = strpos($tmp, $endRowNeedle);
                        if ($endRowPos === false) {
                            $endLoop = true;
                        } else {
                            $tmp = substr($tmp, $endRowPos + strlen($endRowNeedle));
                        }
                    } else {
                        $endLoop = true;
                    }
                }
            }

            $info = $this->info;
            if ($info === null) {
                $info = [];
            }
            $info['packeta'] = [
                'status' => $packetaStatus,
                'changes' => $statusChanges,
            ];
            $this->info = $info;
            $this->save();
        }
    }

    public static function refreshTable()
    {
        $dropin = new \Driverfury\Dropin\Dropin('zarosrls@gmail.com', 'medi9810');
        $orders = $dropin->getOrders();
        $shippings = [];
        foreach ($orders as $order) {
            $orderId = $order['id'];
            $details = $dropin->getOrderDetails(strval($orderId));
            if ($details === null || !isset($details['order'])) {
                continue;
            }
            $shipping = \App\third\DropinShipping::find($orderId);
            if ($shipping === null) {
                $shipping = \App\third\DropinShipping::create([
                    'id' => $orderId,
                    'name' => $details['order']['customer_full_name'],
                    'phone' => $details['order']['customer_mobile'],
                    'email' => $details['order']['customer_email'],
                    'address' => $details['order']['customer_address'],
                    'city' => $details['order']['customer_city'],
                    'postcode' => $details['order']['customer_post_code'],
                    'notes' => $details['order']['comment'],
                    'amount' => floatval($details['order']['items_price']),
                    'delivery_price' => floatval($details['order']['delivery_charge']),
                    'tax' => floatval($details['order']['tax']),
                    'payment_method' => (($details['order']['is_cod']) ? 'cod' : 'online'),
                    'status' => $details['orderStatus']['status_title'],
                    'info' => [],
                    'created_at' => $details['order']['created_at'],
                    'updated_at' => $details['order']['updated_at'],
                ]);
            }
            if (isset($details['tracking']['tracking_url'])) {
                $shipping->tracking_url = $details['tracking']['tracking_url'];
                $shipping->save();
            }
            $shipping->refreshShippingStatus();

            $shippings[] = $shipping;
        }

        return $shippings;
    }
}
