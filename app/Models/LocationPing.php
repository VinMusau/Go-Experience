<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationPing extends Model
{
    protected $fillable = [
        'tag_id', 'latitude', 'longitude', 'speed'
    ];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
