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
        Schema::table('attendance_employees', function (Blueprint $table) {
            $table->bigInteger('batch_id')->nullable();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('empcode2', 255)->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_employees', function (Blueprint $table) {
            $table->dropColumn('batch_id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('empcode2');
        });
    }
};