<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\WarehouseLead;
use App\Http\Resources\api\v1\WarehouseLeadResource;

class WarehouseLeadController extends Controller
{
    /**
     * Get warehouse leads list.
     *
     * @response {
     *  "data": [warehouse_leads],
     * }
     */
    public function index(Request $request)
    {
        // TODO: Authorization

        $objects = WarehouseLead::query();
        $objects = $objects->where('archived', 0);

        return WarehouseLeadResource::collection($objects->get());
    }

    /**
     * Get warehouse lead's informations.
     *
     * @urlParam id int required Tag's id
     *
     * @response {
     *  "data": tag,
     * }
     */
    public function show($id)
    {
        // TODO: Authorization

        $object = WarehouseLead::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That warehouse lead is archived"
            ], 403);
        }

        return new WarehouseLeadResource($object);
    }

    /**
     * Create a warehouse lead.
     *
     * @bodyParam product_id int Warehouse lead's product
     * @bodyParam first_name string required Warehouse lead's first name
     * @bodyParam last_name string required Warehouse lead's last name
     * @bodyParam phone string Warehouse lead's phone number
     * @bodyParam email string Warehouse lead's email
     * @bodyParam address_line1 string Warehouse lead's address_line1
     * @bodyParam address_line2 string Warehouse lead's address_line2
     * @bodyParam city string Warehouse lead's city
     * @bodyParam postcode string Warehouse lead's postcode
     * @bodyParam zone string Warehouse lead's zone
     * @bodyParam country string Warehouse lead's country
     * @bodyParam info json Warehouse lead's info
     *
     * @response {
     *  "message": "Warehouse lead successfully created",
     *  "data": warehouse_lead,
     * }
     */
    public function store(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'product_id'    => 'integer|exists:products,id',
            'status'        => 'in:' . join(',', WarehouseLead::STATUSES),
            'first_name'    => 'required|string|min:1',
            'last_name'     => 'required|string|min:1',
            'phone'         => 'nullable|string|min:7',
            'email'         => 'nullable|string',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'city'          => 'nullable|string',
            'postcode'      => 'nullable|string',
            'zone'          => 'nullable|string',
            'country'       => 'nullable|string',
            'info'          => 'nullable|json',
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        $object = WarehouseLead::create($params);

        // Refresh hash
        $object->refreshHash();

        return response([
            'message' => 'Warehouse lead successfully created',
            'data' => new WarehouseLeadResource($object),
        ], 201);
    }
    
    /**
     * Update a warehouse lead.
     *
     * @urlParam id int required Warehouse lead's id
     *
     * @bodyParam product_id int Warehouse lead's product
     * @bodyParam first_name string Warehouse lead's first name
     * @bodyParam last_name string Warehouse lead's last name
     * @bodyParam phone string Warehouse lead's phone number
     * @bodyParam email string Warehouse lead's email
     * @bodyParam address_line1 string Warehouse lead's address_line1
     * @bodyParam address_line2 string Warehouse lead's address_line2
     * @bodyParam city string Warehouse lead's city
     * @bodyParam postcode string Warehouse lead's postcode
     * @bodyParam zone string Warehouse lead's zone
     * @bodyParam country string Warehouse lead's country
     * @bodyParam info json Warehouse lead's info
     *
     * @response {
     *  "data": warehouse_lead,
     * }
     */
    public function update($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseLead::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That warehouse lead is archived"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'product_id'    => 'integer|exists:products,id',
            'status'        => 'in:' . join(',', WarehouseLead::STATUSES),
            'first_name'    => 'string|min:1',
            'last_name'     => 'string|min:1',
            'phone'         => 'string|min:7',
            'email'         => 'nullable|string',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'city'          => 'nullable|string',
            'postcode'      => 'nullable|string',
            'zone'          => 'nullable|string',
            'country'       => 'nullable|string',
            'info'          => 'nullable|json',
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        $object->update($params);

        return new WarehouseLeadResource($object);
    }

    /**
     * Archive a warehouse lead.
     *
     * @urlParam id int required Warehouse lead's id
     *
     * @response {
     *  "message": "Warehouse lead successfully deleted",
     * }
     */
    public function destroy($id, Request $request)
    {
        // TODO: Authorization

        $object = WarehouseLead::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That warehouse lead is archived"
            ], 403);
        }

        $object->archived = 1;
        $object->save();

        return response([
            'message' => 'Warehouse lead successfully deleted',
        ]);
    }

    /**
     * Get lead's public info.
     *
     * @urlParam hash int required Lead's hash
     *
     * @response {
     *  "data": info,
     * }
     */
    public function publicInfo($hash, Request $request)
    {
        // TODO: Authorization

        $lead = WarehouseLead::findByHash($hash);
        if ($lead === null) {
            return response([
                'status' => 'fail',
                'message' => 'Lead not found',
            ], 404);
        }

        if ($lead->archived) {
            return response([
                "message" => "You don't have permissions"
            ], 403);
        }

        $info = $lead->info;
        if ($info === null) {
            $info = [];
        }

        $quantity = null;
        if (isset($info['quantity'])) {
            $quantity = $info['quantity'];
        }

        $total = 0;
        if (isset($info['total'])) {
            $total = $info['total'];
        }

        $product = null;
        if ($this->product_id !== null) {
            $product = [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
            ];
        }

        return response([
            'data' => [
                'id' => $lead->hash,
                'hash' => $lead->hash,
                'first_name' => $lead->first_name,
                'last_name' => $lead->last_name,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'address_line1' => $lead->address_line1,
                'address_line2' => $lead->address_line2,
                'city' => $lead->city,
                'postcode' => $lead->postcode,
                'zone' => $lead->zone,
                'country' => $lead->country,
                'product' => $product,
                'quantity' => $quantity,
                'total' => $total,
                'created_at' => $lead->created_at,
            ],
        ]);
    }

    /**
     * Public update a lead.
     *
     * @urlParam hash int required Lead's hash
     *
     * @bodyParam address_line1 string Warehouse lead's address_line1
     * @bodyParam address_line2 string Warehouse lead's address_line2
     * @bodyParam city string Warehouse lead's city
     * @bodyParam postcode string Warehouse lead's postcode
     * @bodyParam zone string Warehouse lead's zone
     * @bodyParam country string Warehouse lead's country
     *
     * @response {
     *  "status": "success",
     *  "message": "Lead's address updated',
     * }
     */
    public function publicUpdate($hash, Request $request)
    {
        $lead = WarehouseLead::findByHash($hash);
        if ($lead === null) {
            return response([
                'status' => 'fail',
                'message' => 'Lead not found',
            ], 404);
        }

        if ($lead->archived) {
            return response([
                "message" => "You don't have permissions"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'status'        => 'in:' . join(',', WarehouseLead::STATUSES),
            'first_name'    => 'string',
            'last_name'     => 'string',
            'phone'         => 'string',
            'email'         => 'nullable|string',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'city'          => 'nullable|string',
            'postcode'      => 'nullable|string',
            'zone'          => 'nullable|string',
            'country'       => 'nullable|string',
            'info'          => 'nullable|json',
        ]);

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

        // TODO: Is this necessary?
        // TODO: Check time difference now() - lead.created_at

        $lead->update($params);

        return response([
            'status' => 'success',
            'message' => 'Lead successfully updated',
        ]);
    }


    /**
     * Delete a lead.
     *
     * @urlParam hash int required Lead's public hash
     *
     * @response {
     *  "status": "success",
     *  "message": "Lead successfully deleted",
     * }
     */
    public function publicDestroy($hash, Request $request)
    {
        // TODO: Authorization

        $lead = WarehouseLead::findByHash($hash);
        if ($lead === null) {
            return response([
                'status' => 'fail',
                'message' => 'Lead not found',
            ], 404);
        }

        if ($lead->archived) {
            return response([
                "message" => "You don't have permissions"
            ], 403);
        }

        $lead->status = 'cancelled';
        $lead->archived = 1;
        $lead->save();

        return response([
            'status' => 'success',
            'message' => 'Lead successfully cancelled',
        ]);
    }

}
