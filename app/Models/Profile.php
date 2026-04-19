<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'photo',
        'phone',
        'bio',
        'address',
        'city',
        'country',
        'birth_date',
        'role',
        'preferred_language',
        'company',
        'job_title',
        'district',
        'mukim',
        'latitude',
        'longitude',
        'website',
        'twitter',
        'linkedin',
        'github',
        'preferences',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
        'preferences' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}