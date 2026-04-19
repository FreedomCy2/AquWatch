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
        Schema::create('flow_readings', function (Blueprint $table) {
            $table->id();
            $table->string('sensor_id', 100)->index();
            $table->decimal('flow_lpm', 10, 3);
            $table->unsignedBigInteger('total_ml');
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
        Schema::dropIfExists('flow_readings');
    }
};
