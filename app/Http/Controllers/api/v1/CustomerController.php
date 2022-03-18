<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// TODO: Use customer and customer resource

class CustomerController extends Controller
{
    /**
     * Get all customers list.
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

    // TODO: Other functions (show, store, update, delete)
}
