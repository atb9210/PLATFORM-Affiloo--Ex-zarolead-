<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// DEBUG
use Illuminate\Support\Facades\Log;

use Driverfury\Facebook\Facebook;

class FacebookController extends Controller
{
    /**
     * Send event
     *
     * @bodyParam access_token string required Access token // TODO DEBUG
     * @bodyParam test_code string required Test code // TODO DEBUG
     * @bodyParam pixel string required Pixel id
     * @bodyParam event string required Event name
     * @bodyParam event_source_url string Event source URL
     * @bodyParam custom_data json Custom data
     *
     * @response {
     *  "message": "Event has been sent",
     * }
     */
    public function sendEvent(Request $request)
    {
        $events = [
            'AddPaymentInfo',
            'AddToCart',
            'AddToWishlist',
            'CompleteRegistration',
            'Contact',
            'CustomizeProduct',
            'Donate',
            'InitiateCheckout',
            'Lead',
            'PageView',
            'Purchase',
            'Schedule',
            'Search',
            'StartTrial',
            'SubmitApplication',
            'Subscribe',
            'ViewContent',
        ];
        $eventsList = implode(',', $events);

        // Param validation
        $params = $request->validate([
            'access_token' => 'required|string|min:1', // TODO DEBUG
            'test_code' => 'required|string|min:1', // TODO DEBUG
            'pixel' => 'required|string|min:1',
            'event' => 'required|string|min:1|in:' . $eventsList,
            'event_source_url' => 'string',
            'custom_data' => 'json',
        ]);

        // TODO DEBUG: What about token?
        //$token = env('FACEBOOK_API_KEY');
        $token = $params['access_token'];

        // TODO DEBUG
        $testCode = $params['test_code'];

        $pixel = $params['pixel'];
        $event = $params['event'];
        $userData = [
            'client_ip_address' => request()->ip(),
            'client_user_agent' => request()->server('HTTP_USER_AGENT'),
        ];
        $eventSourceUrl = $_SERVER['HTTP_REFERER'];
        if (isset($params['event_source_url'])) {
            $eventSourceUrl = $params['event_source_url'];
        }
        $customData = [];
        if (isset($params['custom_data'])) {
            $customData = json_decode($params['custom_data'], true);
        }

        $fb = new \Driverfury\Facebook\Facebook($token);

        // TODO DEBUG
        $fb->sendEventTest($pixel, $testCode, $event, $userData, $eventSourceUrl, $customData);

        return response([
            'message' => 'Event has been sent',
        ]);
    }
}
