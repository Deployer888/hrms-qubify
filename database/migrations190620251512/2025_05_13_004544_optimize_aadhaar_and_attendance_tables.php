<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class OptimizeAadhaarAndAttendanceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add columns to aadhaar_details table
        Schema::table('aadhaar_details', function (Blueprint $table) {
            // Check if photo_encoded_optimized column doesn't exist before adding
            if (!Schema::hasColumn('aadhaar_details', 'photo_encoded_optimized')) {
                $table->text('photo_encoded_optimized')->nullable()->after('photo_encoded');
            }
            
            // Check if is_active column doesn't exist before adding
            if (!Schema::hasColumn('aadhaar_details', 'is_active')) {
                $table->boolean('is_active')->default(1)->after('photo_encoded_optimized');
            }
        });

        // Add indexes using raw SQL with try-catch blocks to handle cases where index might already exist
        
        // Index on employee_id in aadhaar_details
        try {
            DB::statement('CREATE INDEX idx_aadhar_employee_id ON aadhaar_details(employee_id)');
        } catch (\Exception $e) {
            // Index might already exist, that's fine
        }
        
        // Index on is_active in aadhaar_details
        try {
            DB::statement('CREATE INDEX idx_aadhar_is_active ON aadhaar_details(is_active)');
        } catch (\Exception $e) {
            // Index might already exist, that's fine
        }
        
        // Index on employee_id and date in attendance_employee
        try {
            DB::statement('CREATE INDEX idx_attendance_employee_date ON attendance_employee(employee_id, date)');
        } catch (\Exception $e) {
            // Index might already exist, that's fine
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove columns from aadhaar_details table
        Schema::table('aadhaar_details', function (Blueprint $table) {
            if (Schema::hasColumn('aadhaar_details', 'photo_encoded_optimized')) {
                $table->dropColumn('photo_encoded_optimized');
            }
            
            if (Schema::hasColumn('aadhaar_details', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });

        // Drop indexes using raw SQL with try-catch blocks to handle cases where index might not exist
        try {
            DB::statement('DROP INDEX idx_aadhar_employee_id ON aadhaar_details');
        } catch (\Exception $e) {
            // Index might not exist, that's fine
        }
        
        try {
            DB::statement('DROP INDEX idx_aadhar_is_active ON aadhaar_details');
        } catch (\Exception $e) {
            // Index might not exist, that's fine
        }
        
        try {
            DB::statement('DROP INDEX idx_attendance_employee_date ON attendance_employee');
        } catch (\Exception $e) {
            // Index might not exist, that's fine
        }
    }
}