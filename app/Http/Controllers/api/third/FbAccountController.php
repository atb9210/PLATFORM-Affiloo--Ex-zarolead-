<?php

namespace App\Http\Controllers\api\third;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\third\FbAccount as FbAccount;
use App\Http\Resources\api\third\FbAccountResource;

class FbAccountController extends Controller
{
    /**
     * Get all fb accounts list.
     *
     * @urlParam filter_user int User id
     * @urlParam filter_status string Status
     * @urlParam filter_product string Product id
     *
     * @response {
     *  "data": [fb_accounts],
     * }
     */
    public function index(Request $request)
    {
        //$this->authorize('viewAny', 'App\Tag');
        $params = $request->validate([
            'filter_user' => 'integer|exists:users,id',
            'filter_status' => 'in:setup,run,ban',
            'filter_product' => 'integer|exists:products,id',
        ]);

        $fb_accounts = FbAccount::query();
        if (!(Auth::user()->hasRole('developer') || Auth::user()->hasRole('admin') || Auth::user()->hasRole('account_manager'))) {
            $fb_accounts = $fb_accounts->where('user_id', Auth::user()->id);
        }
        if (isset($params['filter_user'])) {
            $fb_accounts = $fb_accounts->where('user_id', $params['filter_user']);
        }
        if (isset($params['filter_status'])) {
            $fb_accounts = $fb_accounts->where('status', $params['filter_status']);
        }
        if (isset($params['filter_product'])) {
            $fb_accounts = $fb_accounts->where('product_id', $params['filter_product']);
        }

        return FbAccountResource::collection($fb_accounts->get());
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
     * @bodyParam product_id array Fb account's product
     * @bodyParam notes string Fb account's notes
     * @bodyParam token string Fb account's token
     * @bodyParam info json Fb account's info
     * @bodyParam status string Fb account's status
     * @bodyParam token_status string Fb account's token status
     * @bodyParam user_agent string Fb account's user agent
     * @bodyParam proxy_enabled bool Fb account's proxy is enabled
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
            'user_id' => 'integer|exists:users,id',
            'name' => 'required|string|min:1',
            'product_id' => 'integer|exists:products,id',
            'notes' => 'string|nullable',
            'token' => 'nullable|string',
            'info' => 'nullable|json',
            'status' => 'in:setup,run,ban',
            'token_status' => 'in:active,disabled',
            'user_agent' => 'nullable|string',
            'proxy_enabled' => 'boolean',
            'proxy' => 'nullable|string|regex:/^([a-zA-Z0-9-.]*::[0-9]*::[a-zA-Z0-9-_.]*::.*)?$/',
        ]);
        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
        }

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
                'resolution' => '1600x900',
                'language' => 'it-IT',
                'platform' => 'Win32',
            ];
        }
        if (isset($params['proxy_enabled'])) {
            $profile['proxy_enabled'] = ($params['proxy_enabled'] == true);
            $options['proxyEnabled'] = ($params['proxy_enabled'] == true);
        } else {
            $profile['proxy_enabled'] = false;
            $options['proxyEnabled'] = false;
        }
        if (isset($params['proxy'])) {
            if ($params['proxy'] != '') {
                $profile['proxy'] = $params['proxy'];
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
            }
        }
        $params['profile'] = $profile;
        $options['os'] = 'win';
        $options['browserType'] = 'chrome';
        $options['googleServicesEnabled'] = true;

        /*
        $gologin_id = $this->gologinCreate($options);
        if ($gologin_id == '' || $gologin_id == null) {
            return response([
                "message" => "Fb account cannot be created on GoLogin",
                "gologin_errors" => $this->gologinGetErrors(),
            ], 401);
        }

        $params['gologin_id'] = $gologin_id;
        */
        $fb_account = FbAccount::create($params);

        if (isset($params['token']) && $params['token'] != '' && $params['token'] != null) {
            $dolphin_id = $this->dolphinCreate($fb_account);
            if ($dolphin_id != null) {
                $fb_account->dolphin_id = $dolphin_id;
                $fb_account->save();
            }
        }

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
     * @bodyParam product_id array Fb account's product
     * @bodyParam notes string Fb account's notes
     * @bodyParam token string Fb account's token
     * @bodyParam info json Fb account's info
     * @bodyParam status string Fb account's status
     * @bodyParam token_status string Fb account's token status
     * @bodyParam user_agent string Fb account's user agent
     * @bodyParam proxy_enabled bool Fb account's proxy is enabled
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
            'product_id' => 'integer|exists:products,id',
            'notes' => 'string|nullable',
            'token' => 'nullable|string',
            'info' => 'nullable|json',
            'status' => 'in:setup,run,ban',
            'token_status' => 'in:active,disabled',
            'user_agent' => 'nullable|string',
            'proxy_enabled' => 'boolean',
            'proxy' => 'nullable|string|regex:/^([a-zA-Z0-9-.]*::[0-9]*::[a-zA-Z0-9-_.]*::.*)?$/',
        ]);
        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], true);
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
                'resolution' => '1600x900',
                "language" => "it-IT",
            ];
        }
        if (isset($params['proxy_enabled'])) {
            $profile['proxy_enabled'] = ($params['proxy_enabled'] == true);
            $options['proxyEnabled'] = ($params['proxy_enabled'] == true);
        }
        if (isset($params['proxy'])) {
            if ($params['proxy'] != '' && $params['proxy'] != null) {
                $profile['proxy'] = $params['proxy'];
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
                $profile['proxy'] = '';
            }
        }
        $params['profile'] = $profile;
        /*
        if (!empty($options)) {
            if (!($fb_account->gologin_id != '' && $fb_account->gologin_id != null && $this->gologinUpdate($fb_account->gologin_id, $options))) {
                return response([
                    "message" => "Fb account cannot be updated on GoLogin",
                    "gologin_errors" => $this->gologinGetErrors(),
                ], 401);
            }
        }
        */
        $fb_account->update($params);

        if ($fb_account->dolphin_id == null && isset($params['token']) && $params['token'] != '' && $params['token'] != null && $params['token'] != $fb_account->token) {
            $dolphin_id = $this->dolphinCreate($fb_account);
            if ($dolphin_id != null) {
                $fb_account->dolphin_id = $dolphin_id;
                $fb_account->save();
            }
        }

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
            /*
            if (!$this->gologinDelete($fb_account->gologin_id)) {
                //return response([
                //    "message" => "Fb account cannot be deleted on GoLogin",
                //    "gologin_errors" => $this->gologinGetErrors(),
                //], 401);
            }
            */
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

        $fb_accounts = [];
        foreach ($params['gologin_ids'] as $gologin_id) {
            if (FbAccount::where('gologin_id', $gologin_id)->count() >= 1) {
                continue;
            }

            //$profile = $this->gologinGetProfile($gologin_id);
            $profile = [];
            if (empty($profile) || !isset($profile['id']) || !isset($profile['name'])) {
                continue;
            }

            $data = [
                'user_id' => $user_id,
                //'name' => $profile['name'],
                //'gologin_id' => $profile['id'],
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
            if (isset($profile['proxyEnabled'])) {
                $data['profile']['proxy_enabled'] = ($profile['proxyEnabled'] == true);
            }
            if (isset($profile['proxy']) &&
                isset($profile['proxy']['host']) && isset($profile['proxy']['port']) &&
                isset($profile['proxy']['username']) && isset($profile['proxy']['password'])) {
                $data['profile']['proxy'] = $profile['proxy']['host'] . '::' . $profile['proxy']['port'] . '::' . $profile['proxy']['username'] . '::' . $profile['proxy']['password'];
            }
            $fb_accounts[] = FbAccount::create($data);
        }

        return FbAccountResource::collection($fb_accounts);
    }

    /**
     * Create multiple fb accounts.
     *
     * @bodyParam data json required Fb accounts
     *
     * @response {
     *  "message": [fb_account],
     * }
     */
    public function storeMultiple(Request $request)
    {
        // Params validation
        $data = $request->validate([
            'data' => 'required|json',
        ]);
        $data['data'] = json_decode($data['data'], true);

        // Validate every element of data
        foreach ($data['data'] as $element) {
            if (is_array($element)) {
                $validator = Validator::make($element, [
                    'user_id' => 'integer|exists:users,id',
                    'name' => 'required|string|min:1',
                    'product_id' => 'integer|exists:products,id',
                    'notes' => 'string|nullable',
                    'token' => 'string|nullable',
                    'info' => 'json|nullable',
                    'status' => 'in:setup,run,ban',
                    'token_status' => 'in:active,disabled',
                    'user_agent' => 'string|nullable',
                    'proxy_enabled' => 'boolean',
                    'proxy' => 'nullable|string|regex:/^([a-zA-Z0-9-.]*::[0-9]*::[a-zA-Z0-9-_.]*::.*)?$/',
                ]);
                if ($validator->fails()) {
                    return response([
                        'message' => 'The given data - for the data JSON param - was invalid.',
                        'errors' => $validator->messages(),
                    ], 422);
                }
            } else {
                return response([
                    'message' => 'The given data - for the data JSON param - was invalid.',
                    'errors' => [
                        'data' => [
                            'Every element of data should be a json array',
                        ],
                    ],
                ], 422);
            }
        }

        // Create fb accounts
        $fb_accounts = [];
        foreach ($data['data'] as $element) {
            $params = $element;
            if (isset($params['info'])) {
                $params['info'] = json_decode($params['info'], true);
            }

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
                    'resolution' => '1600x900',
                    'language' => 'it-IT',
                    'platform' => 'Win32',
                ];
            }
            if (isset($params['proxy_enabled'])) {
                $profile['proxy_enabled'] = ($params['proxy_enabled'] == true);
                $options['proxyEnabled'] = ($params['proxy_enabled'] == true);
            } else {
                $profile['proxy_enabled'] = false;
                $options['proxyEnabled'] = false;
            }
            if (isset($params['proxy'])) {
                if ($params['proxy'] != '') {
                    $profile['proxy'] = $params['proxy'];
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
                }
            }
            $params['profile'] = $profile;
            $options['os'] = 'win';
            $options['browserType'] = 'chrome';
            $options['googleServicesEnabled'] = true;
            /*
            $gologin_id = $this->gologinCreate($options);
            if ($gologin_id == '' || $gologin_id == null) {
                return response([
                    "message" => "Fb account cannot be created on GoLogin",
                    "gologin_errors" => $this->gologinGetErrors(),
                ], 401);
            }

            $params['gologin_id'] = $gologin_id;
            */
            $fb_account = FbAccount::create($params);

            if (isset($params['token']) && $params['token'] != '' && $params['token'] != null) {
                $dolphin_id = $this->dolphinCreate($fb_account);
                if ($dolphin_id != null) {
                    $fb_account->dolphin_id = $dolphin_id;
                    $fb_account->save();
                }
            }

            $fb_accounts[] = $fb_account;
        }

        return FbAccountResource::collection($fb_accounts);
    }

    // Put these functions into a vendor package?
    private $gologinErrors = [];

    public function gologinGetErrors() : array
    {
        return $this->gologinErrors;
    }

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
            } else {
                $this->gologinErrors[] = json_decode($response, true);
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
            } else {
                $this->gologinErrors[] = json_decode($response, true);
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
            "notes" => "",
            "browserType" => "chrome",
            "os" => "win",
            "startUrl" => "https://business.facebook.com",
            "googleServicesEnabled" => true,
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
                "userAgent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.111 Safari/537.36",
                "resolution" => "1600x900",
                "language" => "it-IT",
                "platform" => "Win32"
            ],
            "proxyEnabled" => false,
            "screenHeight" => 900,
            "screenWidth" => 1600,
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
            } else {
                $this->gologinErrors[] = json_decode($response, true);
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
            } else {
                $this->gologinErrors[] = json_decode($response, true);
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
            } else {
                $this->gologinErrors[] = json_decode($response, true);
            }
        } catch (Exception $e) {
        } catch (Throwable $e) {
        }
        return false;
    }

    private $dolphinErrors = [];

    public function dolphinGetErrors() : array
    {
        return $this->dolphinErrors;
    }

    public function dolphinCreate($fb_account)
    {
        $dolphin_id = null;

        $params = [
            "name" => $fb_account->name,
            "access_token" => $fb_account->token,
            "tags" => [],
        ];
        if (isset($fb_account->profile['user_agent']) && $fb_account->profile['user_agent'] != '') {
            $params['user_agent'] = $fb_account->profile['user_agent'];
        }
        if (isset($fb_account->profile['proxy_enabled']) && $fb_account->profile['proxy_enabled'] &&
            isset($fb_account->profile['proxy']) && $fb_account->profile['proxy'] != '') {
            $temp = explode('::', $fb_account->profile['proxy'], 4);
            $host = $temp[0];
            $port = intval($temp[1]);
            $user = $temp[2];
            $password = $temp[3];
            $params['proxy'] = [
                'ip' => $host,
                'port' => $port,
                'type' => 'http',
                'login' => $user,
                'password' => $password,
            ];
        }

        try {
            $ch = curl_init();
            $url = 'http://justadforme.com/new/accounts/add';

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: 1-e5f858ef904cb6820a3d3b32877327e3',
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

            $response = curl_exec($ch);


            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 200) {
                $response = json_decode($response, true);
                if (isset($response['success']) && $response['success']) {
                    $dolphin_id = $response['data']['account_id'];
                }
            } else {
                $this->dolphinErrors[] = json_decode($response, true);
            }
        } catch (Exception $e) {
        } catch (Throwable $e) {
        }
        return $dolphin_id;
    }
}
