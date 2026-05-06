<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'dependant_id',
        'type',
        'location_name',
        'latitude',
        'longitude',
    ];

    public function dependant()
    {
        return $this->belongsTo(Dependant::class);
    }
}
