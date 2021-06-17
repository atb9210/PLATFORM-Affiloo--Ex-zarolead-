<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Driverfury\Cloudflare\Cloudflare;

class Domain extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain', 'path', 'info',
    ];

    protected $casts = [
        'info' => 'array',
    ];

    /*
     * Relationships methods
     */

    public function flows()
    {
        return $this->hasMany('App\Flow');
    }

    /*
     * Logic methods
     */

    public static function getFromUrl(string $url): Domain
    {
        $host = parse_url($url, PHP_URL_HOST);
        $domain = Domain::where('domain', $host)->first();
        return $domain;
    }

    public function getDirectoryFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (strpos($path, '/') === false) {
            $path = '/' . $path;
        }
        if (strpos($path, '.') !== false) {
            $lastIndex = strrpos($path, '/');
            if ($lastIndex !== false && $lastIndex !== 0) {
                $path = substr($path, 0, $lastIndex);
            } else if ($lastIndex == 0) {
                $path = '/';
            }
        }
        if ($path != '/' && $path[strlen($path)-1] != '/') {
            $path .= '/';
        }

        return 'public' . $path;
    }

    public function createAppAndSetDns(string $path): bool
    {
        $result = false;

        $params = [
            'path' => $path,
            'domain' => $this->domain,
        ];

        $info = [];
        if ($this->info !== null) {
            $info = $this->info;
        }

        // Create domain's apps (folder and virtualhost) on landings' server with ServerPilot
        $serverpilot = $this->serverpilotGetClient();
        $app = $serverpilot->app_create(
            $params['path'],
            env('SERVERPILOT_LANDINGS_SYSUSER_ID'),
            env('SERVERPILOT_APPS_PHP_VERSION'),
            [$params['domain'], 'www.'.$params['domain']],
            []
        );
        $appId = $app['data']['id'];
        $info['serverpilot_app_id'] = $appId;
        $this->info = $info;
        $this->save();

        // TODO: What about these? Test this code.
        //$serverpilot->ssl_delete($appId);
        //$serverpilot->ssl_auto($appId);
        //$serverpilot->ssl_force($appId, true);

        // Set domain's record on Cloudflare
        $cloudflare = new Cloudflare(env('CLOUDFLARE_API_KEY'));
        $zones = $cloudflare->getZones();
        if ($zones !== false) {
            $zone = null;
            foreach ($zones as $z) {
                if ($z['name'] == $params['domain']) {
                    $zone = $z;
                    break;
                }
            }

            if ($zone === null) {
                $zone = $cloudflare->createZone($params['domain']);
            }

            if ($zone !== false && $zone !== null) {
                $dnsRecords = $cloudflare->getDNSRecords($zone['id']);
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
                    $cloudflare->deleteDNSRecord($zone['id'], $dnsRecord['id']);
                }

                // TODO: What about env('SFTP_HOST')???
                $dnsRecord = $cloudflare->addDNSRecord($zone['id'], 'A', $params['domain'], env('SFTP_HOST'));
                if ($dnsRecord !== false) {
                    $result = true;
                    $this->namecheapSetNameservers($params['domain'], Cloudflare::$nameservers);
                }
            }
        }

        return $result;
    }

    public function change(string $newDomain): void
    {
        // App update on Serverpilot
        $serverpilot = $this->serverpilotGetClient();
        $apps = ($serverpilot->app_list())->data;
        foreach ($apps as $app) {
            if ($app->name == $this->path) {
                $serverpilot->app_update($app->id, env('SERVERPILOT_APPS_PHP_VERSION'), [$newDomain, 'www.'.$newDomain]);
                break;
            }
        }

        // Update zone on Cloudflare (delete the old one and create a new one)
        $cloudflare = new Cloudflare(env('CLOUDFLARE_API_KEY'));
        $zones = $cloudflare->getZones();
        if ($zones !== false) {
            $zone = null;
            foreach ($zones as $z) {
                if ($z['name'] == $this->domain) {
                    $cloudflare->deleteZone($z['id']);
                } else if ($z['name'] == $newDomain) {
                    $zone = $z;
                }
            }

            if ($zone === null) {
                $zone = $cloudflare->createZone($newDomain);
            }

            if ($zone !== false && $zone !== null) {
                $dnsRecords = $cloudflare->getDNSRecords($zone['id']);
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
                    $cloudflare->deleteDNSRecord($zone['id'], $dnsRecord['id']);
                }

                // TODO: What about env('SFTP_HOST')???
                $dnsRecord = $cloudflare->addDNSRecord($zone['id'], 'A', $newDomain, env('SFTP_HOST'));
                if ($dnsRecord !== false) {
                    $this->update($params);
                }
            }
        }
    }

    public function deleteAppAndZone(): void
    {
        // App deletion on Serverpilot
        $serverpilot = $this->serverpilotGetClient();
        $apps = ($serverpilot->app_list())->data;
        foreach ($apps as $app) {
            if ($app->name == $this->path) {
                $serverpilot->app_delete($app->id);
                break;
            }
        }

        // Zone deletion on Cloudflare??
        $cloudflare = new Cloudflare(env('CLOUDFLARE_API_KEY'));
        $zones = $cloudflare->getZones();
        if ($zones !== false) {
            foreach ($zones as $z) {
                if ($z['name'] == $this->domain) {
                    $cloudflare->deleteZone($z['id']);
                    break;
                }
            }
        }
    }

    // TODO: Implement recursive function
    public function getDirectoryContent(string $directory = '', bool $recursive = false): array
    {
        $content = [];

        // Adjust directory format
        if ($directory != '') {
            if ($directory[0] == '/') {
                $directory = substr($directory, 1);
            }
            if (substr($directory, 0, 2) == './') {
                $directory = substr($directory, 2);
            }
            if ($directory == '.' || $directory == '/') {
                $directory = '';
            }
            str_replace('/./', '/', $directory);
        }

        $prefix = 'apps/' . $this->path . '/';
        $prefixLength = strlen($prefix);
        $directoryPathname = $prefix . $directory;
        if ($directoryPathname[strlen($directoryPathname)-1] != '/') {
            $directoryPathname .= '/';
        }
        $directoryPathnameLength = strlen($directoryPathname);

        $directories = Storage::disk('landings')->directories($directoryPathname);
        foreach ($directories as $dir) {
            $pathname = substr($dir, $prefixLength);
            $dirContent = [];
            if ($recursive) {
                // TODO: It does not work with only the line below
                //$dirContent = $this->getDirectoryContent($pathname);
            }
            $content[] = [
                'pathname' => $pathname,
                'name' => substr($dir, $directoryPathnameLength),
                'type' => 'directory',
                'data' => $dirContent,
            ];
        }

        $files = Storage::disk('landings')->files($directoryPathname);
        foreach ($files as $file) {
            $pathname = substr($file, $prefixLength);
            $content[] = [
                'pathname' => $pathname,
                'name' => substr($file, $directoryPathnameLength),
                'type' => 'file',
                'data' => '',
            ];
        }

        return $content;
    }

    public function getFileContent(string $pathname): string
    {
        $content = Storage::disk('landings')->get('apps/' . $this->path . '/' . $pathname);
        return $content;
    }

    public function uploadFile($path, $file, $file_pathname)
    {
        Storage::disk('landings')->putFileAs('apps/' . $this->path . '/' . $path, $file, $file_pathname);
    }

    public function createFile(string $pathname, $contents = '')
    {
        Storage::disk('landings')->put('apps/' . $this->path . '/' . $pathname, $contents);
    }

    public function deleteFile(string $pathname)
    {
        Storage::disk('landings')->delete('apps/' . $this->path . '/' . $pathname);
    }

    public function createDirectory(string $pathname)
    {
        Storage::disk('landings')->makeDirectory('apps/' . $this->path . '/' . $pathname);
    }

    public function deleteDirectory(string $pathname)
    {
        Storage::disk('landings')->deleteDirectory('apps/' . $this->path . '/' . $pathname);
    }

    private function serverpilotGetClient()
    {
        $serverpilotConfig = [
            'id' => env('SERVERPILOT_CLIENT_ID'),
            'key' => env('SERVERPILOT_API_KEY'),
        ];
        return new \ServerPilot($serverpilotConfig);
    }

    public static function namecheapGetClient()
    {
        $client = new \Namecheap\Api(
            env('NAMECHEAP_USER'),
            env('NAMECHEAP_API_KEY'),
            env('NAMECHEAP_USER'),
            env('NAMECHEAP_CLIENT_IP'),
            'array'
        );
        if (env('NAMECHEAP_SANDBOX')) {
            $client->enableSandbox();
        }
        return $client;
    }

    public static function namecheapGetDomains()
    {
        // TODO: What about pages (like Cloudflare API)???

        $client = self::namecheapGetClient();

        $ncDomains = new \Namecheap\Domain\Domains($client);
        if (env('NAMECHEAP_SANDBOX')) {
            $ncDomains->enableSandbox();
        }
        $domainList = $ncDomains->getList(); 

        $q = 'ApiResponse.CommandResponse.DomainGetListResult.Domain';
        $q = explode('.', $q);
        $domains = [];
        $current = $domainList;
        foreach ($q as $element) {
            if (isset($current[$element])) {
                $current = $current[$element];
            } else {
                // TODO: Log error?
                $domains = [];
                break;
            }
        }
        if (isset($current['_ID'])) {
            $domains[] = $current;
        } else {
            $domains = $current;
        }

        return $domains;
    }

    private function namecheapSetNameservers($domain, $nameservers)
    {
        $client = self::namecheapGetClient();

        $ncDomainsDns = new \Namecheap\Domain\DomainsDns($client);
        if (env('NAMECHEAP_SANDBOX')) {
            $ncDomains->enableSandbox();
        }

        $domainParts = explode('.', $domain);
        $domainSld = $domainParts[0];
        $domainTld = $domainParts[1];
        $formattedNameservers = implode(',', $nameservers);

        $response = $ncDomainsDns->setCustom($domainSld, $domainTld, $formattedNameservers);

        // TODO: Check response and log errors
    }

    public function getUrlFromPath($path)
    {
        $result = '';

        $adjustedPath = $path;
        $toRemove = [
            '/^apps\/' . $this->path . '\/public\//',
            '/^\/' . $this->path . '\/public\//',
            '/^' . $this->path . '\/public\//',
            '/^public\//',
            '/^\/public\//',
        ];
        foreach ($toRemove as $regex) {
            $adjustedPath = preg_replace($regex, '', $adjustedPath);
        }
        $result = 'https://' . $this->domain . '/' . $adjustedPath;

        return $result;
    }

    public function setSSL(string $ssl)
    {
        $info = [];
        if ($this->info !== null) {
            $info = $this->info;
        }

        $info['ssl'] = $ssl;
        $this->info = $info;
        $this->save();
    }

    public function getSSL(): string
    {
        $result = 'No SSL';

        $info = [];
        if ($this->info !== null) {
            $info = $this->info;
        }

        if ($this->info !== null && isset($this->info['ssl'])) {
            $result = $this->info['ssl'];
        } else {
            $serverpilot = $this->serverpilotGetClient();
            $appId = $this->serverpilotGetAppId();
            $app = ($serverpilot->app_info($appId))->data;

            if ($app->ssl !== null) {
                $info['ssl'] = 'SSL';
            } else {
                $info['ssl'] = 'No SSL';
            }

            $this->info = $info;
            $this->save();
            $result = $this->info['ssl'];
        }

        return $result;
    }

    protected function serverpilotGetAppId(): string
    {
        $result = '';

        if (isset($this->info) && isset($this->info['serverpilot_app_id'])) {
            $result = $this->info['serverpilot_app_id'];
            return $result;
        }

        $serverpilot = $this->serverpilotGetClient();
        $apps = ($serverpilot->app_list())->data;
        foreach ($apps as $app) {
            if ($app->name == $this->path) {
                $result = $app->id;
                $info = [];
                if (isset($this->info) && is_array($this->info)) {
                    $info = $this->info;
                    $info['serverpilot_app_id'] = $result;
                    $this->info = $info;
                    $this->save();
                }
                break;
            }
        }
        return $result;
    }

    public function activateSSL(): bool
    {
        $result = false;

        $appId = $this->serverpilotGetAppId();
        $serverpilot = $this->serverpilotGetClient();
        $app = ($serverpilot->app_info($appId))->data;
        if ($app->ssl == null) {
            if ($app->autossl->available) {
                $serverpilot->ssl_auto($appId);
                $serverpilot->ssl_force($appId, true);
                $result = true;
            }
        } elseif ($app->ssl->auto == false) {
            if ($app->autossl->available) {
                $serverpilot->ssl_delete($appId);
                $serverpilot->ssl_auto($appId);
                $serverpilot->ssl_force($appId, true);
                $result = true;
            }
        } elseif ($app->ssl->auto) {
            $result = true;
        }

        $info = [];
        if ($this->info !== null) {
            $info = $this->info;
        }

        if ($result) {
            $info['ssl'] = 'SSL';
        } else {
            $info['ssl'] = 'No SSL';
        }
        $this->info = $info;
        $this->save();

        return $result;
    }

    public static function isRegistered(string $domain): bool
    {
        $result = (gethostbyname($domain) != $domain);
        return $result;
    }

    public static function isOnline(string $domainOrUrl): bool
    {
        $url = $domainOrUrl;

        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
            $url = 'http://' . $url;
        }

        // Check, if a valid url is provided
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // cURL HTTP request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return ($response ? true : false);
    }
}
