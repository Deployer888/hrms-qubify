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
        Schema::table('aadhaar_details', function (Blueprint $table) {
            // Add composite index for faster department-based searches
            $table->index(['employee_id', DB::raw('photo_encoded_optimized(255)')], 'idx_employee_photo_search');
            
            // Add index on employee_id if not exists
            if (!Schema::hasIndex('aadhaar_details', 'aadhaar_details_employee_id_index')) {
                $table->index('employee_id');
            }
        });
        
        // Add full-text index for better search performance (MySQL only)
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE aadhaar_details ADD FULLTEXT(photo_encoded_optimized)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aadhaar_details', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_employee_photo_search');
            
            // Drop full-text index if exists
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                try {
                    DB::statement('ALTER TABLE aadhaar_details DROP INDEX photo_encoded_optimized');
                } catch (\Exception $e) {
                    // Index might not exist, continue
                }
            }
        });
    }
};
