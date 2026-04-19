<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sensors', function (Blueprint $table): void {
            $table->id();
            $table->string('sensor_id', 100)->unique();
            $table->string('sensor_type', 20)->index();
            $table->string('label', 120)->nullable();
            $table->string('ingest_token_hash', 64)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamps();

            $table->index(['sensor_type', 'is_active']);
        });

        Schema::create('sensor_user', function (Blueprint $table): void {
            $table->foreignId('sensor_id')->constrained('sensors')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20)->default('viewer');
            $table->timestamps();

            $table->primary(['sensor_id', 'user_id']);
            $table->index(['user_id', 'role']);
        });

        $this->backfillSensorsFromReadings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_user');
        Schema::dropIfExists('sensors');
    }

    private function backfillSensorsFromReadings(): void
    {
        $now = now();

        $this->insertSensorIds('flow_readings', 'flow', $now);
        $this->insertSensorIds('rain_readings', 'rain', $now);
        $this->insertSensorIds('flood_readings', 'flood', $now);
    }

    private function insertSensorIds(string $table, string $type, $now): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $sensorIds = DB::table($table)
            ->select('sensor_id')
            ->whereNotNull('sensor_id')
            ->distinct()
            ->pluck('sensor_id');

        foreach ($sensorIds as $sensorId) {
            if (! is_string($sensorId) || trim($sensorId) === '') {
                continue;
            }

            DB::table('sensors')->insertOrIgnore([
                'sensor_id' => trim($sensorId),
                'sensor_type' => $type,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};
