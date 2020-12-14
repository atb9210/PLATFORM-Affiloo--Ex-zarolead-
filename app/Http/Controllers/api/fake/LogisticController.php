<?php

namespace App\Http\Controllers\api\fake;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogisticController extends Controller
{
    public function momoka(Request $request)
    {
        return response([
            'success' => 'true',
            'error' => 'false',
        ]);
    }

    public function momokaFindOrder($id)
    {
        return response([
            'success' => 'true',
            'error' => 'false',
            'status' => 2,
        ]);
    }
}
