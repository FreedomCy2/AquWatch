<?php

namespace App\Http\Controllers;

use App\Models\FlowReading;
use App\Models\FloodReading;
use App\Models\RainReading;
use App\Models\Sensor;
use App\Services\AutoSensorAlertService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SensorIngestionController extends Controller
{
    private function hasValidToken(Request $request, string $sensorId = ''): bool
    {
        $configuredToken = (string) config('services.sensors.ingest_token', '');
        $providedToken = (string) $request->header('X-Sensor-Token', '');

        if ($providedToken === '') {
            return false;
        }

        $acceptLegacyToken = (bool) config('services.sensors.accept_legacy_token', true);

        // If legacy token is accepted but not configured, allow any token for bootstrapping new sensors
        if ($acceptLegacyToken && $configuredToken === '') {
            return true;
        }

        // If legacy token is configured, check it
        if ($acceptLegacyToken && $configuredToken !== '' && hash_equals($configuredToken, $providedToken)) {
            return true;
        }

        if ($sensorId === '') {
            return false;
        }

        // Check sensor-specific token
        $sensor = Sensor::query()
            ->where('sensor_id', $sensorId)
            ->where('is_active', true)
            ->first();

        if (! $sensor || ! is_string($sensor->ingest_token_hash) || $sensor->ingest_token_hash === '') {
            return false;
        }

        return hash_equals($sensor->ingest_token_hash, hash('sha256', $providedToken));
    }

    private function touchSensor(string $sensorId, string $sensorType): void
    {
        $sensor = Sensor::query()->firstOrNew(['sensor_id' => $sensorId]);

        if (! $sensor->exists) {
            $sensor->sensor_type = $sensorType;
            $sensor->is_active = true;
        }

        if (! is_string($sensor->sensor_type) || $sensor->sensor_type === '') {
            $sensor->sensor_type = $sensorType;
        }

        $sensor->last_seen_at = CarbonImmutable::now();
        $sensor->save();
    }

    public function storeFlow(Request $request): JsonResponse
    {
        $sensorId = (string) $request->input('sensor_id', '');

        if (! $this->hasValidToken($request, $sensorId)) {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthorized sensor token.',
            ], 401);
        }

        $validated = $request->validate([
            'sensor_id' => ['required', 'string', 'max:100'],
            'flow_lpm' => ['required', 'numeric', 'min:0', 'max:10000'],
            'total_ml' => ['required', 'integer', 'min:0'],
            'measured_at' => ['nullable', 'date'],
        ]);

        $reading = FlowReading::create([
            'sensor_id' => $validated['sensor_id'],
            'flow_lpm' => (float) $validated['flow_lpm'],
            'total_ml' => (int) $validated['total_ml'],
            'measured_at' => isset($validated['measured_at'])
                ? CarbonImmutable::parse($validated['measured_at'])
                : CarbonImmutable::now(),
        ]);

        $this->touchSensor((string) $validated['sensor_id'], 'flow');

        return response()->json([
            'ok' => true,
            'id' => $reading->id,
            'received_at' => $reading->created_at?->toIso8601String(),
        ]);
    }

    public function storeRain(Request $request, AutoSensorAlertService $autoSensorAlert): JsonResponse
    {
        $sensorId = (string) $request->input('sensor_id', '');

        if (! $this->hasValidToken($request, $sensorId)) {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthorized sensor token.',
            ], 401);
        }

        $validated = $request->validate([
            'sensor_id' => ['required', 'string', 'max:100'],
            'analog_value' => ['required', 'integer', 'min:0', 'max:4095'],
            'intensity_level' => ['required', 'string', 'in:no_rain,rain,heavy_rain'],
            'measured_at' => ['nullable', 'date'],
        ]);

        $reading = RainReading::create([
            'sensor_id' => $validated['sensor_id'],
            'analog_value' => (int) $validated['analog_value'],
            'intensity_level' => $validated['intensity_level'],
            'measured_at' => isset($validated['measured_at'])
                ? CarbonImmutable::parse($validated['measured_at'])
                : CarbonImmutable::now(),
        ]);

        $this->touchSensor((string) $validated['sensor_id'], 'rain');
        $autoSensorAlert->onRainReading($reading);

        return response()->json([
            'ok' => true,
            'id' => $reading->id,
            'received_at' => $reading->created_at?->toIso8601String(),
        ]);
    }

    public function storeFlood(Request $request, AutoSensorAlertService $autoSensorAlert): JsonResponse
    {
        $sensorId = (string) $request->input('sensor_id', '');

        if (! $this->hasValidToken($request, $sensorId)) {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthorized sensor token.',
            ], 401);
        }

        $validated = $request->validate([
            'sensor_id' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:SAFE / DRY,LEVEL 1 DETECTED,NORMAL RISE,FLASH FLOOD WARNING,CRITICAL'],
            's1_wet' => ['required', 'boolean'],
            's2_wet' => ['required', 'boolean'],
            's3_wet' => ['required', 'boolean'],
            'rise_time_sec' => ['required', 'integer', 'min:0', 'max:86400'],
            'measured_at' => ['nullable', 'date'],
        ]);

        $reading = FloodReading::create([
            'sensor_id' => $validated['sensor_id'],
            'status' => $validated['status'],
            's1_wet' => (bool) $validated['s1_wet'],
            's2_wet' => (bool) $validated['s2_wet'],
            's3_wet' => (bool) $validated['s3_wet'],
            'rise_time_sec' => (int) $validated['rise_time_sec'],
            'measured_at' => isset($validated['measured_at'])
                ? CarbonImmutable::parse($validated['measured_at'])
                : CarbonImmutable::now(),
        ]);

        $this->touchSensor((string) $validated['sensor_id'], 'flood');
        $autoSensorAlert->onFloodReading($reading);

        return response()->json([
            'ok' => true,
            'id' => $reading->id,
            'received_at' => $reading->created_at?->toIso8601String(),
        ]);
    }
}
