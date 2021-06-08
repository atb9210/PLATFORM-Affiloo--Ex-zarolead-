<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Spatie\Ssh\Ssh;

// TODO: to delete (this is only for debug)
use Illuminate\Support\Facades\Storage;

class CloneWebpageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $webpage_url;
    private $path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $webpage_url, string $path)
    {
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
                $this->path
            );
    }
}
