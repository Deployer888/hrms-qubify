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
        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('is_team_leader')->default(0); // Add is_team_leader column with default value 0
            $table->unsignedBigInteger('team_leader_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('is_team_leader'); // Drop the is_team_leader column
            $table->dropColumn('team_leader_id'); // Drop the team_leader_id column
        });
    }
};
