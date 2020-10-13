<?php

namespace App\Http\Controllers\api\fake;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function worldfilia(Request $request)
    {
        return response([
            'status' => 'success',
        ]);
    }

    public function amsped(Request $request)
    {
        return response([
            'error' => false,
        ]);
    }

    public function affiliationPark(Request $request)
    {
        return response([
            'status' => true,
            'response' => 'Order added',
        ]);
    }
}
