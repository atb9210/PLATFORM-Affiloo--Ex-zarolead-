<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Landing as Landing;
use App\LandingStats as LandingStats;
use App\Form as Form;
use App\Http\Resources\api\v1\LandingResource;
use App\Http\Resources\api\v1\LandingStatsResource;

class LandingController extends Controller
{
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

    /**
     * Get all landings list.
     *
     * @urlParam filter_product int Order's product id
     *
     * @response {
     *  "data": [landings],
     * }
     */
    public function index(Request $request)
    {
        // Param validation
        $params = $request->validate([
            'filter_product' => 'integer|exists:products,id',
        ]);

        $landings = Landing::query();
        $landings = $landings->where('archived', 0);
        if (!Auth::user()->can('viewAny', 'App\Landing')) {
            $landings = $landings->where('user_id', Auth::user()->id);
        }

        if (isset($params['filter_product'])) {
            $landings = $landings->where('product_id', $params['filter_product']);
        }

        return LandingResource::collection($landings->get());
    }

    /**
     * Get landing's informations.
     *
     * @urlParam id int required Landing's id
     *
     * @response {
     *  "data": landing,
     * }
     */
    public function show($id)
    {
        $landing = Landing::findOrFail($id);
        $this->authorize('view', $landing);

        if ($landing->archived) {
            return response([
                "message" => "That landing is archived"
            ], 403);
        }

        return new LandingResource($landing);
    }

    /**
     * Create a new landing.
     *
     * @bodyParam name string required Landing's name
     * @bodyParam path string Landing's path
     * @bodyParam product_id int required Landing's product id
     * @bodyParam url string required Landing's URL
     * @bodyParam country string Country
     *
     * @response 201 {
     *  "message": "Landing created",
     *  "data": [landing],
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Landing');

        // Params validation
        $params = $request->validate([
            'name' => 'required|string|min:3',
            'path' => 'string|landing_path|unique:landings,path',
            'product_id' => 'required|integer|exists:products,id',
            'url' => 'required|url',
            'country' => 'string|min:1',
        ]);
        $params['user_id'] = Auth::user()->id;

        $path = '';
        if (!isset($params['path'])) {
            $params['path'] = Str::slug($params['name'], '-');
            $path = $params['path'];
        }

        $domain = parse_url($params['url'])['host'];

        // TODO: Check if path already exists (change it if it already exists?)

        // Create landing's apps (folder and virtualhost) on landings' server with ServerPilot
        $serverpilot = $this->serverpilotGetClient();
        $app = $serverpilot->app_create(
            $path,
            env('SERVERPILOT_LANDINGS_SYSUSER_ID'),
            'php7.4',
            [$domain],
            []
        );
        //$serverpilot->ssl_auto($app->data->id);
        //$serverpilot->ssl_force($app->data->id, true);

        // Set domain's record on CloudFlare
        $zones = $this->cloudflareGetZones();
        if ($zones !== false) {
            $zone = null;
            foreach ($zones as $z) {
                if ($z['name'] == $domain) {
                    $zone = $z;
                    break;
                }
            }

            if ($zone === null) {
                $zone = $this->cloudflareCreateZone($domain);
            }

            if ($zone === null || $zone === false) {
            }

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

            $dnsRecord = $this->cloudflareAddDNSRecord($zone['id'], 'A', $domain, env('SFTP_HOST')); // TODO: What about env('SFTP_HOST')???

            if ($dnsRecord !== false) {
                $landing = Landing::create($params);
            } else {
                // TODO: Log error?
            }
        } else {
            // TODO: Log error?
        }


        return response([
            "message" => "Landing created",
            "data" => new LandingResource($landing),
        ], 201);
    }

    /**
     * Update the landing's fields.
     *
     * @urlParam id int required Landing's id
     *
     * @bodyParam name string Landing's name
     * @bodyParam path string Landing's path
     * @bodyParam product_id int Landing's product id
     * @bodyParam url string Landing's URL
     * @bodyParam country string Country
     *
     * @response {
     *  "data": landing,
     * }
     */
    public function update(Request $request, $id)
    {
        $landing = Landing::findOrFail($id);
        $this->authorize('update', $landing);

        if ($landing->archived) {
            return response([
                "message" => "That landing is archived"
            ], 403);
        }

        // Param validation
        $params = $request->validate([
            'name' => 'string|min:3',
            'path' => 'string|landing_path|unique:landings,path',
            'product_id' => 'integer|exists:products,id',
            'url' => 'url',
            'country' => 'string|min:1',
        ]);
        
        if (isset($params['path']) && $params['path'] == '') {
            $params['path'] = Str::slug($params['name'], '-');
        }

        // TODO: Rename landing's folder if path is changed or create if path
        // goes from null (or '') to something

        $landing->update($params);

        return new LandingResource($landing);
    }

