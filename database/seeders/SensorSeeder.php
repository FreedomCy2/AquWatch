<?php

namespace Database\Seeders;

use App\Models\Sensor;
use Illuminate\Database\Seeder;

class SensorSeeder extends Seeder
{
    /**
     * Seed test sensors into the database.
     */
    public function run(): void
    {
        Sensor::updateOrCreate(
            ['sensor_id' => 'flow-s1'],
            [
                'sensor_type' => 'flow',
                'label' => 'Flow Sensor 1',
                'is_active' => true,
            ]
        );

        Sensor::updateOrCreate(
            ['sensor_id' => 'flow-s2'],
            [
                'sensor_type' => 'flow',
                'label' => 'Flow Sensor 2',
                'is_active' => true,
            ]
        );

        Sensor::updateOrCreate(
            ['sensor_id' => 'rain-sensor'],
            [
                'sensor_type' => 'rain',
                'label' => 'Rain Sensor',
                'is_active' => true,
            ]
        );

        Sensor::updateOrCreate(
            ['sensor_id' => 'flood-sensor'],
            [
                'sensor_type' => 'flood',
                'label' => 'Flood Sensor',
                'is_active' => true,
            ]
        );
    }
}
