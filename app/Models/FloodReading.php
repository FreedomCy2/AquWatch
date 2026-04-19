<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FloodReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'sensor_id',
        'status',
        's1_wet',
        's2_wet',
        's3_wet',
        'rise_time_sec',
        'measured_at',
    ];

    protected function casts(): array
    {
        return [
            's1_wet' => 'boolean',
            's2_wet' => 'boolean',
            's3_wet' => 'boolean',
            'rise_time_sec' => 'integer',
            'measured_at' => 'immutable_datetime',
        ];
    }
}
