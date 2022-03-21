<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Customer;
use App\Http\Resources\api\v1\CustomerResource;

use App\Http\Resources\api\v1\WarehouseOrderResource;

use App\Settings;

class CustomerController extends Controller
{
    /**
     * Get customers list.
     *
     * @response {
     *  "data": [customers],
     * }
     */
    public function index(Request $request)
    {
        // TODO: Authorization

        $objects = Customer::query();
        $objects = $objects->where('archived', 0);

        return CustomerResource::collection($objects->get());
    }

    /**
     * Get customer's informations.
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

        $object = Customer::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That customer is archived"
            ], 403);
        }

        return new CustomerResource($object);
    }

    /**
     * Create a customer.
     *
     * @bodyParam first_name string required Customer's first name
     * @bodyParam last_name string required Customer's last name
     * @bodyParam phone string required Customer's phone number
     * @bodyParam email string Customer's email
     * @bodyParam address_line1 string Customer's address_line1
     * @bodyParam address_line2 string Customer's address_line2
     * @bodyParam city string Customer's city
     * @bodyParam postcode string Customer's postcode
     * @bodyParam zone string Customer's zone
     * @bodyParam country string Customer's country
     * @bodyParam info json Customer's info
     *
     * @response {
     *  "message": "Customer successfully created",
     *  "data": customer,
     * }
     */
    public function store(Request $request)
    {
        // TODO: Authorization

        // Param validation
        $params = $request->validate([
            'first_name'    => 'required|string|min:1',
            'last_name'     => 'required|string|min:1',
            'phone'         => 'required|string|min:7',
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

        $object = Customer::create($params);

        return response([
            'message' => 'Customer successfully created',
            'data' => new CustomerResource($object),
        ], 201);
    }
    
    /**
     * Update a customer.
     *
     * @urlParam id int required Customer's id
     *
     * @bodyParam first_name string Customer's first name
     * @bodyParam last_name string Customer's last name
     * @bodyParam phone string Customer's phone number
     * @bodyParam email string Customer's email
     * @bodyParam address_line1 string Customer's address_line1
     * @bodyParam address_line2 string Customer's address_line2
     * @bodyParam city string Customer's city
     * @bodyParam postcode string Customer's postcode
     * @bodyParam zone string Customer's zone
     * @bodyParam country string Customer's country
     * @bodyParam info json Customer's info
     *
     * @response {
     *  "data": customer,
     * }
     */
    public function update($id, Request $request)
    {
        // TODO: Authorization

        $object = Customer::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That customer is archived"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
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

        return new CustomerResource($object);
    }

    /**
     * Archive a customer.
     *
     * @urlParam id int required Customer's id
     *
     * @response {
     *  "message": "Customer successfully deleted",
     * }
     */
    public function destroy($id, Request $request)
    {
        // TODO: Authorization

        $object = Customer::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That customer is archived"
            ], 403);
        }

        $object->archived = 1;
        $object->save();

        return response([
            'message' => 'Customer successfully deleted',
        ]);
    }

    /**
     * Get customer's orders.
     *
     * @urlParam id int required Customer's id
     *
     * @response {
     *  "data": [orders],
     * }
     */
    public function orders($id, Request $request)
    {
        // TODO: Authorization

        $customer = Customer::findOrFail($id);
        if ($customer->archived) {
            return response([
                "message" => "That customer is archived"
            ], 403);
        }

        return WarehouseOrderResource::collection($customer->orders);
    }

    private function getChatAPIClient()
    {
        $settings = Settings::get();
        $apiInstance = env('CHATAPI_INSTANCE', '');
        $apiToken = env('CHATAPI_TOKEN', '');
        if (isset($settings['chatapi']['api_instance'])) {
            $apiInstance = $settings['chatapi']['api_instance'];
        }
        if (isset($settings['chatapi']['api_token'])) {
            $apiToken = $settings['chatapi']['api_token'];
        }
        $chatapi = new \Driverfury\ChatAPI\ChatAPI($apiInstance, $apiToken);
        return $chatapi;
    }

    /**
     * Get whatsapp messages to and from the customer.
     *
     * @urlParam id int required Customer's id
     *
     * @response {
     *  "data": [messages],
     * }
     */
    public function getMessages($id)
    {
        // TODO: Authorization

        $object = Customer::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That customer is archived"
            ], 403);
        }

        $chatapi = $this->getChatAPIClient();
        $messages = $chatapi->getMessages($object->phone);

        return response([
            'data' => $messages,
        ]);
    }

    /**
     * Send message
     *
     * @urlParam id int required Customer's id
     *
     * @bodyParam message required string Message
     */
    public function sendMessage($id, Request $request)
    {
        // TODO: Authorization

        $object = Customer::findOrFail($id);
        if ($object->archived) {
            return response([
                "message" => "That customer is archived"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'message' => 'required|string',
        ]);

        $chatapi = $this->getChatAPIClient();
        $object = $chatapi->sendMessage($object->phone, $params['message']);

        if ($object === null) {
            return response([
                'status' => 'fail',
                'message' => 'Message not sent',
            ], 500);
        }

        return response([
            'status' => 'success',
            'data' => $object,
            'message' => 'Message sent',
        ]);
    }

}
