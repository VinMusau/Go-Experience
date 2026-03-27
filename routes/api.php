<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DependantController;
use App\Http\Controllers\AuthController;


Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('dependants', DependantController::class);

    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/dependants/{dependant}/avatar', [DependantController::class, 'updateAvatar']);
});

