<?php

namespace Driverfury\ZaroCloner;

use Driverfury\ZaroCloner\Jobs\ZaroClonerJob;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Symfony\Component\Process\Process;
use Imtigger\LaravelJobStatus\JobStatus;

class ZaroCloner
{
    use DispatchesJobs;

    public function executeAsync(string $url, string $path)
    {
        $job = new ZaroClonerJob($url, $path);
        $job->onQueue('zarocloner');
        $this->dispatch($job);
        $jobStatus = JobStatus::find($job->getJobStatusId());
        $jobId = $jobStatus->job_id;

        // This command is used to run jobs in queue:
        //    php artisan queue:work --queue=zarocloner --stop-when-empty
        $command = 'php ../artisan queue:work --queue=zarocloner --stop-when-empty &';
        $process = Process::fromShellCommandline($command);
        $process->start();

        return $jobId;
    }
}
