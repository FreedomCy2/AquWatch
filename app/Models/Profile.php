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
        'company',
        'job_title',
        'website',
        'twitter',
        'linkedin',
        'github',
        'preferences',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'preferences' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}