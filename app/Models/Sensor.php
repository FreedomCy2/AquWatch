<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sensor extends Model
{
    use HasFactory;

    protected $fillable = [
        'sensor_id',
        'sensor_type',
        'label',
        'ingest_token_hash',
        'is_active',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_seen_at' => 'immutable_datetime',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sensor_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
