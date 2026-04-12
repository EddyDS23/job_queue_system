<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;

class HealthController extends Controller
{

    function check(): JsonResponse{

        try {
            DB::connection()->getPdo();
            $statusDB="connected";
        } catch (\Throwable $th) {
            $statusDB="disconnected";
        }

        try {
            Redis::command('ping');
            $statusRedis="connected";
        } catch (\Throwable $th) {
            $statusRedis="disconnected";
        }


        $code = ($statusDB == 'disconnected' || $statusRedis == 'disconnected')? 503 : 200;

        $masters = app(MasterSupervisorRepository::class)->all();
        $statusHorizon = !empty($masters) ? $masters[0]->status : 'inactive';

        return response()->json([
            'status'=>'ok',
            'database'=>$statusDB,
            'redis'=>$statusRedis,
            'horizon'=>$statusHorizon,
            'timestamp'=>now()->toISOString()
        ],$code);
    }

}
