<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Driverfury\Facebook\Facebook;

class FacebookController extends Controller
{
    /**
     * Send event
     *
     * @bodyParam pixel string required Pixel id
     * @bodyParam event string required Event name
     * @bodyParam fbc string Facebook click id
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
            'pixel' => 'required|string|min:1',
            'event' => 'required|string|min:1|in:' . $eventsList,
            'fbc' => 'required|string',
            'custom_data' => 'json',
        ]);

        $token = env('FACEBOOK_API_KEY');
        $pixel = $params['pixel'];
        $event = $params['event'];
        $clickId = $params['fbc'];
        $customData = [];
        if (isset($params['custom_data'])) {
            $customData = json_decode($params['custom_data'], true);
        }

        $fb = new \Driverfury\Facebook\Facebook($token);
        $fb->sendEvent($pixel, $event, $clickId, $customData);

        // TODO: Log when event is not sent
        Log::channel('postback')->info('Postback request received');

        return response([
            'message' => 'Event has been sent',
        ]);
    }
}
