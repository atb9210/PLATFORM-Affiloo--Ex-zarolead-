<?php

namespace Driverfury\ZaroCloner\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Imtigger\LaravelJobStatus\Trackable;

use Spatie\Ssh\Ssh;

class ZaroClonerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    private $webpage_url;
    private $path;
    private $new_url;
    private $form_id;
    private $landing_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $webpage_url, string $path, string $new_url = '', string $form_id = '', string $landing_id = '')
    {
        $this->prepareStatus();
        $this->webpage_url = $webpage_url;
        $this->path = $path;
        $this->new_url = $new_url;
        $this->form_id = $form_id;
        $this->landing_id = $landing_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $command = 'python3 ' . env('ZAROCLONER_PATH') . '/zarocloner.py ' .
                $this->webpage_url . ' ' .
                $this->path . ' ' .
                $this->new_url;
        if ($form_id != '' && $landing_id != '') {
            $command .= ' ' .
                $this->form_id . ' ' .
                $this->landing_id;
        }
        Ssh::create(env('SFTP_USER'), env('SFTP_HOST'))
            ->execute($command);
    }
}
