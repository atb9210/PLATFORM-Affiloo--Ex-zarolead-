<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\third\FbAccount as FbAccount;
use App\Http\Resources\api\third\FbAccountResource;

class FbAccountController extends Controller
{
    /**
     * Get all fb accounts list.
     *
     * @response {
     *  "data": [fb_accounts],
     * }
     */
    public function index()
    {
        //$this->authorize('viewAny', 'App\Tag');
        return FbAccountResource::collection(FbAccount::query()->get());
    }

    /**
     * Get fb account's informations.
     *
     * @urlParam id int required Fb account's id
     *
     * @response {
     *  "data": fb_account,
     * }
     */
    public function show($id)
    {
        $fb_account = FbAccount::findOrFail($id);
        //$this->authorize('view', $tag);

        return new FbAccountResource($fb_account);
    }

    /**
     * Create a new fb account.
     *
     * @bodyParam user_id int required Fb account's owner
     * @bodyParam name string required Fb account's name
     * @bodyParam notes string Fb account's notes
     * @bodyParam token string Fb account's token
     * @bodyParam status string Fb account's status
     * @bodyParam user_agent string Fb account's user agent
     * @bodyParam proxy string Fb account's proxy (Format: host::port::user::password)
     *
     * @response 201 {
     *  "message": "Fb account created",
     *  "data": fb_account,
     * }
     */
    public function store(Request $request)
    {
        //$this->authorize('create', 'App\Tag');

        // Params validation
        $params = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|min:1',
            'notes' => 'string|nullable',
            'token' => 'string',
            'status' => 'in:active,disabled',
            'user_agent' => 'string',
            'proxy' => 'string|regex:/^[a-zA-Z0-9-.]+::[0-9]+::[a-zA-Z0-9-_.]+::.+$/',
        ]);

        if (!isset($params['user_id'])) {
            $params['user_id'] = Auth::user()->id;
        }

        $options = [
            'name' => $params['name'],
        ];
        if (isset($params['notes'])) {
            $options['notes'] = $params['notes'];
        }
        $profile = [];
        if (isset($params['user_agent'])) {
            $profile['user_agent'] = $params['user_agent'];
            $options['navigator'] = [
                'userAgent' => $params['user_agent'],
                'platform' => 'Win32',
            ];
        }
        if (isset($params['proxy'])) {
            if ($params['proxy'] != '') {
                $profile['proxy_enabled'] = true;
                $profile['proxy'] = $params['proxy'];
                $options['proxyEnabled'] = true;
                $temp = explode('::', $params['proxy'], 4);
                $host = $temp[0];
                $port = intval($temp[1]);
                $user = $temp[2];
                $password = $temp[3];
                $options['proxy'] = [
                    'mode' => 'http',
                    'host' => $host,
                    'port' => $port,
                    'username' => $user,
                    'password' => $password,
                ];
            } else {
                $profile['proxy_enabled'] = false;
                $options['proxyEnabled'] = false;
            }
        }
        $params['profile'] = $profile;
        $gologin_id = $this->gologinCreate($options);
        if ($gologin_id == '' || $gologin_id == null) {
            return response([
                "message" => "Fb account cannot be created on GoLogin",
            ], 401);
        }

        $params['gologin_id'] = $gologin_id;
        $fb_account = FbAccount::create($params);
        return response([
            "message" => "Fb account created",
            "data" => new FbAccountResource($fb_account),
        ], 201);
    }

    /**
     * Update the fb account's fields.
     *
     * @urlParam id int required Fb account's id
     *
     * @bodyParam user_id int Fb account's owner
     * @bodyParam name string Fb account's name
     * @bodyParam notes string Fb account's notes
     * @bodyParam token string Fb account's token
     * @bodyParam status string Fb account's status
     * @bodyParam user_agent string Fb account's user agent
     * @bodyParam proxy string Fb account's proxy (Format: host::port::user::password)
     *
     * @response {
     *  "data": fb_account,
     * }
     */
    public function update(Request $request, $id)
    {
        $fb_account = FbAccount::findOrFail($id);
        //$this->authorize('update', $tag);

        // Params validation
        $params = $request->validate([
            'user_id' => 'integer|exists:users,id',
            'name' => 'string|min:1',
            'notes' => 'string|nullable',
            'token' => 'string',
            'status' => 'in:active,disabled',
            'user_agent' => 'string',
            'proxy' => 'string|regex:/^[a-zA-Z0-9-.]+::[0-9]+::[a-zA-Z0-9-_.]+::.+$/',
        ]);

        if (!isset($params['user_id'])) {
            $params['user_id'] = Auth::user()->id;
        }

        $options = [];
        if (isset($params['name'])) {
            $options['name'] = $params['name'];
        }
        if (isset($params['notes'])) {
            $options['notes'] = $params['notes'];
        }
        $profile = $fb_account->profile;
        if (isset($params['user_agent'])) {
            $profile['user_agent'] = $params['user_agent'];
            $options['navigator'] = [
                'userAgent' => $params['user_agent'],
                'platform' => 'Win32',
            ];
        }
        if (isset($params['proxy'])) {
            if ($params['proxy'] != '') {
                $profile['proxy_enabled'] = true;
                $profile['proxy'] = $params['proxy'];
                $options['proxyEnabled'] = true;
                $temp = explode('::', $params['proxy'], 4);
                $host = $temp[0];
                $port = intval($temp[1]);
                $user = $temp[2];
                $password = $temp[3];
                $options['proxy'] = [
                    'mode' => 'http',
                    'host' => $host,
                    'port' => $port,
                    'username' => $user,
                    'password' => $password,
                ];
            } else {
                $profile['proxy_enabled'] = false;
                $options['proxyEnabled'] = false;
            }
        }
        $params['profile'] = $profile;
        if (!empty($options)) {
            if (!($fb_account->gologin_id != '' && $fb_account->gologin_id != null && $this->gologinUpdate($fb_account->gologin_id, $options))) {
                return response([
                    "message" => "Fb account cannot be updated on GoLogin",
                ], 401);
            }
        }
        $fb_account->update($params);

        return new FbAccountResource($fb_account);
    }

    /**
     * Delete the specified fb account.
     *
     * @urlParam id int required fb account's id
     *
     * @response {
     *  "message": "Fb account has been delete",
     * }
     */
    public function destroy($id)
    {
        $fb_account = FbAccount::findOrFail($id);

        //$this->authorize('delete', $tag);

        if ($fb_account->gologin_id != null) {
            $this->gologinDelete($fb_account->gologin_id);
        }
        $fb_account->delete();

        return response([
            'message' => 'Fb account has been deleted'
        ]);
    }

    /**
     * Import fb accounts from gologin.
     *
     * @bodyParam user_id int required Fb accounts' owner
     * @bodyParam gologin_ids array required Fb accounts' gologin ids array
     *
     * @response {
     *  "message": [fb_account],
     * }
     */
    public function import(Request $request)
    {
        // Params validation
        $params = $request->validate([
            'user_id' => 'integer|exists:users,id',
            'gologin_ids' => 'required|array',
            'gologin_ids.*' => 'string|min:3',
        ]);

        $user_id = Auth::user()->id;
        if (isset($params['user_id'])) {
            $user_id = $params['user_id'];
        }

        // TODO: to delete
        /*
        echo "<pre>".var_export($params['gologin_ids'], true)."</pre>";
        exit();
        */

        $fb_accounts = [];
        foreach ($params['gologin_ids'] as $gologin_id) {
            if (FbAccount::where('gologin_id', $gologin_id)->count() >= 1) {
                continue;
            }

            $profile = $this->gologinGetProfile($gologin_id);
            if (empty($profile) || !isset($profile['id']) || !isset($profile['name'])) {
                continue;
            }

            $data = [
                'user_id' => $user_id,
                'name' => $profile['name'],
                'gologin_id' => $profile['id'],
                'profile' => [
                    'proxy_enabled' => false,
                ],
            ];
            if (isset($profile['notes'])) {
                $data['notes'] = $profile['notes'];
            }

            if (isset($profile['navigator']) && isset($profile['navigator']['userAgent'])) {
                $data['profile']['user_agent'] = $profile['navigator']['userAgent'];
            }
            if (isset($profile['proxyEnabled']) && isset($profile['proxy']) &&
                isset($profile['proxy']['host']) && isset($profile['proxy']['port']) &&
                isset($profile['proxy']['username']) && isset($profile['proxy']['password'])) {
                $data['profile']['proxy_enabled'] = true;
                $data['profile']['proxy'] = $profile['proxy']['host'] . '::' . $profile['proxy']['port'] . '::' . $profile['proxy']['username'] . '::' . $profile['proxy']['password'];
            }
            $fb_accounts[] = FbAccount::create($data);
        }

        return FbAccountResource::collection($fb_accounts);
    }

    // Put these functions into a vendor package?
    private $gologinToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI1ZTY3Y2VkNjNjODg5NTA5YWI3N2I2OGMiLCJ0eXBlIjoiZGV2Iiwiand0aWQiOiI1ZTg1YjY1ZTNhYmRkMzJhNTg1ZDI0YTgifQ.CI4zOcLcWmcoKT6H_hhPFzm3UcBpdBgIRnzjcOZS4-I';

    private function gologinInitialize(string $token)
    {
        $this->gologinToken = $token;
    }

    private function gologinGetRandomFingerprint(array $options = []) : array
    {
        $fingerprint = [];

        $os = 'win';
        if (isset($options['os'])) {
            $os = $options['os'];
        } 

        try {
            $ch = curl_init();
            $url = 'https://api.gologin.app/browser/fingerprint?os=' .  $os;

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->gologinToken,
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode == 200) {
                $fingerprint = json_decode($response, true);
            }
        } catch (Exception $e) {
        } catch (Throwable $e) {
        }
        
        return $fingerprint;
    }

    private function gologinGetProfile(string $gologin_id) : array
    {
        $profile = [];

        try {
            $ch = curl_init();
            $url = 'https://api.gologin.app/browser/' . $gologin_id;

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->gologinToken,
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode == 200) {
                $profile = json_decode($response, true);
            }
        } catch (Exception $e) {
        } catch (Throwable $e) {
        }

        return $profile;
    }

    private function gologinCreate(array $options = [])
    {
        $gologin_id = null;
        $profileOptions = $this->gologinGetRandomFingerprint($options);

        $params = [
            "name" => "default_name",
            "notes" => "auto generated",
            "browserType" => "chrome",
            "os" => "lin",
            "startUrl" => "google.com",
            "googleServicesEnabled" => false,
            "lockEnabled" => false,
            "audioContext" => [
                "mode" => "noise"
            ],
            "canvas" => [
                "mode" => "noise"
            ],
            "webRTC" => [
                "mode" => "alerted",
                "enabled" => true,
                "customize" => true,
                "fillBasedOnIp" => true
            ],
            "navigator" => [
                "userAgent" => "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:71.0) Gecko/20100101 Firefox/71.0",
                "resolution" => "1200x800",
                "language" => "EN",
                "platform" => "Linux x86_64"
            ],
            "proxyEnabled" => true,
            "screenHeight" => 800,
            "screenWidth" => 1200,
            "profile" => json_encode($profileOptions),
        ];

        foreach ($options as $key => $value) {
            $params[$key] = $options[$key];
        }

        try {
            $ch = curl_init();
            $url = 'https://api.gologin.app/browser/';

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->gologinToken,
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

            $response = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode == 201) {
                $response = json_decode($response, true);
                $gologin_id = $response['id'];
            }
        } catch (Exception $e) {
        } catch (Throwable $e) {
        }
        return $gologin_id;
    }

    private function gologinUpdate(string $gologin_id, array $options) : bool
    {
        $profile = $this->gologinGetProfile($gologin_id);

        foreach ($options as $key => $value) {
            $profile[$key] = $options[$key];
        }

        try {
            $ch = curl_init();
            $url = 'https://api.gologin.app/browser/' . $gologin_id;

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->gologinToken,
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($profile));

            $response = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode == 204) {
                return true;
            }
        } catch (Exception $e) {
        } catch (Throwable $e) {
        }
        return false;
    }

    private function gologinDelete(string $gologin_id) : bool
    {
        try {
            $ch = curl_init();
            $url = 'https://api.gologin.app/browser/' . $gologin_id;

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->gologinToken,
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

            $response = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode == 204) {
                return true;
            }
        } catch (Exception $e) {
        } catch (Throwable $e) {
        }
        return false;
    }
}
