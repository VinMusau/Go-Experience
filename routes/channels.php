<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Dependant;

/*
Broadcast::channel('dependant.{id}', function ($user, $id) {
    $dependant = Dependant::find($id);
    if (!$dependant) {
        return true;
    }

    return (int) $user->id === (int) Dependant::find($id)->user_id;
}, ['guards' => ['api']]);

*/
Broadcast::channel('dependant.{id}', function ($user, $id) {
    // 1. Debug: Log the attempt to your laravel.log
    \Log::info("Auth Attempt - User: {$user->id}, Requesting Child ID: {$id}");

    $dependant = Dependant::find($id);

    if (!$dependant) {
        return false;
    }

    return (int) $user->id === (int) $dependant->user_id;
}, ['guards' => ['api']]);