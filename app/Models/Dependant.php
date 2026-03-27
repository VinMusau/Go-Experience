<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dependant extends Model
{
    protected $fillable = [
        'user_id', 'name', 'date_of_birth', 'avatar', 'gender', 'school_name', 'grade'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
