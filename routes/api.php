<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DependantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PingController;
use Illuminate\Support\Facades\Broadcast;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('dependants', DependantController::class);

    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/dependants/{dependant}/avatar', [DependantController::class, 'updateAvatar']);

    Route::get('/notifications', function (Request $request) {
        $user = $request->user();

        $alerts = $user->alerts()->latest()->take(10)->get();
        return response()->json($alerts);
    });

    Route::post('/notifications/clear', function (Request $request) {
        $request->user()->notifications()->delete();
        return response()->json(['message' => 'Notifications cleared']);
    });
});

Route::post('/iot/ping', [PingController::class, 'store']);

Broadcast::routes(['middleware' => ['auth:api']]);
