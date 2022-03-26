<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\WarehouseShippingCarrier;
use App\Http\Resources\api\v1\WarehouseShippingCarrierResource;

class WarehouseShippingCarrierController extends Controller
{
    /**
     * Get shipping carriers list.
     *
     * @response {
     *  "data": [carriers],
     * }
     */
    public function index(Request $request)
    {
        // TODO: Authorization

        return WarehouseShippingCarrierResource::collection(WarehouseShippingCarrier::all());
    }

    // TODO: HERE
}
