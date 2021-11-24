<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Settings as Settings;

class SettingsController extends Controller
{
    /**
     * Get settings.
     *
     * @response {
     *  "data": [settings],
     * }
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', 'App\Settings');

        $settings = Settings::get();

        return response([
            'data' => $settings,
        ]);
    }

    /**
     * Get settings with a specific key.
     *
     * @urlParam key string required Settings key
     *
     * @response {
     *  "data": [settings],
     * }
     */
    public function show($key)
    {
        $this->authorize('viewAny', 'App\Settings');

        $settings = Settings::getK($key);

        return response([
            'data' => $settings,
        ]);
    }

    /**
     * Update settings.
     *
     * @bodyParam settings json required New settings
     *
     * @response {
     *  "data": [settings],
     * }
     */
    public function update(Request $request)
    {
        $this->authorize('update', Settings::getInstance());

        // Param validation
        $params = $request->validate([
            'settings' => 'required|json',
        ]);
        $params['settings'] = json_decode($params['settings'], true);

        $settings = Settings::set($params['settings']);

        return response([
            'data' => $settings,
        ]);
    }
}
