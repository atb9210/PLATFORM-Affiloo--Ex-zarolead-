<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\third\WarehouseLead;

class WarehouseController extends Controller
{
    private $statusValidation = 'in:confirmed,no answer,cancelled,fake,duplicate,to recall,to do';

    /**
     * Get leads list.
     *
     * @bodyParam filter_date_from string From date (Y-m-d format)
     * @bodyParam filter_date_to string To date (Y-m-d format)
     * @bodyParam filter_status string Leads status
     * @bodyParam filter_product string Leads product's SKU
     *
     * @response {
     *  "data": [leads],
     * }
     */
    public function leads(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to'   => 'nullable|date|date_format:Y-m-d',
            'filter_status'    => 'nullable|string|' . $this->statusValidation,
            'filter_product'   => 'nullable|string',
        ]);

        $q = WarehouseLead::query();
        if (isset($params['filter_date_from'])) {
            $q = $q->where('created_at', '>=', $params['filter_date_from'] . " 00:00:00");
        }
        if (isset($params['filter_date_to'])) {
            $q = $q->where('created_at', '<=', $params['filter_date_to'] . " 23:59:59");
        }
        if (isset($params['filter_status'])) {
            $q = $q->where('status', $params['filter_status']);
        }
        if (isset($params['filter_product'])) {
            $q = $q->where('info->sku', $params['filter_product']);
        }
        $objects = $q->orderBy('created_at', 'desc')->get();

        return response([
            'data' => $objects,
        ]);
    }

    /**
     * Create a lead.
     *
     * @bodyParam first_name string required Lead's first name
     * @bodyParam last_name string required Lead's last name
     * @bodyParam phone string required Lead's phone number
     * @bodyParam notes string Lead's notes
     * @bodyParam info string Lead's info
     * @bodyParam status string Lead's status
     * @bodyParam order_id string Lead's order id
     * @bodyParam sku string Lead's sku
     * @bodyParam product_name string Lead's product
     * @bodyParam price string Lead's price
     * @bodyParam total string Lead's total
     * @bodyParam quantity string Lead's quantity
     *
     * @response {
     *  "success": true,
     *  "data": lead,
     * }
     */
    public function createLead(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'first_name' => 'required|string|min:1',
            'last_name'  => 'required|string|min:1',
            'phone'      => 'required|string|min:7',
            'notes'      => 'string',
            'info'       => 'json',
            'status'     => 'string|' . $this->statusValidation,

            'order_id'     => 'numeric',
            'sku'          => 'string',
            'product_name' => 'string',
            'price'        => 'numeric',
            'total'        => 'numeric',
            'quantity'     => 'numeric',

            'ip'             => 'nullable|string',
            'user_agent'     => 'nullable|string',
            'source_url'     => 'nullable|string',
            'affiliate_id'   => 'nullable|numeric',
            'traffic_source' => 'nullable|string',
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        if (isset($params['order_id'])) {
            $params['info']['order_id'] = $params['order_id'];
        }
        if (isset($params['sku'])) {
            $params['info']['sku'] = $params['sku'];
        }
        if (isset($params['product_name'])) {
            $params['info']['product_name'] = $params['product_name'];
        }
        if (isset($params['price'])) {
            $params['info']['price'] = $params['price'];
        }
        if (isset($params['total'])) {
            $params['info']['total'] = $params['total'];
        }
        if (isset($params['quantity'])) {
            $params['info']['quantity'] = $params['quantity'];
        }

        if (isset($params['ip'])) {
            $params['info']['ip'] = $params['ip'];
        }
        if (isset($params['user_agent'])) {
            $params['info']['user_agent'] = $params['user_agent'];
        }
        if (isset($params['source_url'])) {
            $params['info']['source_url'] = $params['source_url'];
        }
        if (isset($params['affiliate_id'])) {
            $params['info']['affiliate_id'] = $params['affiliate_id'];
        }
        if (isset($params['traffic_source'])) {
            $params['info']['traffic_source'] = $params['traffic_source'];
        }

        $lead = WarehouseLead::create($params);

        $sku = '';
        if (isset($params['sku'])) {
            $sku = $params['sku'];
        }
        if ($sku !== '') {
            $phone = $params['phone'];
            $checkLead = WarehouseLead::where('phone', $phone)->get();
            foreach ($checkLead as $l) {
                if ($l !== null) {
                    if ($l->id != $lead->id && $l->info !== null && isset($l->info['sku']) &&
                        $l->info['sku'] == $sku) {
                        $lead->status = 'duplicate';
                        $lead->save();
                    }
                }
            }
        }

        return response([
            'status' => 'success',
            'data' => $lead,
        ]);
    }
    
    /**
     * Update a lead.
     *
     * @urlParam id int required Lead's id
     *
     * @bodyParam first_name string Lead's first name
     * @bodyParam last_name string Lead's last name
     * @bodyParam phone string Lead's phone number
     * @bodyParam notes string Lead's notes
     * @bodyParam info string Lead's info
     * @bodyParam status string Lead's status
     *
     * @response {
     *  "data": lead,
     * }
     */
    public function updateLead($id, Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'first_name' => 'string|min:1',
            'last_name'  => 'string|min:1',
            'phone'      => 'nullable|string|min:7',
            'email'      => 'nullable|string|min:4',
            'notes'      => 'string',
            'info'       => 'json',
            'status'     => 'string|in:confirmed,no answer,cancelled,fake,duplicate,to recall',
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        $lead = WarehouseLead::findOrFail($id);
        $lead->update($params);

        return response([
            'data' => $lead,
        ]);
    }

    /**
     * Delete a lead.
     *
     * @urlParam id int required Lead's id
     *
     * @response {
     *  "message": "Lead successfully deleted",
     * }
     */
    public function destroyLead($id, Request $request)
    {
        // TODO: Authorization

        $lead = WarehouseLead::findOrFail($id);
        $lead->delete();

        return response([
            'message' => 'Lead successfully deleted',
        ]);
    }

    /**
     * Get stock products list.
     *
     * @response {
     *  "data": [products],
     * }
     */
    public function products()
    {
        // TODO: Authorization

        $elogy = new \Driverfury\Elogy\Elogy(
            env('ELOGY_API_TOKEN'),
            env('ELOGY_EMAIL'),
            env('ELOGY_PASSWORD'));
        $objects = $elogy->getProducts();

        return response([
            'data' => $objects,
        ]);
    }

    private function mapDelivery(array|null $delivery)
    {
        if ($delivery === null) {
            return [];
        } else {
            return [
                'order_number'  => strval($delivery['order_number']),
                'gateway'       => $delivery['gateway'],
                'products'      => $delivery['products'],
                'status'        => $delivery['status'],
                'lead_status'   => $delivery['lead_status'],
                'tracking_link' => $delivery['tracking_link'],
                'created_at'    => $delivery['created_at'],
                'completed_at'  => $delivery['completed_at'],
            ];
        }
    }

    /**
     * Create delivery (elogy).
     *
     * @bodyParam lead_id string Lead's id
     * @bodyParam params json required Delivery's params
     *
     * @response {
     *  "message": "Order pushed",
     * }
     */
    public function createDelivery(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'lead_id' => 'nullable',
            'params'  => 'json|required',
        ]);

        $deliveryParams = json_decode($params['params'], true);

        $elogy = new \Driverfury\Elogy\Elogy(
            env('ELOGY_API_TOKEN'),
            env('ELOGY_EMAIL'),
            env('ELOGY_PASSWORD'));
        $elogyOrder = $elogy->createOrder($deliveryParams);
        if ($elogyOrder === null) {
            if (isset($params['lead_id']) && $params['lead_id'] !== null) {
                $leadId = $params['lead_id'];
                $lead = WarehouseLead::findOrFail($leadId);
                $info = $lead->info;
                $info['delivery'] = null;
                $lead->info = $info;
                $lead->save();
            }

            return response([
                'message' => 'Cannot push the order',
            ], 500);
        }

        if (isset($params['lead_id']) && $params['lead_id'] !== null) {
            $leadId = $params['lead_id'];

            $elogyOrder = $elogy->getOrderByNumber(strval($elogyOrder['order_number']));
            if($elogyOrder !== null) {
                $lead = WarehouseLead::findOrFail($leadId);
                $info = $lead->info;
                $info['delivery'] = $this->mapDelivery($elogyOrder);
                $lead->info = $info;
                $lead->save();
            }

            return response([
                'message' => 'Order pushed',
                'data' => $lead,
            ]);
        }

        return response([
            'message' => 'Order pushed',
        ]);
    }

    /**
     * Get delivery (elogy).
     *
     * @urlParam lead_id string Lead's id
     *
     * @response {
     *  "data": [delivery],
     * }
     */
    public function getDelivery($lead_id, Request $request)
    {
        // TODO: Authorization

        $lead = WarehouseLead::findOrFail($lead_id);

        $delivery = null;
        if ($lead->info !== null && isset($lead->info['delivery']) && isset($lead->info['delivery']['order_number'])) {
            $elogy = new \Driverfury\Elogy\Elogy(
                env('ELOGY_API_TOKEN'),
                env('ELOGY_EMAIL'),
                env('ELOGY_PASSWORD'));
            $delivery = $elogy->getOrderByNumber($lead->info['delivery']['order_number']);

            $info = $lead->info;
            $info['delivery'] = $this->mapDelivery($delivery);
            $lead->info = $info;
            $lead->save();
        }
        return response([
            'data' => $delivery,
        ]);
    }
}
