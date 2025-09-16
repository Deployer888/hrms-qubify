<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoordinatesToOfficeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offices', function (Blueprint $table) {
            // Add latitude and longitude columns if they don't already exist
            if (!Schema::hasColumn('offices', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('location');
            }
            
            if (!Schema::hasColumn('offices', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            
            // Add radius column if it doesn't already exist
            if (!Schema::hasColumn('offices', 'radius')) {
                $table->integer('radius')->nullable()->after('longitude')->comment('Radius in meters');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offices', function (Blueprint $table) {
            // Drop the columns if they exist
            if (Schema::hasColumn('offices', 'latitude')) {
                $table->dropColumn('latitude');
            }
            
            if (Schema::hasColumn('offices', 'longitude')) {
                $table->dropColumn('longitude');
            }
            
            if (Schema::hasColumn('offices', 'radius')) {
                $table->dropColumn('radius');
            }
        });
    }
}