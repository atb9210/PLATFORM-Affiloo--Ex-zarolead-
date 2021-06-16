<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Flow as Flow;
use App\Http\Resources\api\v1\FlowResource;

use App\Domain as Domain;
use App\Landing as Landing;

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
        if (!Auth::user()->can('viewAny', 'App\Flow')) {
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
     * @bodyParam domain_id int required Domain's id
     * @bodyParam path_name string required Flow's path name
     * @bodyParam traffic_source_id int required Traffic source's id
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
            'domain_id' => 'required|exists:domains,id',
            'path_name' => 'required|regex:#^(\w+/){1,2}\w+\.\w+$#',
            'traffic_source_id' => 'required|integer|exists:traffic_sources,id',
        ]);
        $params['user_id'] = Auth::user()->id;
        $params['rules'] = json_decode($params['rules'], TRUE);

        // Validate rules JSON
        $validator = Validator::make($params['rules'], [
            'landings' => 'required|array',
            'safe_page_url' => 'string',
            'fraud_filter_protection' => 'boolean',
            'fraud_filter_campaign_id' => 'string',
            'fraud_filter_campaign_key' => 'string',
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

        $jobsArray = [];

        // Create file script
        $type = $params['type'];
        if ($type == 'redirect') {
            $flow->createFileScript();
        } else if ($type == 'hotload') {
            $domain = Domain::findOrFail($params['domain_id']);

            // Import landings if URL is external
            $landings = $params['rules']['landings'];
            $counter = 0;
            foreach ($landings as $landing) {
                $l = Landing::findOrFail($landing['id']);
                $url = $l->url;
                $path = Str::slug($url, '-');
                $fullPath = 'apps/' . $domain->path . '/public/' . $path;

                if ($flow->urlIsExternal($url)) {
                    $zaroCloner = new \Driverfury\ZaroCloner\ZaroCloner();
                    $jobsArray[] =  [
                        'id' => $zaroCloner->executeAsync($url, $fullPath),
                        'path' => $path,
                    ];
                    $params['rules']['landings'][$counter]['internal_url'] = $domain->getUrlFromPath($path);
                }
                $counter += 1;
            }

            // Import safe page if URL is external
            if (isset($params['rules']['safe_page_url']) &&
                $params['rules']['safe_page_url'] != '') {
                $url = $params['rules']['safe_page_url'];
                $path = Str::slug($url, '-');
                $fullPath = 'apps/' . $domain->path . '/public/' . $path;
                if ($flow->urlIsExternal($url)) {
                    $zaroCloner = new \Driverfury\ZaroCloner\ZaroCloner();
                    $jobsArray[] =  [
                        'id' => $zaroCloner->executeAsync($url, $fullPath),
                        'path' => $path,
                    ];
                    $params['rules']['safe_page_url'] = $domain->getUrlFromPath($path);
                }
            }

            $flow->rules = $params['rules'];
            $flow->save();

            $flow->createHotloadScript();
        }

        $jobs = [];
        foreach ($jobsArray as $job) {
            $jobs[] = [
                'id' => $job['id'],
                'name' => 'ZaroCloner ' . $job['path'],
                'created_at' => date('YYYY-mm-dd h:i:s'),
                'status' => 'queued',
            ];
        }

        return response([
            "message" => "Flow created",
            "data" => new FlowResource($flow),
            "jobs" => $jobs,
        ], 201);
    }

    /**
     * Update the flow's fields.
     *
     * @urlParam id int required Flow's id
     *
     * @bodyParam name string Flow's name
     * @bodyParam traffic_source_id int Traffic source's id
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
            'traffic_source_id' => 'integer|exists:traffic_sources,id',
            'rules' => 'json',
        ]);
        if (isset($params['rules'])) {
            $params['rules'] = json_decode($params['rules'], TRUE);
        }

        // Validate rules JSON
        if (isset($params['rules'])) {
            $validator = Validator::make($params['rules'], [
                'landings' => 'required|array',
            ]);
            if ($validator->fails()) {
                return response([
                    'message' => 'The given data - for the rules JSON param - was invalid.',
                    'errors' => $validator->messages(),
                ], 422);
            }
        }

        // TODO: Is this still useful?
        //// Check if path name already exists
        //if (isset($params['path_name'])) {
        //    if ($flow->path_name != $params['path_name'] && file_exists(Flow::buildFullPathname($params['path_name']))) {
        //        return response([
        //            'message' => 'The given path name already exists.',
        //            'errors' => [
        //                'path_name' => ['The given path name already exists.'],
        //            ],
        //        ], 422);
        //    }
        //}

        $jobsArray = [];

        // TODO: This should be in Flow.php file?????
        if ($flow->type == 'hotload') {
            $oldLandings = [];
            $oldSafePage = '';
            $newLandings = [];
            $newSafePage = '';

            if (isset($flow->rules['landings'])) {
                foreach ($flow->rules['landings'] as $landing) {
                    $oldLandings[] = $landing['id'];
                }
            }
            if (isset($flow->rules['safe_page_url'])) {
                $oldSafePage = $flow->rules['safe_page_url'];
            }

            if (isset($params['rules']['landings'])) {
                foreach ($params['rules']['landings'] as $landing) {
                    $newLandings[] = $landing['id'];
                }
            }
            if (isset($params['rules']['safe_page_url'])) {
                $newSafePage = $params['rules']['safe_page_url'];
            }

            $removedLandings = [];
            $addedLandings = [];
            foreach ($oldLandings as $oldLanding) {
                if (!in_array($oldLanding, $newLandings)) {
                    $removedLandings[] = $oldLanding;
                }
            }
            foreach ($newLandings as $newLanding) {
                if (!in_array($newLanding, $oldLandings)) {
                    $addedLandings[] = $newLanding;
                }
            }

            // Remove landings
            if (isset($flow->rules['landings'])) {
                foreach ($flow->rules['landings'] as $landing) {
                    if (in_array($landing['id'], $removedLandings)) {
                        $url = $landing['internal_url'];
                        $domain = Domain::getFromUrl($url);
                        $pathToDelete = $domain->getDirectoryFromUrl($url);
                        $domain->deleteDirectory($pathToDelete);
                    }
                }
            }

            // Add landings
            if (count($addedLandings) > 0) {
                $counter = 0;
                if (isset($flow->rules['landings'])) {
                    foreach ($params['rules']['landings'] as $landing) {
                        foreach ($addedLandings as $landingId) {
                            if ($landing['id'] == $landingId) {
                                $l = Landing::findOrFail($landingId);
                                $url = $l->url;
                                $path = Str::slug($url, '-');
                                $domain = $flow->domain;
                                $fullPath = 'apps/' . $domain->path . '/public/' . $path;

                                if ($flow->urlIsExternal($url)) {
                                    $zaroCloner = new \Driverfury\ZaroCloner\ZaroCloner();
                                    $jobsArray[] =  [
                                        'id' => $zaroCloner->executeAsync($url, $fullPath),
                                        'path' => $path,
                                    ];
                                    $params['rules']['landings'][$counter]['internal_url'] = $domain->getUrlFromPath($path);
                                }
                                break;
                            }
                        }
                        $counter += 1;
                    }
                }
            }

            // Get internal URLs
            if (isset($flow->rules['landings'])) {
                $counter = 0;
                foreach ($params['rules']['landings'] as $newLanding) {
                    foreach ($flow->rules['landings'] as $oldLanding) {
                        if ($newLanding['id'] == $oldLanding['id']) {
                            if (isset($oldLanding['internal_url']) &&
                                !isset($newLanding['internal_url'])) {
                                $params['rules']['landings'][$counter]['internal_url'] = $oldLanding['internal_url'];
                            }
                        }
                    }
                    $counter += 1;
                }
            }

            // Change\delete\create safe page
            $oldSafePage = '';
            if (isset($flow->rules['safe_page_url'])) {
                $oldSafePage = $flow->rules['safe_page_url'];
            }
            $newSafePage = '';
            if (isset($params['rules']['safe_page_url'])) {
                $newSafePage = $params['rules']['safe_page_url'];
            }
            if ($newSafePage != $oldSafePage) {
                if ($oldSafePage != '') {
                    // Delete the old safe page
                    $directory = $flow->domain->getDirectoryFromUrl($oldSafePage);
                    $flow->domain->deleteDirectory($directory);
                }
                if ($newSafePage != '') {
                    // Create the new safe page
                    $url = $newSafePage;
                    $path = Str::slug($url, '-');
                    $domain = $flow->domain;
                    $fullPath = 'apps/' . $domain->path . '/public/' . $path;
                    if ($flow->urlIsExternal($url)) {
                        $zaroCloner = new \Driverfury\ZaroCloner\ZaroCloner();
                        $jobsArray[] =  [
                            'id' => $zaroCloner->executeAsync($url, $fullPath),
                            'path' => $path,
                        ];
                        $params['rules']['safe_page_url'] = $domain->getUrlFromPath($path);
                    }
                }
            }
        }

        $flow->update($params);

        // TODO: Is this still useful?
        //// Create file script and delete the old one when the path name or the type has changed
        //if ((isset($params['path_name']) && $params['path_name'] != $oldPathName) ||
        //    (isset($params['type']) && $params['type'] != $oldType)) {
        //    $flow->createFileScript($oldPathName);
        //}
        
        $jobs = [];
        foreach ($jobsArray as $job) {
            $jobs[] = [
                'id' => $job['id'],
                'name' => 'ZaroCloner ' . $job['path'],
                'created_at' => date('YYYY-mm-dd h:i:s'),
                'status' => 'queued',
            ];
        }

        return response([
            "message" => "Flow updated",
            "data" => new FlowResource($flow),
            "jobs" => $jobs,
        ], 201);
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

    /**
     * Hotload
     *
     * @urlParam id int required Flow's id
     *
     * @response {
     *  "data": url,
     * }
     */
    public function hotload($id, Request $request)
    {
        $flow = Flow::findOrFail($id);

        // FraudFilter protection
        if ($flow->isFraudFilterProtectionEnabled()) {
            $campaignId = $flow->fraudFilterCampaignId();
            $campaignKey = $flow->fraudFilterCampaignKey();
            $fraudFilter = new \Driverfury\FraudFilter\FraudFilter();
            if ($fraudFilter->check($campaignId, $campaignKey)) {
                // Money page
                $page_url = $flow->getLandingUrl();
            } else {
                // Safe page
                $page_url = $flow->getSafePageUrl();
            }
        } else {
            $page_url = $flow->getLandingUrl();
        }

        $html = file_get_contents($page_url);

        $parsed_url = parse_url($page_url);
        $base = $page_url;
        if (strpos($parsed_url['path'], '.') === false && $base[strlen($base) - 1] != '/') {
            $base .= '/';
        }

        $html = str_replace('<head>', '<head><base href="' . $base . '">', $html);

        return response([
            'succes' => true,
            'html' => $html
        ]);
    }
}
