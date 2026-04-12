<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class InformationController extends Controller
{
    function info():JsonResponse{
        return response()->json(['name_app'=>env('APP_NAME'),'environment_current'=>env('APP_ENV')]);
    }
}
