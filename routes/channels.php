<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Dependant;

Broadcast::channel('App.Models.Dependant.{id}', function ($user, $id) {
    return (int) $user->id === (int) Dependant::find($id)->user_id;
});
