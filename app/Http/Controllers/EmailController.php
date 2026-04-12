<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Jobs\ProcessEmailJob;
use App\Models\JobStatus;
use PhpOption\None;

class EmailController extends Controller
{
    function send(Request $request){
        $email = $request->input('email');

        $uuid = Str::uuid()->toString();

        JobStatus::create([
            'job_id'=>$uuid,
            'email'=>$email,
            'status'=>'pending'
        ]);

        $job = ProcessEmailJob::dispatch($email,$uuid)->delay(now()->addSecond(15));
        
        return response()->json(['message'=>'Email en cola','job_id'=>$uuid]);
    }

    function status(string $jobId): JsonResponse{

        $job = JobStatus::where('job_id',$jobId)->first();

        if(!$job){
            return response()->json(['message'=>'Job not found'],404);
        }

        return response()->json(['job'=>$job]);
    }

    function cancel(string $job_id):JsonResponse{

        $job = JobStatus::where('job_id',$job_id)->first();

        if($job == null){
            return response()->json(['message'=>'Job not found'],404);
        }

        if($job->status != 'pending' && $job->status != 'cancelled'){
            return response()->json(['message'=>'You cannot delete job in processing'],422);
        }

        if($job->status == 'cancelled'){
            return response()->json(['message'=>'Job has already been canceled'],200);
        }

        $job->status = 'cancelled';
        $job->save();

        return response()->json(['message'=>'Job has been canceled'],200);

    }
}   
