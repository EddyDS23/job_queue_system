<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\JsonResponse;

class HealthController extends Controller
{

    function check(): JsonResponse{
        return response()->json(['status'=>'ok']);
    }

}
