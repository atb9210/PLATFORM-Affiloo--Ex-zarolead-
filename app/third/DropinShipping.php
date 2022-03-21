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

    private function substrFromTo(string $haystack, string $needleStart, string $needleEnd = ''): string
    {
        $result = '';

        $startPos = strpos($haystack, $needleStart);
        if ($startPos !== false) {
            $result = substr($haystack, $startPos + strlen($needleStart));
        }

        if ($needleEnd != '') {
            $endPos = strpos($result, $needleEnd);
            if ($endPos !== false) {
                $result = substr($result, 0, $endPos);
            }
        }

        return $result;
    }

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

    private function getOuterHtml(string $html, string $tag): string
    {
        $outerHTML = '';

        $elementPosition = $this->getHtmlElementPosition($html, $tag);
        if ($elementPosition['startInnerHtml'] >= 0 && $elementPosition['endInnerHtml'] >= 0) {
            $start = $elementPosition['start'];
            $end = $elementPosition['end'];
            $outerHTML = substr($html, $start, ($end - $start));
        }

        return $outerHTML;
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
    }

    private function parseTableHtmlKeyValue(string $innerHtml): array
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

            $row = [];
            $endRow = false;
            while (!$endRow) {
                $value = '';
                $tdPos = $this->getHtmlElementPosition($tr, 'td');
                if ($tdPos['start'] >= 0 && $tdPos['end'] >= 0) {
                    $value = trim($this->getInnerHtml($tr, 'td'));
                } else {
                    $endRow = true;
                    continue;
                }
                $row[] = $value;
                $tr = substr($tr, $tdPos['end']);
            }

            $table[] = $row;
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
            $table = $this->parseTableHtmlKeyValue($tableHtml);
            foreach ($table as $key => $value) {
                if ($key == 'Cash on delivery') {
                    $codAmount = floatval($value);
                    break;
                }
            }

            // Get external carrier tracking number and url
            $brtTrackingNumber = '';
            $brtTrackingUrl = '';
            $tableHtmlPos = $this->getHtmlElementPosition($html, 'table');
            $tmp = substr($html, $tableHtmlPos['end']);
            $tableHtml = $this->getInnerHtml($tmp, 'table');
            $table = $this->parseTableHtmlKeyValue($tableHtml);
            foreach ($table as $key => $value) {
                $needle = '<a href="';
                if (strpos($value, $needle) !== false) {
                    $brtTrackingNumber = $this->getInnerHtml($value, 'a');
                    $brtTrackingUrl = $this->substrFromTo($value, '<a href="', '"');
                    $brtTrackingUrl = str_replace('&amp;', '&', $brtTrackingUrl);
                    break;
                }
            }

            // TODO: Old code
            /*
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
                $table = $this->parseTableHtmlKeyValue($tableHtml);
                foreach ($table as $key => $value) {
                    $statusChanges[] = [
                        'date' => $key,
                        'status' => $value,
                    ];
                }
            }
            */

            // Get status changes
            $statusChanges = [];
            if ($brtTrackingUrl != '') {
                $response = $requests->get($brtTrackingUrl);
                $html = '';
                if ($response['code'] == 200) {
                    $html = $response['data'];
                    $html = str_replace(["\t", "\n"], "", $html);
                }
                // Look for the right table
                $tmp = $html;
                $tableFound = false;
                $tableHtml = '';
                while (!$tableFound) {
                    $tableHtmlPos = $this->getHtmlElementPosition($tmp, 'table');
                    if ($tableHtmlPos['end'] < 0) {
                        break;
                    }
                    $tableOuterHtml = $this->getOuterHtml($tmp, 'table');
                    if (strpos($tableOuterHtml, 'table_stato_dati') !== false) {
                        $tableFound = true;
                        $tableHtml = $tableOuterHtml;
                        continue;
                    }
                    $tmp = substr($tmp, $tableHtmlPos['end']);
                }
                if ($tableFound) {
                    $tableBody = $this->getInnerHtml($tableHtml, 'tbody');
                    // Skip first row of the table (header)
                    $tableRowPos = $this->getHtmlElementPosition($tableBody, 'tr');
                    $tableBody = substr($tableBody, $tableRowPos['end']);
                    $table = $this->parseTableHtml($tableBody);
                    foreach ($table as $row) {
                        $tmp = explode('.', $row[0]);
                        $date = $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0];
                        $time = '00:00:00';
                        if ($row[1] != '') {
                            $time = str_replace('.', ':', $row[1]) . ':00';
                        }
                        $datetime = $date . ' ' . $time;
                        $status = $row[3];
                        $statusChanges[] = [
                            'date' => $datetime,
                            'status' => $status,
                        ];
                    }
                }
                $isReturn = false;
                $isDelivered = false;
                foreach ($statusChanges as $change) {
                    if ($change['status'] == 'RESO MITTENTE') {
                        $isReturn = true;
                    }
                }
                $isDelivered = (count($statusChanges) > 0 && $statusChanges[0]['status'] == 'CONSEGNATA');
                if ($isReturn) {
                    $tmp = $statusChanges;
                    $statusChanges = [];
                    for ($i = count($tmp) - 1; $i >= 0; $i -= 1) {
                        $change = $tmp[$i];
                        $statusChanges[] = $change;
                        if ($change['status'] == 'RESO MITTENTE') {
                            break;
                        }
                    }
                    if ($isDelivered) {
                        $statusChanges[] = [
                            'date' => $tmp[0]['date'],
                            'status' => 'RIT. CONSEGNATA',
                        ];
                    }
                    $statusChanges = array_reverse($statusChanges);
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
            $info['bartolini'] = [
                'tracking_number' => $brtTrackingNumber,
                'tracking_url' => $brtTrackingUrl,
                'changes' => $statusChanges,
            ];
            $info['cod_amount'] = $codAmount;
            $info['status_check_date'] = date('Y-m-d');
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
