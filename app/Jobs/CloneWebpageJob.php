<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use \Imtigger\LaravelJobStatus\Trackable;

use Spatie\Ssh\Ssh;

class CloneWebpageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    private $webpage_url;
    private $path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $webpage_url, string $path)
    {
        $this->prepareStatus();
        $this->webpage_url = $webpage_url;
        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Ssh::create(env('SFTP_USER'), env('SFTP_HOST'))
            ->execute(
                'python3 ' . env('ZAROCLONER_PATH') . '/zarocloner.py ' .
                $this->webpage_url . ' ' .
                $this->path . ' >> ' . env('ZAROCLONER_PATH') . '/error.log 2>&1'
            );
    }
}
