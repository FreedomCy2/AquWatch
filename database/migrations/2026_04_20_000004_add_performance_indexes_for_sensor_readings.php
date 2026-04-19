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
        Schema::table('flow_readings', function (Blueprint $table): void {
            $table->index('created_at', 'flow_readings_created_at_index');
        });

        Schema::table('rain_readings', function (Blueprint $table): void {
            $table->index('created_at', 'rain_readings_created_at_index');
            $table->index(['intensity_level', 'created_at'], 'rain_level_created_at_index');
        });

        Schema::table('flood_readings', function (Blueprint $table): void {
            $table->index('created_at', 'flood_readings_created_at_index');
            $table->index(['status', 'created_at'], 'flood_status_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flow_readings', function (Blueprint $table): void {
            $table->dropIndex('flow_readings_created_at_index');
        });

        Schema::table('rain_readings', function (Blueprint $table): void {
            $table->dropIndex('rain_readings_created_at_index');
            $table->dropIndex('rain_level_created_at_index');
        });

        Schema::table('flood_readings', function (Blueprint $table): void {
            $table->dropIndex('flood_readings_created_at_index');
            $table->dropIndex('flood_status_created_at_index');
        });
    }
};
