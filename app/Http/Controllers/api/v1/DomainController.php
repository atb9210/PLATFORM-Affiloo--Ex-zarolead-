<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Domain as Domain;
use App\Http\Resources\api\v1\DomainResource;

class DomainController extends Controller
{
    private const DOMAIN_REGEX = '/^([a-zA-Z0-9]{1,61}\.)+[a-zA-Z]{2,}$/';

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
        if (!Auth::user()->can('viewAny', 'App\Domain')) {
            $domains = $domains->where('user_id', Auth::user()->id);
        }
        $domains = $domains->where('archived', 0);

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

        if ($domain->archived) {
            return response([
                "message" => "That domain is archived"
            ], 403);
        }

        return new DomainResource($domain);
    }

    /**
     * Create a new domain.
     *
     * @bodyParam user_id int User's id
     * @bodyParam domain string required Domain
     * @bodyParam path string required Domain's path
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
            'user_id' => 'integer|exists:users,id',
            'domain' => 'required|string|unique:domains,domain|regex:' . self::DOMAIN_REGEX,
            'path' => 'required|string|landing_path|unique:domains,path',
        ]);

        if ((!Auth::user()->can('changeUser', 'App\Domain')) || !isset($params['user_id'])) {
            $params['user_id'] = Auth::user()->id;
        }

        // Check if the domain is registered
        if (!Domain::isRegistered($params['domain'])) {
            return response([
                "message" => $params['domain'] . " is not a registered domain",
                "errors" => [
                    "domain" => [ "This is not a registered domain" ],
                ],
            ], 422);
        }

        // Create domain
        $params['info'] = [];
        $domain = Domain::create($params);
        $result = $domain->createAppAndSetDns($params['path']);

        if ($result === false) {
            $domain->delete();
            return response([
                "message" => "Unexpected error: domain cannot be created!",
            ], 500);
        }

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
     * @bodyParam user_id int User's id
     * @bodyParam domain string Domain
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
            'user_id' => 'integer|exists:users,id',
            'domain' => 'string|regex:' . self::DOMAIN_REGEX,
            'info' => 'json',
        ]);

        if (isset($params['user_id']) && !Auth::user()->can('changeUser', 'App\Domain')) {
            unset($params['user_id']);
        }

        // Check if the domain has already been taken
        if (isset($params['domain']) && $params['domain'] != $domain->domain) {
            if (Domain::where('domain', $params['domain'])->count() > 0) {
                return response([
                    "message" => $params['domain'] . " has already been taken",
                    "errors" => [
                        "domain" => [ "The domain has already been taken." ],
                    ],
                ], 422);
            }
        }

        // Check if the domain is registered
        if (isset($params['domain'])) {
            if (!Domain::isRegistered($params['domain'])) {
                return response([
                    "message" => $params['domain'] . " is not a registered domain",
                    "errors" => [
                        "domain" => [ "This is not a registered domain" ],
                    ],
                ], 422);
            }

            if ($params['domain'] != $domain->domain) {
                $domain->change($params['domain']);
            }
        }

        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

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
        $domain->deleteAppAndZone();

        // TODO: Delete or archive?
        $domain->delete();
        //$domain->archived = 1;
        //$domain->save();

        return response([
            'message' => 'Domain has been deleted'
        ]);
    }

    /**
     * Enable SSL for the specified domain.
     *
     * @urlParam id int required Domain's id
     *
     * @response {
     *  "message": "SSL has been activated",
     * }
     */
    public function activateSSL($id)
    {
        $domain = Domain::findOrFail($id);
        $this->authorize('update', $domain);

        // Try to activate SSL for the domain
        $result = $domain->activateSSL();
        if ($result) {
            return response([
                'message' => 'SSL has been activated'
            ]);
        } else {
            return response([
                'message' => 'SSL cannot be activated'
            ], 400);
        }
    }

    /**
     * Get domain's files and directories list
     *
     * @urlParam id int required Domain's id
     *
     * @bodyParam path string required Path
     *
     * @response {
     *  "data": [content]
     * }
     */
    public function listFilesAndDirectories($id, Request $request)
    {
        $domain = Domain::findOrFail($id);
        $this->authorize('view', $domain);

        // Params validation
        $params = $request->validate([
            'pathname' => 'nullable|string|min:0',
        ]);
        $pathname = '';
        if (isset($params['pathname'])) {
            $pathname = $params['pathname'];
        }

        $content = null;
        if ($domain->path != null && $domain->path != '') {
            $content = $domain->getDirectoryContent($pathname);
        }

        return response([
            'data' => $content,
        ]);
    }

    /**
     * Get domain's file content
     *
     * @urlParam id int required Domain's id
     *
     * @bodyParam pathname string required File's pathname
     *
     * @response {
     *  "data": content
     * }
     */
    public function getFileContent($id, Request $request)
    {
        $domain = Domain::findOrFail($id);
        $this->authorize('view', $domain);

        // Params validation
        $params = $request->validate([
            'pathname' => 'required|string|min:1',
        ]);

        // TODO: Check if pathname contains (at the beginning) $domain->path . '/'

        $content = $domain->getFileContent($params['pathname']);

        return response([
            'data' => $content,
        ]);
    }

    /**
     * Create domain's file
     *
     * @urlParam id int required Domain's id
     *
     * @bodyParam pathname string required File's pathname
     *
     * @response {
     *  "message": "File created successfully"
     * }
     */
    public function createFile($id, Request $request)
    {
        $domain = Domain::findOrFail($id);
        $this->authorize('update', $domain);

        // Params validation
        $params = $request->validate([
            'pathname' => 'required|string|min:1',
        ]);

        // TODO: Check if pathname contains (at the beginning) $domain->path . '/'

        $domain->createFile($params['pathname'], '');

        return response([
            'message' => 'File created successfully',
        ]);
    }

    /**
     * Create domain's directory
     *
     * @urlParam id int required Domain's id
     *
     * @bodyParam pathname string required Directory's pathname
     *
     * @response {
     *  "message": "Directory created successfully"
     * }
     */
    public function createDirectory($id, Request $request)
    {
        $domain = Domain::findOrFail($id);
        $this->authorize('update', $domain);

        // Params validation
        $params = $request->validate([
            'pathname' => 'required|string|min:1',
        ]);

        // TODO: Check if pathname contains (at the beginning) $domain->path . '/'

        $domain->createDirectory($params['pathname']);

        return response([
            'message' => 'Directory created successfully',
        ]);
    }

    /**
     * Upload a file
     *
     * @urlParam id int required Domain's id
     *
     * @bodyParam path string required File's path
     * @bodyParam file string required File's file to upload
     *
     * @response {
     *  "message": "File saved successfully"
     * }
     */
    public function uploadFile($id, Request $request)
    {
        $domain = Domain::findOrFail($id);
        $this->authorize('update', $domain);

        // Params validation
        $params = $request->validate([
            'path' => 'required|string|min:1',
            'file' => 'required|file',
        ]);

        // TODO: Check if path contains (at the beginning) $domain->path . '/'

        $domain->uploadFile(
            $params['path'],
            $request->file('file'),
            $request->file('file')->getClientOriginalName()
        );

        return response([
            'message' => 'File saved successfully',
        ]);
    }

    /**
     * Delete domain's file or directory
     *
     * @urlParam id int required Domain's id
     *
     * @bodyParam type string required Is it a file or a directory?
     * @bodyParam pathname string required Pathname
     *
     * @response {
     *  "message": "File saved successfully"
     * }
     */
    public function deleteFileOrDirectory($id, Request $request)
    {
        $domain = Domain::findOrFail($id);
        $this->authorize('update', $domain);

        // Params validation
        $params = $request->validate([
            'type' => 'required|string|in:file,directory',
            'pathname' => 'required|string|min:1',
        ]);

        // TODO: Check if path contains (at the beginning) $domain->path . '/'

        $type = '';
        if ($params['type'] == 'file') {
            $type = 'File';
            $domain->deleteFile($params['pathname']);
        } else if ($params['type'] == 'directory') {
            $type = 'Directory';
            $domain->deleteDirectory($params['pathname']);
        }

        return response([
            'message' => $type . ' deleted successfully',
        ]);
    }

    /**
     * Add DNS record
     *
     * @urlParam id int required Domain's id
     *
     * @bodyParam type string required Is it a file or a directory?
     * @bodyParam pathname string required Pathname
     *
     * @response {
     *  "message": "File saved successfully"
     * }
     */
    public function addDNSRecord($id, Request $request)
    {
        $domain = Domain::findOrFail($id);
        $this->authorize('update', $domain);

        // Params validation
        $params = $request->validate([
            'type' => 'required|string|min:1',
            'name' => 'required|string|min:1',
            'content' => 'required|string|min:1',
        ]);

        if ($domain->addDNSRecord($params['type'], $params['name'], $params['content']) === false) {
            return response([
                'message' => 'Cannot add DNS record',
            ], 400);
        }

        return response([
            'message' => $params['type'] . ' record added successfully',
            'data' => new DomainResource($domain),
        ]);
    }

    /**
     * Clone webpage
     *
     * @urlParam id int required Domain's id
     *
     * @bodyParam url string required Webpage's URL to copy
     * @bodyParam path string Target path
     *
     * @response {
     *  "message": "ZaroCloner running asynchronously"
     * }
     */
    public function cloneWebpage($id, Request $request)
    {
        $domain = Domain::findOrFail($id);
        $this->authorize('update', $domain);

        // Params validation
        $params = $request->validate([
            'url' => 'required|url',
            'path' => 'nullable|regex:/^([A-Za-z0-9_-]*\/*)*$/',
        ]);

        $path = 'apps/' . $domain->path . '/public';
        if (isset($params['path']) && $params['path'] != '') {
            $path .= '/' . $params['path'];
        }
        $zaroCloner = new \Driverfury\ZaroCloner\ZaroCloner();
        $jobId = $zaroCloner->executeAsync($params['url'], $path);

        return response([
            'message' => 'ZaroCloner running asynchronously',
            'job' => [
                'id' => $jobId,
                'name' => 'ZaroCloner ' . $params['path'],
                'created_at' => date('YYYY-mm-dd h:i:s'),
                'status' => 'queued',
            ],
        ]);
    }

    /**
     * Get namecheap domain list
     *
     * @response {
     *  "data": [domains]
     * }
     */
    public function getNamecheapDomains()
    {
        $this->authorize('viewAny', 'App\Domain');
        $this->authorize('create', 'App\Domain');

        $domains = Domain::namecheapGetDomains();

        return response([
            'data' => $domains,
        ]);
    }

    /**
     * Get job status
     *
     * @response {
     *  "data": [domains]
     * }
     */
    public function getJob($id)
    {
        // TODO: Move this function into driverfury/zarocloner package or not???
        $this->authorize('viewAny', 'App\Domain');
        $this->authorize('create', 'App\Domain');

        $status = 'failed';
        $jobStatuses = DB::table('job_statuses')->where('job_id', $id)->get();
        if (count($jobStatuses) > 0) {
            $status = $jobStatuses->first()->status;
        }

        return response([
            'data' => [
                'id' => $id,
                'name' => 'Job ZaroCloner',
                'status' => $status,
            ],
        ]);
    }
}
