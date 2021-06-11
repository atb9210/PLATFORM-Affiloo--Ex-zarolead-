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
        'domain', 'path',
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

    public function createAppAndSetDns($path)
    {
        $result = false;

        $params = [
            'path' => $path,
            'domain' => $this->domain,
        ];

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

    public function change($newDomain)
    {
        // App update on Serverpilot
        $serverpilot = $this->serverpilotGetClient();
        $apps = ($serverpilot->app_list())->data;
        foreach ($apps as $app) {
            if ($app->name == $this->path) {
                $serverpilot->app_update($app->id, env('SERVERPILOT_APPS_PHP_VERSION'), [$newDomain]);
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

    public function deleteAppAndZone()
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

    public function getDirectoryContent($directory)
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

    public function getFileContent($pathname)
    {
        $content = Storage::disk('landings')->get('apps/' . $pathname);
        return $content;
    }

    public function uploadFile($path, $file, $file_pathname)
    {
        Storage::disk('landings')->putFileAs('apps/' . $path, $file, $file_pathname);
    }

    public function createFile($pathname, $contents = '')
    {
        Storage::disk('landings')->put('apps/' . $pathname, $contents);
    }

    public function deleteFile($pathname)
    {
        Storage::disk('landings')->delete('apps/' . $pathname);
    }

    public function createDirectory($pathname)
    {
        Storage::disk('landings')->makeDirectory('apps/' . $pathname);
    }

    public function deleteDirectory($pathname)
    {
        Storage::disk('landings')->deleteDirectory('apps/' . $pathname);
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
}
