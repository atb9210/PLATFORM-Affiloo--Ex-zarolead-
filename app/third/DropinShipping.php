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

    private function getHtmlElementPosition(string $html, string $tag): array
    {
        $start = -1;
        $end = -1;
        $startInnerHtml = -1;
        $endInnerHtml = -1;

        $tmpHtml = $html;

        $needleStart = '<'  . $tag . '>';
        $needleEnd   = '</' . $tag . '>';
        $needleEndLen = strlen($needleEnd);

        $needleStartPos = strpos($tmpHtml, $needleStart);
        if ($needleStartPos === false) {
            $needleStart = '<'  . $tag;
            $needleStartPos = strpos($tmpHtml, $needleStart);
            if ($needleStartPos === false || !ctype_space($tmpHtml[$needleStartPos + strlen($needleStart)])) {
                $needleStartPos = false;
            }
        }
        $initialTagEndRel = -1;
        $startOfInnerHtmlRel = -1;
        if ($needleStartPos !== false) {
            $i = 0;
            while ($tmpHtml[$needleStartPos + $i] != '>') {
                $i += 1;
            }
            $initialTagEndRel = $i;
            $startOfInnerHtmlRel = $initialTagEndRel + 1;
            $tmpHtml = substr($tmpHtml, $needleStartPos + $startOfInnerHtmlRel);
        }

        $needleEndPos = strpos($tmpHtml, $needleEnd);

        if ($needleStartPos !== false && $needleEndPos !== false) {
            $start = $needleStartPos;
            $startInnerHtml = $needleStartPos + $startOfInnerHtmlRel;
            $endInnerHtml = $needleStartPos + $startOfInnerHtmlRel + $needleEndPos;
            $end = $endInnerHtml + $needleEndLen;
        }

        return [
            'start' => $start,
            'end' => $end,
            'startInnerHtml' => $startInnerHtml,
            'endInnerHtml' => $endInnerHtml,
        ];
    }

    private function getInnerHtml(string $html, string $tag): string
    {
        $innerHTML = '';

        $elementPosition = $this->getHtmlElementPosition($html, $tag);
        if ($elementPosition['startInnerHtml'] >= 0 && $elementPosition['endInnerHtml'] >= 0) {
            $start = $elementPosition['startInnerHtml'];
            $end = $elementPosition['endInnerHtml'];
            $innerHTML = substr($html, $start, ($end - $start));
        }

        return $innerHTML;

        // TODO: to delete from now on?????? (old code)
        /*
        $needleStart = '<'  . $tag . '>';
        $needleEnd   = '</' . $tag . '>';

        $needleStartPos = strpos($html, $needleStart);
        if ($needleStartPos === false) {
            $needleStart = '<'  . $tag;
            $needleStartPos = strpos($html, $needleStart);
            if ($needleStartPos === false || !ctype_space($html[$needleStartPos + strlen($needleStart)])) {
                $needleStartPos = false;
            }
        }
        if ($needleStartPos !== false) {
            $i = 0;
            while ($html[$needleStartPos + $i] != '>') {
                $i += 1;
            }
            $html = substr($html, $needleStartPos + $i + 1);
        }

        $needleEndPos = strpos($html, $needleEnd);
        if ($needleEndPos !== false) {
            $html = substr($html, 0, $needleEndPos);
        }

        $innerHTML = $html;

        return $innerHTML;
        */
    }

    private function parseTableHtml(string $innerHtml): array
    {
        $table = [];

        $endLoop = false;
        while (!$endLoop) {
            $trPos = $this->getHtmlElementPosition($innerHtml, 'tr');
            if ($trPos['start'] < 0 || $trPos['end'] < 0) {
                $endLoop = true;
                continue;
            }
            $tr = $this->getInnerHtml($innerHtml, 'tr');

            $key = null;
            $thPos = $this->getHtmlElementPosition($tr, 'th');
            if ($thPos['start'] >= 0 && $thPos['end'] >= 0) {
                $key = trim($this->getInnerHtml($tr, 'th'));
            }

            $value = '';
            $tdPos = $this->getHtmlElementPosition($tr, 'td');
            if ($tdPos['start'] >= 0 && $tdPos['end'] >= 0) {
                $value = trim($this->getInnerHtml($tr, 'td'));
            }

            if ($key !== null) {
                $table[$key] = $value;
            } else {
                $table[] = $value;
            }

            $innerHtml = substr($innerHtml, $trPos['end']);
        }

        return $table;
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

            // Get COD amount
            $codAmount = 0.0;
            $tableHtml = $this->getInnerHtml($html, 'table');
            $table = $this->parseTableHtml($tableHtml);
            foreach ($table as $key => $value) {
                if ($key == 'Cash on delivery') {
                    $codAmount = floatval($value);
                    break;
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
                $tableHtml = $this->getInnerHtml($html, 'table');
                $table = $this->parseTableHtml($tableHtml);
                foreach ($table as $key => $value) {
                    $statusChanges[] = [
                        'date' => $key,
                        'status' => $value,
                    ];
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
            $info['cod_amount'] = $codAmount;
            $this->info = $info;
            $this->save();
        }
    }

    public static function importShipping($id, $dropin = null)
    {
        if ($dropin === null) {
            $dropin = new \Driverfury\Dropin\Dropin('zarosrls@gmail.com', 'medi9810');
        }
        $details = $dropin->getOrderDetails(strval($id));
        if ($details === null || !isset($details['order'])) {
            return null;
        }
        $shipping = \App\third\DropinShipping::find($id);
        if ($shipping === null) {
            $shipping = \App\third\DropinShipping::create([
                'id' => $id,
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
        }
        $info = $shipping->info;
        if ($info === null) {
            $info = [];
        }
        $info['order_id'] = $details['order']['external_id'];
        $shipping->info = $info;
        $shipping->save();
        $shipping->refreshShippingStatus();

        return $shipping;
    }

    public static function refreshTable()
    {
        $dropin = new \Driverfury\Dropin\Dropin('zarosrls@gmail.com', 'medi9810');
        $orders = $dropin->getOrders();
        $shippings = [];
        foreach ($orders as $order) {
            $shipping = self::importShipping($order['id'], $dropin);
            $shippings[] = $shipping;
        }

        return $shippings;
    }
}
