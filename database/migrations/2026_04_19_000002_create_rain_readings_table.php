<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rain_readings', function (Blueprint $table) {
            $table->id();
            $table->string('sensor_id', 100)->index();
            $table->unsignedSmallInteger('analog_value');
            $table->string('intensity_level', 20)->index();
            $table->timestamp('measured_at')->index();
            $table->timestamps();

            $table->index(['sensor_id', 'measured_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rain_readings');
    }
};
