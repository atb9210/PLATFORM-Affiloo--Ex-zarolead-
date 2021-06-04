<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\Ssh\Ssh;

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
            'domain' => 'required|string|unique:domains,domain|regex:' . self::DOMAIN_REGEX,
            'path' => 'required|string|landing_path|unique:domains,path',
        ]);
        
        $domain = null;

        // Create domain's apps (folder and virtualhost) on landings' server with ServerPilot
        $serverpilot = $this->serverpilotGetClient();
        $app = $serverpilot->app_create(
            $params['path'],
            env('SERVERPILOT_LANDINGS_SYSUSER_ID'),
            env('SERVERPILOT_APPS_PHP_VERSION'),
            [$params['domain']],
            []
        );
        // TODO: What about these? Test this code.
        //$serverpilot->ssl_delete($app->data->id);
        //$serverpilot->ssl_auto($app->data->id);
        //$serverpilot->ssl_force($app->data->id, true);

        // Set domain's record on Cloudflare
        $zones = $this->cloudflareGetZones();
        if ($zones !== false) {
            $zone = null;
            foreach ($zones as $z) {
                if ($z['name'] == $params['domain']) {
                    $zone = $z;
                    break;
                }
            }

            if ($zone === null) {
                $zone = $this->cloudflareCreateZone($params['domain']);
            }

            if ($zone !== false && $zone !== null) {
                $dnsRecords = $this->cloudflareGetDNSRecords($zone['id']);
                $dnsRecord = null;
                if ($dnsRecords !== false) {
                    foreach ($dnsRecords as $r) {
                        if ($r['type'] == 'A') {
                            $dnsRecord = $r;
                            break;
                        }
                    }
                }

                if ($dnsRecord !== null) {
                    $this->cloudflareDeleteDNSRecord($zone['id'], $dnsRecord['id']);
                }

                $dnsRecord = $this->cloudflareAddDNSRecord($zone['id'], 'A', $params['domain'], env('SFTP_HOST')); // TODO: What about env('SFTP_HOST')???
                if ($dnsRecord !== false) {
                    $domain = Domain::create($params);
                }
            }
        }

        if($domain === null) {
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
     * @bodyParam domain string Domain
     * @bodyParam path string Domain's path
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
            'domain' => 'string|unique:domains,domain|regex:' . self::DOMAIN_REGEX,
        ]);

        if ($params['domain'] != $domain->domain) {
            // App update on Serverpilot
            $serverpilot = $this->serverpilotGetClient();
            $apps = ($serverpilot->app_list())->data;
            foreach ($apps as $app) {
                if ($app->name == $domain->path) {
                    $serverpilot->app_update($app->id, env('SERVERPILOT_APPS_PHP_VERSION'), [$params['domain']]);
                    break;
                }
            }

            // Update zone on Cloudflare (delete the old one and create a new one)
            $zones = $this->cloudflareGetZones();
            if ($zones !== false) {
                $zone = null;
                foreach ($zones as $z) {
                    if ($z['name'] == $domain->domain) {
                        $this->cloudflareDeleteZone($z['id']);
                    } else if ($z['name'] == $params['domain']) {
                        $zone = $z;
                    }
                }

                if ($zone === null) {
                    $zone = $this->cloudflareCreateZone($params['domain']);
                }

                if ($zone !== false && $zone !== null) {
                    $dnsRecords = $this->cloudflareGetDNSRecords($zone['id']);
                    $dnsRecord = null;
                    if ($dnsRecords !== false) {
                        foreach ($dnsRecords as $r) {
                            if ($r['type'] == 'A') {
                                $dnsRecord = $r;
                                break;
                            }
                        }
                    }

                    if ($dnsRecord !== null) {
                        $this->cloudflareDeleteDNSRecord($zone['id'], $dnsRecord['id']);
                    }

                    $dnsRecord = $this->cloudflareAddDNSRecord($zone['id'], 'A', $params['domain'], env('SFTP_HOST')); // TODO: What about env('SFTP_HOST')???
                    if ($dnsRecord !== false) {
                        $domain->update($params);
                    }
                }
            }
        }

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

        // App deletion on Serverpilot
        $serverpilot = $this->serverpilotGetClient();
        $apps = ($serverpilot->app_list())->data;
        foreach ($apps as $app) {
            if ($app->name == $domain->path) {
                $serverpilot->app_delete($app->id);
                break;
            }
        }

        // Zone deletion on Cloudflare??
        $zones = $this->cloudflareGetZones();
        if ($zones !== false) {
            foreach ($zones as $z) {
                if ($z['name'] == $domain->domain) {
                    $this->cloudflareDeleteZone($z['id']);
                    break;
                }
            }
        }

        // Delete the domain
        $domain->delete();

        return response([
            'message' => 'Domain has been deleted'
        ]);
    }

    /**
     * Get domain's files and directories list
     *
     * @urlParam id int required Domain's id
     *
     * @response {
     *  "data": [content]
     * }
     */
    public function listFilesAndDirectories($id)
    {
        $domain = Domain::findOrFail($id);
        $this->authorize('view', $domain);

        $content = null;
        if ($domain->path != null && $domain->path != '') {
            $content = $this->getDirectoryContent('apps/' . $domain->path);
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

        $content = Storage::disk('landings')->get('apps/' . $params['pathname']);

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

        Storage::disk('landings')->put('apps/' . $params['pathname'], '');

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

        Storage::disk('landings')->makeDirectory('apps/' . $params['pathname']);

        return response([
            'message' => 'Directory created successfully',
        ]);
    }

    /**
     * Save domain's file content
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

        Storage::disk('landings')->putFileAs(
            'apps/' . $params['path'],
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
            Storage::disk('landings')->delete('apps/' . $params['pathname']);
        } else if ($params['type'] == 'directory') {
            $type = 'Directory';
            Storage::disk('landings')->deleteDirectory('apps/' . $params['pathname']);
        }

        return response([
            'message' => $type . ' deleted successfully',
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
     *  "message": "Zarocloner running asynchronously"
     * }
     */
    public function cloneWebpage($id, Request $request)
    {
        $domain = Domain::findOrFail($id);
        $this->authorize('update', $domain);

        // Params validation
        $params = $request->validate([
            'url' => 'required|url',
            'path' => 'regex:/^([A-Za-z0-9_-]+\/*)*$/',
        ]);

        $path = 'apps/' . $domain->path . '/public';
        if (isset($params['path']) && $params['path'] != '') {
            $path .= '/' . $params['path'];
        }
        $this->zaroclonerExecuteAsync($params['url'], $path);

        return response([
            'message' => 'Zarocloner running asynchronously',
        ]);
    }
    
    private function getDirectoryContent($directory)
    {
        $content = [];

        $directoryPathname = $directory;
        if ($directoryPathname[strlen($directoryPathname) - 1] != '/') {
            $directoryPathname .= '/';
        }

        $directories = Storage::disk('landings')->directories($directory);
        foreach ($directories as $dir) {
            $dirContent = $this->getDirectoryContent($dir);
            $content[] = [
                'pathname' => substr($dir, 5),
                'name' => str_replace($directoryPathname, '', $dir),
                'type' => 'directory',
                'data' => $dirContent,
            ];
        }

        $files = Storage::disk('landings')->files($directory);
        foreach ($files as $file) {
            $content[] = [
                'pathname' => substr($file, 5),
                'name' => str_replace($directoryPathname, '', $file),
                'type' => 'file',
                'data' => '',
            ];
        }

        return $content;
    }

    private function serverpilotGetClient()
    {
        $serverpilotConfig = [
            'id' => env('SERVERPILOT_CLIENT_ID'),
            'key' => env('SERVERPILOT_API_KEY'),
        ];
        return new \ServerPilot($serverpilotConfig);
    }

    private function cloudflareGetHeaders()
    {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . env('CLOUDFLARE_API_KEY'),
        ];
        return $headers;
    }

    private function cloudflareGetZones()
    {
        $toReturn = false;

        $url = 'https://api.cloudflare.com/client/v4/zones';
        $headers = $this->cloudflareGetHeaders();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            $toReturn = $response['result'];
        } else {
            Log::error('Cannot get zones list on Cloudflare - RESPONSE: ' . json_encode($response));
        }

        return $toReturn;
    }

    private function cloudflareCreateZone($name)
    {
        $toReturn = false;

        $url = 'https://api.cloudflare.com/client/v4/zones';
        $headers = $this->cloudflareGetHeaders();
        $params = [
            'name' => $name,
            'account' => [
                'id' => env('CLOUDFLARE_ACCOUNT_ID'),
            ],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            $toReturn = $response['result'];
        } else {
            Log::error('Cannot create zone ' . $name . ' on Cloudflare - RESPONSE: ' . json_encode($response));
        }

        return $toReturn;
    }

    private function cloudflareDeleteZone($id)
    {
        $toReturn = false;

        $url = 'https://api.cloudflare.com/client/v4/zones/' . $id;
        $headers = $this->cloudflareGetHeaders();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            $toReturn = true;
        }

        return $toReturn;
    }

    private function cloudflareGetDNSRecords($zone_id)
    {
        $toReturn = false;

        $url = 'https://api.cloudflare.com/client/v4/zones/' . $zone_id . '/dns_records';
        $headers = $this->cloudflareGetHeaders();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            $toReturn = $response['result'];
        }

        return $toReturn;
    }

    private function cloudflareAddDNSRecord($zone_id, $type, $name, $value)
    {
        $toReturn = false;

        $url = 'https://api.cloudflare.com/client/v4/zones/' . $zone_id . '/dns_records';
        $headers = $this->cloudflareGetHeaders();
        $params = [
            'type' => $type,
            'name' => $name,
            'content' => $value,
            'ttl' => 1,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            $toReturn = $response['result'];
        } else {
            Log::error('Cannot create DNS record on Cloudflare for the zone ' . $name . ' - RESPONSE: ' . json_encode($response));
        }

        return $toReturn;
    }

    private function cloudflareDeleteDNSRecord($zone_id, $dns_record_id)
    {
        $toReturn = false;

        $url = 'https://api.cloudflare.com/client/v4/zones/' . $zone_id . '/dns_records/' . $dns_record_id;
        $headers = $this->cloudflareGetHeaders();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            $toReturn = true;
        }

        return $toReturn;
    }

    private function zaroclonerExecuteAsync($url, $path)
    {
        Ssh::create(env('SFTP_USER'), env('SFTP_HOST'))
            ->executeAsync(
                'python3 ' . env('ZAROCLONER_PATH') . '/zarocloner.py ' .
                $url . ' ' .
                $path
            );
    }
}
