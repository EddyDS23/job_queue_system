<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Endpoints de autenticacion y registro
Route::post('/register',[AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//Endpoints de informacion
Route::get('/health', [HealthController::class, 'check']);
Route::get('/info', [InformationController::class,'info']);

//Proteccion de rutas <Solo usuarios autenticados>
Route::middleware('auth:sanctum')->group(function(){
    //Endpoints para hacer y consultar un proceso de cola
    Route::post('/email', [EmailController::class,'send']);
    Route::get('/jobs/{jobId}',[EmailController::class, 'status']);
    Route::delete('/jobs/{jobId}', [EmailController::class, 'cancel']);
});

