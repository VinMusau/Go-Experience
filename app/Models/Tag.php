<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'dependant_id', 'device_id', 'battery_level', 'status', 'last_ping_at'
    ];
    public function dependant(){
        return $this->belongsTo(Dependant::class);
    }

    public function pings(){
        return $this->hasMany(LocationPing::class)->latest();
    }
}
