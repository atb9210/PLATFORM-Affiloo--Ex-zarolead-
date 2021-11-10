<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class EcommerceCart extends Model
{
    protected $table = 'third_ecommerce_carts';

    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email', 'status', 'info', 'notes',
        'time',
    ];

    protected $casts = [
        'info' => 'array',
        'time' => 'datetime',
    ];

    public static function decodeCartObj($s)
    {
        $type = 'x';
        $len = 0;
        $val = '';

        if(strlen($s) >= 3)
        {
            $type = $s[0];
            $s = substr($s, 2);

            $i = 0;
            while (is_numeric($s[$i])) {
                $i += 1;
            }
            $len = intval(substr($s, 0, $i));

            if ($type == 's') {
                $s = substr($s, $i + 1);
                $val = substr($s, 0, -1);
                $val = substr($val, 1, -1);
                return $val;
            } else if ($type == 'i') {
                return intval($len);
            } else if ($type == 'd') {
                return floatval($len);
            } else if ($type == 'a') {
                $s = substr($s, $i + 1);
                $val = $s;
                if ($val[strlen($val)-1] == ';') {
                    $val = substr($s, 0, -1);
                }
                $val = substr($val, 1, -1);

                $items = [];
                $start = 0;
                $end = 0;
                $scope = 0;
                while($end < strlen($val)) {
                    if ($val[$end] == '{') {
                        $scope += 1;
                    } else if ($val[$end] == '}') {
                        $scope -= 1;
                        if ($scope == 0) {
                            if ($start > 0) {
                                $start += 1;
                            }
                            $items[] = substr($val, $start, $end - $start + 1);
                            $start = $end;
                        }
                    } else if ($val[$end] == ';' && $scope == 0) {
                        if ($start > 0) {
                            $start += 1;
                        }
                        $items[] = substr($val, $start, $end - $start);
                        $start = $end;
                    }
                    $end += 1;
                }

                $even = true;
                $keys = [];
                $values = [];
                foreach ($items as $item) {
                    if ($item == '') {
                        continue;
                    }
                    $item .= ';';
                    if($even) {
                        $keys[] = EcommerceCart::decodeCartObj($item);
                        $even = false;
                    } else {
                        $values[] = EcommerceCart::decodeCartObj($item);
                        $even = true;
                    }
                }

                $arr = [];
                $cnt = min(count($keys), count($values));
                for ($i = 0; $i < $cnt; $i += 1) {
                    $arr[$keys[$i]] = $values[$i];
                }
                return $arr;
            } else {
                die("Invalid");
            }
        }

        return null;
    }

    public static function sync()
    {
        // TODO: Authorization

        $carts = EcommerceCart::all();
        $newCarts = [];

        $url = 'https://homygadget.com/zaroapi/carts.php';
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        if ($response != null && $response != '') {
            $newCarts = json_decode($response, true);
        }

        for ($cartIndex = 0; $cartIndex < count($newCarts); $cartIndex += 1) {
            $cart = $newCarts[$cartIndex];

            $alreadyExist = false;
            for ($j = 0; $j < count($carts); ++$j) {
                if ($carts[$j]->id == $newCarts[$cartIndex]['id']) {
                    $alreadyExist = true;
                    break;
                }
            }

            if (!$alreadyExist) {
                $newCarts[$cartIndex]['location'] = EcommerceCart::decodeCartObj($cart['location']);
                $newCarts[$cartIndex]['cart_contents'] = EcommerceCart::decodeCartObj($cart['cart_contents']);
                $newCarts[$cartIndex]['other_fields'] = EcommerceCart::decodeCartObj($cart['other_fields']);
                $info = [
                    'location'      => $newCarts[$cartIndex]['location'],
                    'cart_contents' => $newCarts[$cartIndex]['cart_contents'],
                    'other_fields'  => $newCarts[$cartIndex]['other_fields'],
                    'cart_total'    => $newCarts[$cartIndex]['cart_total'],
                    'currency'      => $newCarts[$cartIndex]['currency'],
                ];

                $params = [
                    'first_name' => $newCarts[$cartIndex]['name'],
                    'last_name' => $newCarts[$cartIndex]['surname'],
                    'email' => $newCarts[$cartIndex]['email'],
                    'phone' => $newCarts[$cartIndex]['phone'],
                    'info' => $info,
                    'time' => $newCarts[$cartIndex]['time'],
                ];
                EcommerceCart::create($params);
            }
        }

        $carts = EcommerceCart::all();
        return($carts);
    }
}