    /**
     * Delete the specified landing.
     *
     * @urlParam id int required Landing's id
     *
     * @response {
     *  "message": "Landing has been archived",
     * }
     */
    public function destroy($id)
    {
        $landing = Landing::findOrFail($id);

        $this->authorize('delete', $landing);

        // Archive the landing
        $landing->archived = 1;
        $landing->save();

        // TODO: What about landing's folder (delete it or keep it?)

        return response([
            'message' => 'Landing has been archived'
        ]);
    }

    /**
     * Get landing stats.
     *
     * @urlParam id int required Landing's id
     *
     * @bodyParam filter_date_from string From date (Y-m-d format)
     * @bodyParam filter_date_to string To date (Y-m-d format)
     *
     * @response {
     *  "data": [stats]
     * }
     */
    public function stats($id, Request $request)
    {
        $landing = Landing::findOrFail($id);
        $this->authorize('view', $landing);

        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $stats = LandingStats::where('landing_id', $id);
        if (isset($params['filter_date_from'])) {
            $stats = $stats->where('day', '>=', $params['filter_date_from']);
        }
        if (isset($params['filter_date_to'])) {
            $stats = $stats->where('day', '<=', $params['filter_date_to']);
        }
        $stats = $stats->orderBy('day', 'desc');

        return LandingStatsResource::collection($stats->get());
    }

    /**
     * Get form code
     *
     * @urlParam default_form int Default form's id
     *
     * @response {
     *  "data": code
     * }
     */
    public function code($id, Request $request)
    {
        // Param validation
        $params = $request->validate([
            'default_form' => 'required|integer|exists:forms,id',
        ]);
        $default_form = $params['default_form'];

        $landing = Landing::findOrFail($id);
        $this->authorize('view', $landing);

        /*
        $form_html  = '<iframe scrolling="no" id="buy-frame" width="100%" height="500px" src="' . $form_url . '"></iframe>' . PHP_EOL;
        $form_html .= "<script type=\"text/javascript\">" . PHP_EOL;
        $form_html .= "document.addEventListener(\"DOMContentLoaded\", function(event) {" . PHP_EOL;
        $form_html .= "var loc = window.location.toString(), params = '', iframe_id = 'buy-frame';" . PHP_EOL;
        $form_html .= "if (loc.indexOf('?') > -1) {" . PHP_EOL;
        $form_html .= "params = loc.split('?')[1]," . PHP_EOL;
        $form_html .= "iframe = document.getElementById(iframe_id);" . PHP_EOL;
        $form_html .= "iframe_src_url = iframe.src;" . PHP_EOL;
        $form_html .= "separator = '?';" . PHP_EOL;
        $form_html .= "if (iframe_src_url.indexOf('?') > -1) {" . PHP_EOL;
        $form_html .= "separator = '&';" . PHP_EOL;
        $form_html .= "}" . PHP_EOL;
        $form_html .= "iframe.src = iframe_src_url + separator + params;" . PHP_EOL;
        $form_html .= "}" . PHP_EOL;
        $form_html .= "});" . PHP_EOL;
        $form_html .= "</script>" . PHP_EOL;
        */

        $form_url = "";
        if(env('APP_LOCAL', false)) {
            $form_url = "http://zarolead.localhost";
        } else if(env('APP_ALPHA', false)) {
            $form_url = "https://alpha.zarolead.com";
        } else if(env('APP_BETA', false)) {
            $form_url = "https://beta2.zarolead.com";
        }

        $form_html  = "<iframe scrolling=\"no\" id=\"buy-frame\" width=\"100%\" height=\"500px\" src=\"\"></iframe>" . PHP_EOL;
        $form_html .= "<script type=\"text/javascript\">" . PHP_EOL;
        $form_html .= "document.addEventListener('DOMContentLoaded', function(event) {" . PHP_EOL;
        $form_html .= "var url_string = window.location.toString();" . PHP_EOL;
        $form_html .= "var url = new URL(url_string);" . PHP_EOL;
        $form_html .= "var form_id = url.searchParams.get('id');" . PHP_EOL;
        $form_html .= "if (form_id == '' || form_id == null) { form_id = " . $default_form . "; }" . PHP_EOL;
        $form_html .= "var iframe_src = '" . $form_url . "/forms/' + form_id + '/get?landing=" . $id . "';" . PHP_EOL;

        $form_html .= "var loc = window.location.toString(), params = '';" . PHP_EOL;
        $form_html .= "if (loc.indexOf('?') > -1) {" . PHP_EOL;
        $form_html .= "    params = loc.split('?')[1]," . PHP_EOL;
        $form_html .= "    iframe_src_url = iframe_src;" . PHP_EOL;
        $form_html .= "    separator = '?';" . PHP_EOL;
        $form_html .= "    if (iframe_src_url.indexOf('?') > -1) {" . PHP_EOL;
        $form_html .= "        separator = '&';" . PHP_EOL;
        $form_html .= "    }" . PHP_EOL;
        $form_html .= "    iframe_src = iframe_src_url + separator + params;" . PHP_EOL;
        $form_html .= "}" . PHP_EOL;
        $form_html .= "" . PHP_EOL;
        $form_html .= "iframe_id = 'buy-frame';" . PHP_EOL;
        $form_html .= "iframe = document.getElementById(iframe_id);" . PHP_EOL;
        $form_html .= "iframe.src = iframe_src;" . PHP_EOL;
        $form_html .= "});" . PHP_EOL;
        $form_html .= "</script>" . PHP_EOL;

        return response([
            'data' => $form_html,
        ]);
    }

