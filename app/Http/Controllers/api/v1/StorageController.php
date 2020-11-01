<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorageController extends Controller
{
    /**
     * Get storage info.
     *
     * @response {
     *  "data": {
     *    "free_space": X in MB,
     *    "total_space": X in MB,
     * }
     */
    public function index()
    {
        if (!Auth::user()->hasPermission('storage', 'view')) {
            return response([
                "message" => "You don't have permissions to access storage info"
            ], 403);
        }

        return response([
            'data' => [
                'free_space' => (int)(disk_free_space(env('APP_PATH'))/(1024*1024)), // in MB
                'total_space' => (int)(disk_total_space(env('APP_PATH'))/(1024*1024)), // in MB
            ],
        ]);
    }
}
