<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use App\Models\JobStatus;

class ProcessEmailJob implements ShouldQueue
{
    use Queueable;
    
    

    /**
     * Create a new job instance.
     */
    public function __construct(private string $email,private string $jobId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {   
    
        $job = JobStatus::where('job_id', $this->jobId)->first();
        
        if($job->status == 'cancelled'){
            return;
        }
        
        $job->status='processing';

        $job->save();

        Log::info("Email Enviado a $this->email");

        $job->status = 'completed';

        $job->save();
    }

    public function failed(\Throwable $exception):void{
        JobStatus::where('job_id',$this->jobId)
            ->first()
            ->update(['status'=>'failed']);
    }


}
