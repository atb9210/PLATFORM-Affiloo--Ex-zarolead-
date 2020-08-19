<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Flow as Flow;
use App\Http\Resources\api\v1\FlowResource;

class FlowController extends Controller
{
    /**
     * Get all flows list.
     *
     * @response {
     *  "data": [flows],
     * }
     */
    public function index(Request $request)
    {
        $flows = Flow::query();
        if (!Auth::user()->can('viewAny', 'App\Flows')) {
            $flows = $flows->where('user_id', Auth::user()->id);
        }

        return FlowResource::collection($flows->get());
    }

    /**
     * Get flow's informations.
     *
     * @urlParam id int required Flow's id
     *
     * @response {
     *  "data": flow,
     * }
     */
    public function show($id)
    {
        $flow = Flow::findOrFail($id);
        $this->authorize('view', $flow);

        return new FlowResource($flow);
    }

    /**
     * Create a new flow.
     *
     * @bodyParam name string required Flow's name
     * @bodyParam type string required Flow's type (redirect, hotload)
     * @bodyParam rules json required Flow's rules
     * @bodyParam path_name string required Flow's path name
     *
     * @response 201 {
     *  "message": "Flow created",
     *  "data": flow,
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Flow');

        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
            'type' => 'required|in:redirect,hotload',
            'rules' => 'required|json',
            'path_name' => 'required|regex:#^(\w+/){1,2}\w+\.\w+$#',
        ]);
        $params['user_id'] = Auth::user()->id;
        $params['rules'] = json_decode($params['rules'], TRUE);

        // Validate rules JSON
        $validator = Validator::make($params['rules'], [
            'landings' => 'array',
        ]);
        if ($validator->fails()) {
            return response([
                'message' => 'The given data - for the rules JSON param - was invalid.',
                'errors' => $validator->messages(),
            ], 422);
        }

        // Check if path name already exists
        if (file_exists(Flow::buildFullPathname($params['path_name']))) {
            return response([
                'message' => 'The given path name already exists.',
                'errors' => [
                    'path_name' => ['The given path name already exists.'],
                ],
            ], 422);
        }

        // Create flow
        $flow = Flow::create($params);

        // Create file script
        $flow->createFileScript();

        return response([
            "message" => "Flow created",
            "data" => new FlowResource($flow),
        ], 201);
    }

    /**
     * Update the flow's fields.
     *
     * @urlParam id int required Flow's id
     *
     * @bodyParam name string Flow's name
     * @bodyParam type string Flow's type (redirect, hotload)
     * @bodyParam path_name string Flow's path name
     * @bodyParam rules json Flow's rules
     *
     * @response {
     *  "data": flow,
     * }
     */
    public function update(Request $request, $id)
    {
        $flow = Flow::findOrFail($id);
        $this->authorize('update', $flow);

        // Params validation
        $params = $request->validate([
            'name' => 'string|min:3',
            'type' => 'in:redirect,hotload',
            'path_name' => 'regex:#^(\w+/){1,2}\w+\.\w+$#',
            'rules' => 'json',
        ]);
        if (isset($params['rules'])) {
            $params['rules'] = json_decode($params['rules'], TRUE);
        }

        // Validate rules JSON
        if (isset($params['rules'])) {
            $validator = Validator::make($params['rules'], [
                'landings' => 'array',
            ]);
            if ($validator->fails()) {
                return response([
                    'message' => 'The given data - for the rules JSON param - was invalid.',
                    'errors' => $validator->messages(),
                ], 422);
            }
        }

        // Check if path name already exists
        if (isset($params['path_name'])) {
            if (file_exists(Flow::buildFullPathname($params['path_name']))) {
                return response([
                    'message' => 'The given path name already exists.',
                    'errors' => [
                        'path_name' => ['The given path name already exists.'],
                    ],
                ], 422);
            }
        }

        $oldPathName = $flow->path_name;
        $oldType = $flow->type;
        $flow->update($params);

        // Create file script and delete the old one when the path name or the type has changed
        if ((isset($params['path_name']) && $params['path_name'] != $oldPathName) ||
            (isset($params['type']) && $params['type'] != $oldType)) {
            $flow->createFileScript($oldPathName);
        }

        return new FlowResource($flow);
    }

    /**
     * Delete the specified flow.
     *
     * @urlParam id int required Flow's id
     *
     * @response {
     *  "message": "Flow has been deleted",
     * }
     */
    public function destroy($id)
    {
        $flow = Flow::findOrFail($id);

        $this->authorize('delete', $flow);

        $flow->delete();

        return response([
            'message' => 'Flow has been deleted'
        ]);
    }

    /**
     * Get landing's url (randomly picked based on their weights).
     *
     * @urlParam id int required Flow's id
     *
     * @response {
     *  "data": url,
     * }
     */
    public function getLandingUrl($id)
    {
        $flow = Flow::findOrFail($id);

        $data = $flow->getLandingUrl();

        return response([
            'data' => $data
        ]);
    }
}
