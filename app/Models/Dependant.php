<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Event;

class Dependant extends Model
{
    protected $fillable = [
        'user_id', 
        'name', 
        'date_of_birth', 
        'avatar', 
        'gender', 
        'school_name', 
        'grade', 
        'blood_group', 
        'allergies', 
        'doctor_contact', 
        'insurance_provider', 
        'tag_id'
    ];

    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return "https://i.pravatar.cc/150?u= " . $this->id;
        }
        return Storage::disk('public')->url($this->avatar);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tag(){
        return $this->hasOne(Tag::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class)->latest();
    }
}
