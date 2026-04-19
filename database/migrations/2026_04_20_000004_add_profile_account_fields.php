<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('role')->nullable()->after('birth_date');
            $table->string('preferred_language', 10)->nullable()->after('role');
            $table->string('district')->nullable()->after('country');
            $table->string('mukim')->nullable()->after('district');
            $table->decimal('latitude', 10, 6)->nullable()->after('mukim');
            $table->decimal('longitude', 10, 6)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'preferred_language',
                'district',
                'mukim',
                'latitude',
                'longitude',
            ]);
        });
    }
};
