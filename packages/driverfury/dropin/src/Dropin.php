<?php

namespace Driverfury\Dropin;

// DEBUG
use Illuminate\Support\Facades\Log;

class Dropin
{
    protected static string $apiUrl = 'https://eurowholesale.eu';
        
    protected function getRequest(string $path): array
    {
        $output = [];

        if (strlen($path) === 0) {
            return $output;
        }
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        $url = self::$apiUrl . $path;

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
        $objects = $this->getRequest('/partner_exports/it/it-zaro/65/it-zaro-620e4273c1c2d.xml');

        $objects = array_map(function ($objs) {
            $mappedObjects = [];
            foreach ($objs as $obj) {
                $mappedObjects[] = [
                    'name' => $obj['Product_Name'],
                    'description' => $obj['Description'],
                    'category' => $obj['Category'],
                    'sku' => $obj['SKU'],
                    'variations' => $obj['Variation'],
                    'dropshipping_price' => $obj['Dropshipping_Price'],
                    'sale_price' => $obj['Suggested_Sale_Price'],
                    'price' => $obj['Suggested_Sale_Price'],
                    'quantity' => $obj['Total_Quantity'],
                ];
            }
            return $mappedObjects;
        }, $objects);
        $objects = $objects['product'];

        return $objects;
    }
}
