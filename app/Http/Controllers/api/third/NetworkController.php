<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    /**
     * Store order in internal network
     *
     * // TODO: Docs
     * @bodyParam param type [required] Description
     *
     * @response {
     *  "data": order,
     * }
     */
    public function store(Request $request)
    {
        // TODO: to develop
        return response([
            'status' => 'error',
        ], 500);
    }
}
