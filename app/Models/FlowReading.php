<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'sensor_id',
        'flow_lpm',
        'total_ml',
        'measured_at',
    ];

    protected function casts(): array
    {
        return [
            'flow_lpm' => 'float',
            'total_ml' => 'integer',
            'measured_at' => 'immutable_datetime',
        ];
    }
}
