<?php

namespace Database\Seeders;

use App\Models\Sensor;
use Illuminate\Database\Seeder;

class SensorSeeder extends Seeder
{
    /**
     * Seed the real AquWatch sensors into the database.
     */
    public function run(): void
    {
        Sensor::query()
            ->whereIn('sensor_id', [
                'flow-s1',
                'flow-s2',
                'rain-sensor',
                'flood-sensor',
            ])
            ->delete();

        Sensor::updateOrCreate(
            ['sensor_id' => 'flow-esp32-p27'],
            [
                'sensor_type' => 'flow',
                'label' => 'Flow Sensor A',
                'is_active' => true,
            ]
        );

        Sensor::updateOrCreate(
            ['sensor_id' => 'flow-esp32-p26'],
            [
                'sensor_type' => 'flow',
                'label' => 'Flow Sensor B',
                'is_active' => true,
            ]
        );

        Sensor::updateOrCreate(
            ['sensor_id' => 'rain-esp32-01'],
            [
                'sensor_type' => 'rain',
                'label' => 'Rain Sensor',
                'is_active' => true,
            ]
        );

        Sensor::updateOrCreate(
            ['sensor_id' => 'flood-esp32-01'],
            [
                'sensor_type' => 'flood',
                'label' => 'Flood Sensor',
                'is_active' => true,
            ]
        );
    }
}
