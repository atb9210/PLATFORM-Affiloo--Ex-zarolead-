<?php

namespace Driverfury\Dropin;

use Driverfury\Requests\Requests;

use Illuminate\Support\Facades\Storage;

// DEBUG
use Illuminate\Support\Facades\Log;

class Dropin
{
    protected static string $apiUrl = 'https://dropship.eurowholesale.eu';
    protected static string $apiUrlProducts = 'https://eurowholesale.eu';
    protected Requests $http;

    protected string $email = '';
    protected string $password = '';
    protected bool $loggedIn = false;

    protected static string $defaultEmail = 'mdjordje82@gmail.com';

    public function __construct(string $email = '', string $password = '')
    {
        $this->http = new Requests();

        $this->email = $email;
        $this->password = $password;
        $this->loggedIn = false;
        if ($this->email != '' && $this->password != '') {
            $this->loggedIn = $this->login($this->email, $this->password);
        }
    }

    protected function login(string $email, string $password)
    {
        $headers = [
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ];
        $response = $this->http->get(self::$apiUrl . '/sanctum/csrf-cookie', $headers);
        if ($response['code'] != 204) {
            return false;
        }

        $cookies = $this->http->getCookies();
        if (isset($cookies['XSRF-TOKEN'])) {
            $xsrfToken = $cookies['XSRF-TOKEN'];
            $headers['x-xsrf-token'] = $xsrfToken;
        }
        $headers['referer'] = 'https://dropship.eurowholesale.eu/auth/login';
        $headers['user-agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36';
        $headers['x-requested-with'] = 'XMLHttpRequest';

        $response = $this->http->post(self::$apiUrl . '/login', $headers, [
            'email' => $email,
            'password' => $password,
            'status' => true,
        ]);
        if ($response['code'] != 204) {
            return false;
        }

        return true;
    }

    protected function getHeaders()
    {
        $headers = [
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'referer' => 'https://dropship.eurowholesale.eu/orders-list',
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36',
            'x-requested-with' => 'XMLHttpRequest',
        ];

        $cookies = $this->http->getCookies();
        if (isset($cookies['XSRF-TOKEN'])) {
            $xsrfToken = $cookies['XSRF-TOKEN'];
            $headers['x-xsrf-token'] = $xsrfToken;
        }

        return $headers;
    }

    public function getOrders(): array
    {
        //TODO: What if he's not logged in?

        $objects = [];
        $response = $this->http->get(self::$apiUrl . '/api/orders-list', $this->getHeaders());
        if ($response['code'] == 200) {
            $objects = $response['data'];
        }
        return $objects;
    }

    public function getOrderDetails($id): array|null
    {
        // TODO: What if he's not logged in?

        $object = null;
        $response = $this->http->get(self::$apiUrl . '/api/order-details', $this->getHeaders(), ['orderId' => $id]);
        if ($response['code'] == 200) {
            $object = $response['data'];
        }
        return $object;
    }

    protected function getRequestXML(string $path): array
    {
        $output = [];

        if (strlen($path) === 0) {
            return $output;
        }
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        $url = self::$apiUrlProducts . $path;
        $content = file_get_contents($url);

        if ($content !== null) {
            try {
                $obj = simplexml_load_string($content);
                $output = json_decode(json_encode($obj), true);
            } catch (Exception $e) {
                $output = [];
            }
        }

        return $output;
    }

    public function getProducts(): array
    {
        // TODO: Get products from the unofficial APIs??

        $objects = $this->getRequestXML('/partner_exports/it/it-zaro/65/it-zaro-620e4273c1c2d.xml');

        $objects = array_map(function ($objs) {
            $mappedObjects = [];
            foreach ($objs as $obj) {
                $description = '';
                if (!is_array($obj['Description'])) {
                    $description = $obj['Description'];
                }
                $variations = '';
                if (!is_array($obj['Variation'])) {
                    $variations = $obj['Variation'];
                }
                $images = [];
                if (isset($obj['Product_Images']['Image'])) {
                    $images = $obj['Product_Images']['Image'];
                }
                $mappedObjects[] = [
                    'name' => $obj['Product_Name'],
                    'description' => $description,
                    'category' => $obj['Category'],
                    'sku' => $obj['SKU'],
                    'variations' => $variations,
                    'dropshipping_price' => $obj['Dropshipping_Price'],
                    'sale_price' => $obj['Suggested_Sale_Price'],
                    'price' => $obj['Suggested_Sale_Price'],
                    'quantity' => $obj['Total_Quantity'],
                    'images' => $images,
                ];
            }
            return $mappedObjects;
        }, $objects);
        $objects = $objects['product'];

        return $objects;
    }

    /*
     * NOTE: Example orders array
     *
     * $orders = [
     *     [
     *         'id' => 20679,
     *         'date' => '2022-04-26',
     *         'gateway' => 'cashondelivery',
     *         'total' => '69.69',
     *         'products' => [
     *             [
     *                 'sku' => '1234-567-89012',
     *                 'quantity' => 1,
     *             ],
     *         ],
     *         'phone' => '+(39)1234567890',
     *         'full_name' => 'Test test',
     *         'street_address' => 'Via Verdi 1',
     *         'city' => 'Milano',
     *         'province' => 'MI',
     *         'postcode' => 37055,
     *         'country' => 'IT',
     *         'notes' => 'test order',
     *     ],
     * ];
     */
    public function uploadOrders(array $orders): bool
    {
        $csv = '';

        foreach ($orders as $order) {
            $email = self::$defaultEmail;
            if (isset($order['email']) && $order['email'] != '') {
                $email = $order['email'];
            }

            $phone = '';
            if (isset($order['phone'])) {
                $phone = $order['phone'];
            }

            $productsString = '[';
            if (isset($order['products'])) {
                $counter = 0;
                foreach ($order['products'] as $product) {
                    if ($counter > 0) {
                        $productsString .= ',';
                    }
                    $sku = '';
                    $quantity = '';
                    if (isset($product['sku'])) {
                        $sku = $product['sku'];
                    }
                    if (isset($product['quantity'])) {
                        $quantity = strval($product['quantity']);
                    }
                    $productsString .= '[';
                    $productsString .= '""' . $sku . '"",""' . $quantity . '""';
                    $productsString .= ']';
                    $counter += 1;
                }
            }
            $productsString .= ']';

            $formattedDate = date('Y-m-d');
            if (isset($order['date']) && $order['date'] != '') {
                $formattedDate = date('Y-m-d', strtotime($order['date']));
            } else if (isset($order['created_at']) && $order['created_at'] != '') {
                $formattedDate = date('Y-m-d', strtotime($order['created_at']));
            }

            $dropinGateways = [
                'cashondelivery' => 'Cod',
                'online'         => 'Prepaid',
            ];
            $gateway = 'Cod';
            if (isset($order['gateway'])) {
                $gateway = $order['gateway'];
            } else if (isset($order['payment_method'])) {
                $gateway = $order['payment_method'];
            } else if (isset($order['method'])) {
                $gateway = $order['method'];
            }

            if (array_key_exists($gateway, $dropinGateways)) {
                $gateway = $dropinGateways[$gateway];
            }

            $orderId = '0';
            if (isset($order['id'])) {
                $orderId = strval($order['id']);
            } else if (isset($order['order_id'])) {
                $orderId = strval($order['order_id']);
            }


            $total = 0.0;
            if (isset($order['total'])) {
                $total = $order['total'];
            }
            $total = strval($total);

            $fullName = '';
            if (isset($order['full_name'])) {
                $fullName = $order['full_name'];
            } else if (isset($order['first_name']) && isset($order['last_name'])) {
                $fullName = $order['first_name'] . ' ' . $order['last_name'];
            }

            $streetAddress = '';
            if (isset($order['street_address'])) {
                $streetAddress = $order['street_address'];
            } else if (isset($order['address'])) {
                $streetAddress = $order['address'];
            }

            $cityAndProvince = '';
            if (isset($order['city'])) {
                $cityAndProvince = $order['city'];
            }
            if (isset($order['province'])) {
                $cityAndProvince .= ' (' . $order['province'] . ')';
            } else if (isset($order['state'])) {
                $cityAndProvince .= ' (' . $order['state'] . ')';
            }

            $postcode = '';
            if (isset($order['postcode'])) {
                $postcode = strval($order['postcode']);
            }

            $country = 'IT';
            if (isset($order['country'])) {
                $country = $order['country'];
            }

            $shippingNotes = '';
            if (isset($order['notes'])) {
                $shippingNotes = $order['notes'];
            } else if (isset($order['shipping_notes'])) {
                $shippingNotes = $order['shipping_notes'];
            }

            $csv .= '"' . $formattedDate . '",';
            $csv .= '"' . $gateway . '",';
            $csv .= $orderId . ',';
            $csv .= '"' . $total . '",';
            $csv .= '"' . $productsString . '",';
            $csv .= '"' . $fullName . '",';
            $csv .= '"' . $streetAddress . '",';
            $csv .= '"' . $cityAndProvince . '",';
            $csv .= $postcode . ',';
            $csv .= '"' . $country . '",';
            $csv .= '"' . $email . '",';
            $csv .= '"' . $phone . '",';
            $csv .= '"' . $shippingNotes . '",';
            $csv .= "\n";
        }

        $csvPathname = 'public/tmp/orders.csv';
        Storage::put($csvPathname, $csv);
        $csvPathname = Storage::path($csvPathname);

        $headers = $this->getHeaders();
        unset($headers['content-type']);
        $headers['origin'] = 'https://dropship.eurowholesale.eu';
        $headers['accept'] = 'application/json, text/plain, */*';
        $headers['accept-encoding'] = 'gzip, deflate, br';
        $headers['accept-language'] = 'it-IT,it;q=0.9,en-US;q=0.8,en;q=0.7';
        $headers['sec-ch-ua'] = '" Not A;Brand";v="99", "Chromium";v="101", "Google Chrome";v="101"';
        $headers['sec-ch-ua-mobile'] = '?0';
        $headers['sec-ch-ua-platform'] = '"Windows"';
        $headers['sec-fetch-dest'] = 'empty';
        $response = $this->http->postFileText(self::$apiUrl . '/api/upload_orders', $csvPathname, $headers);

        if ($response['code'] == 200) {
            return true;
        } else {
            // DEBUG
            Log::channel('dropin')->info('Error while creating the shippings - REQUEST: ' . json_encode(['csv' => $csv]) . ' - RESPONSE: ' . json_encode($response));
        }

        return false;
    }
}
