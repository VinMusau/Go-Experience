<?php

namespace App\Http\Controllers;

use App\Models\LocationPing;
use App\Models\Tag;
use App\Models\Zone;
use App\Events\DependantLocationUpdated;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'device_id' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'battery_level' => 'nullable|integer',
        ]);

        // Find the tag in our DB by its  associated  device_id
        $tag = Tag::where('device_id', $validatedData['device_id'])->first();
        if (!$tag) {
            return response()->json(['message' => 'Tag not found for the provided device_id'], 404);
        }

        // Create a new LocationPing record
        $ping =  $tag->pings()->create([
            'latitude' => $validatedData['latitude'],
            'longitude' => $validatedData['longitude'],
        ]);

        // check if the ping is inside any of the zones associated with the tag's dependant
        $currentZone = null;

        $zones = Zone::all();
        foreach ($zones as $zone) {
            if ($zone->isInside($ping->latitude, $ping->longitude)) {
                $currentZone = $zone;
                break;
            }
        }

        if ($currentZone) {
            Notification::create([
                'user_id' => $tag->dependant->user_id,
                'dependant_name' => $tag->dependant->name,
                'message' => "Hello, {$tag->dependant->name} has entered the zone: {$currentZone->name}",
                'type' => 'success',
            ]);
        }
          

        //update the tag's status
        $tag->update([
            'battery_level' => $validatedData['battery_level'] ?? $tag->battery_level,
            'last_ping_at' => now(),
        ]);

        event(new DependantLocationUpdated(
            tag: $tag,
            latitude: $ping->latitude,
            longitude: $ping->longitude,
            zoneName: $currentZone? $currentZone->name : null,
           // zoneType: $currentZone?->type,
        ));

        // Trigger a websocket broadcast to notify the frontend of the new ping
        return response()->json([
            'status' => 'success',
            'dependant' => $tag->dependant->name,
            'zone' => $currentZone ? $currentZone->name : 'Unknown/In Transit',
            'type' => $currentZone ? $currentZone->type : 'unknown',
            'battery_level' => $tag->battery_level,
            // 'ping' => $ping,
        ]);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(LocationPing $locationPing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LocationPing $locationPing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LocationPing $locationPing)
    {
        //
    }
}
