<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeDataToAttendanceEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_employees', function (Blueprint $table) {
            $table->string('working_location')->nullable()->after('status');
            $table->text('working_location_detail')->nullable()->after('working_location');
            $table->decimal('clock_in_latitude', 10, 7)->nullable()->after('working_location_detail');
            $table->decimal('clock_in_longitude', 10, 7)->nullable()->after('clock_in_latitude');
            $table->decimal('clock_out_latitude', 10, 8)->nullable()->after('clock_in_longitude');
            $table->decimal('clock_out_longitude', 10, 8)->nullable()->after('clock_out_latitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_employees', function (Blueprint $table) {
            $table->dropColumn('working_location');
            $table->dropColumn('working_location_detail');
            $table->dropColumn('clock_in_latitude');
            $table->dropColumn('clock_in_longitude');
            $table->dropColumn('clock_out_latitude');
            $table->dropColumn('clock_out_longitude');
        });
    }
}