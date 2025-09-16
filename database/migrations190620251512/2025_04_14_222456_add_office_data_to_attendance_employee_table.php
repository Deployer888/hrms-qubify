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
            $table->decimal('latitude', 10, 7)->nullable()->after('working_location_detail');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
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
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
}