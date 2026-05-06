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

   // return (int) $user->id === (int) Dependant::find($id)->user_id;
   return $user->dependants()->where('id', $id)->exists();
}, ['guards' => ['api']]);