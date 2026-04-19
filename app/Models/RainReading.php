<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RainReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'sensor_id',
        'analog_value',
        'intensity_level',
        'measured_at',
    ];

    protected function casts(): array
    {
        return [
            'analog_value' => 'integer',
            'measured_at' => 'immutable_datetime',
        ];
    }
}
