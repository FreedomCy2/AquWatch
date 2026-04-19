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
        Schema::create('flood_readings', function (Blueprint $table): void {
            $table->id();
            $table->string('sensor_id', 100);
            $table->string('status', 50);
            $table->boolean('s1_wet')->default(false);
            $table->boolean('s2_wet')->default(false);
            $table->boolean('s3_wet')->default(false);
            $table->unsignedInteger('rise_time_sec')->default(0);
            $table->timestamp('measured_at')->nullable()->index();
            $table->timestamps();

            $table->index(['sensor_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flood_readings');
    }
};
