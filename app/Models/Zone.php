<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'latitude',
        'longitude',
        'radius',
        'type',
    ];
    public function isInside(float $latitude, float $longitude): bool {
        // Implement logic to determine if the given coordinates are inside the zone
        $earthRadius = 6371000; // Earth's radius in meters

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = sin($latDelta / 2)**2 + cos($latFrom) * cos($latTo) * sin($lonDelta / 2)**2;
        $c = 2 * atan2(sqrt($angle), sqrt(1 - $angle));

        $distance = $earthRadius * $c;
        return $distance <= $this->radius; // Assume it's inside if within the zone's radius
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
