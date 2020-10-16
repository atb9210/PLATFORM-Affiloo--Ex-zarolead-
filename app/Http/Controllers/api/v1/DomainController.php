<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Domain as Domain;
use App\Http\Resources\api\v1\DomainResource;

class DomainController extends Controller
{
    /**
     * Get all domains list.
     *
     * @response {
     *  "data": [domains],
     * }
     */
    public function index(Request $request)
    {
        $domains = Domain::query();
        $this->authorize('viewAny', 'App\Domain');
        return DomainResource::collection($domains->get());
    }

    /**
     * Get domain's informations.
     *
     * @urlParam id int required Domain's id
     *
     * @response {
     *  "data": domain,
     * }
     */
    public function show($id)
    {
        $domain = Domain::findOrFail($id);
        $this->authorize('view', $domain);
        return new DomainResource($domain);
    }

    /**
     * Create a new domain.
     *
     * @bodyParam url string required Domain's url
     *
     * @response 201 {
     *  "message": "Domain created",
     *  "data": domain,
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Domain');

        // Params validation
        $params = $request->validate([
            'url' => 'required|url',
        ]);

        // Create domain
        $domain = Domain::create($params);

        return response([
            "message" => "Domain created",
            "data" => new DomainResource($domain),
        ], 201);
    }

    /**
     * Update the domain.
     *
     * @urlParam id int required Domain's id
     *
     * @bodyParam url string Domain's url
     *
     * @response {
     *  "data": domain,
     * }
     */
    public function update(Request $request, $id)
    {
        $domain = Domain::findOrFail($id);
        $this->authorize('update', $domain);


        // Param validation
        $params = $request->validate([
            'url' => 'url',
        ]);

        $domain->update($params);

        return new DomainResource($domain);
    }

    /**
     * Delete the specified domain.
     *
     * @urlParam id int required Domain's id
     *
     * @response {
     *  "message": "Domain has been deleted",
     * }
     */
    public function destroy($id)
    {
        $domain = Domain::findOrFail($id);

        $this->authorize('delete', $domain);

        // Delete the domain
        $domain->delete();

        return response([
            'message' => 'Domain has been deleted'
        ]);
    }
}
