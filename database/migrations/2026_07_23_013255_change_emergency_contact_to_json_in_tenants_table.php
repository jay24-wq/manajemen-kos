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
        Schema::table('tenants', function (Blueprint $table) {
            DB::statement('ALTER TABLE tenants ALTER COLUMN emergency_contact TYPE jsonb USING emergency_contact::jsonb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            DB::statement('ALTER TABLE tenants ALTER COLUMN emergency_contact TYPE varchar(255)');
        });
    }
};