    private function getDirectoryContent($directory) {
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

    /**
     * Get landing's files and directories list
     *
     * @urlParam id int required Landing's id
     *
     * @response {
     *  "data": [content]
     * }
     */
    public function listFilesAndDirectories($id)
    {
        $landing = Landing::findOrFail($id);
        $this->authorize('view', $landing);

        $content = null;
        if ($landing->path != null && $landing->path != '') {
            $content = $this->getDirectoryContent('apps/' . $landing->path);
        }

        return response([
            'data' => $content,
        ]);
    }

    /**
     * Get landing's file content
     *
     * @urlParam id int required Landing's id
     *
     * @bodyParam pathname string required File's pathname
     *
     * @response {
     *  "data": content
     * }
     */
    public function getFileContent($id, Request $request)
    {
        $landing = Landing::findOrFail($id);
        $this->authorize('view', $landing);

        // Params validation
        $params = $request->validate([
            'pathname' => 'required|string|min:1',
        ]);

        // TODO: Check if pathname contains (at the beginning) $landing->path . '/'

        $content = Storage::disk('landings')->get('apps/' . $params['pathname']);

        return response([
            'data' => $content,
        ]);
    }

    /**
     * Create landing's file
     *
     * @urlParam id int required Landing's id
     *
     * @bodyParam pathname string required File's pathname
     *
     * @response {
     *  "message": "File created successfully"
     * }
     */
    public function createFile($id, Request $request)
    {
        $landing = Landing::findOrFail($id);
        $this->authorize('view', $landing);

        // Params validation
        $params = $request->validate([
            'pathname' => 'required|string|min:1',
        ]);

        // TODO: Check if pathname contains (at the beginning) $landing->path . '/'

        Storage::disk('landings')->put('apps/' . $params['pathname'], '');

        return response([
            'message' => 'File created successfully',
        ]);
    }

    /**
     * Create landing's directory
     *
     * @urlParam id int required Landing's id
     *
     * @bodyParam pathname string required Directory's pathname
     *
     * @response {
     *  "message": "Directory created successfully"
     * }
     */
    public function createDirectory($id, Request $request)
    {
        $landing = Landing::findOrFail($id);
        $this->authorize('view', $landing);

        // Params validation
        $params = $request->validate([
            'pathname' => 'required|string|min:1',
        ]);

        // TODO: Check if pathname contains (at the beginning) $landing->path . '/'

        Storage::disk('landings')->makeDirectory('apps/' . $params['pathname']);

        return response([
            'message' => 'Directory created successfully',
        ]);
    }

    /**
     * Save landing's file content
     *
     * @urlParam id int required Landing's id
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
        $landing = Landing::findOrFail($id);
        $this->authorize('view', $landing);

        // Params validation
        $params = $request->validate([
            'path' => 'required|string|min:1',
            'file' => 'required|file',
        ]);

        // TODO: Check if path contains (at the beginning) $landing->path . '/'

        Storage::disk('landings')->putFileAs(
            'apps/' . $params['path'],
            $request->file('file'),
            $request->file('file')->getClientOriginalName()
        );

        return response([
            'message' => 'File saved successfully',
        ]);
    }
}
