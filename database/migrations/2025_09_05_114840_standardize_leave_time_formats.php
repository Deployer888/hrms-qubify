<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Leave;
use App\Helpers\TimeFormatHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create backup table first
        $this->createBackupTable();
        
        // Standardize existing time formats
        $this->standardizeTimeFormats();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore from backup if needed
        $this->restoreFromBackup();
    }

    /**
     * Create backup table for existing leave data
     */
    private function createBackupTable(): void
    {
        Schema::create('leaves_backup_time_migration', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_leave_id');
            $table->string('original_start_time')->nullable();
            $table->string('original_end_time')->nullable();
            $table->string('standardized_start_time')->nullable();
            $table->string('standardized_end_time')->nullable();
            $table->timestamp('migrated_at');
            
            $table->index('original_leave_id');
        });
    }

    /**
     * Standardize time formats for existing leave records
     */
    private function standardizeTimeFormats(): void
    {
        Log::info('Starting leave time format standardization migration');
        
        // Get all leaves with time data
        $leavesWithTimes = DB::table('leaves')
            ->whereNotNull('start_time')
            ->orWhereNotNull('end_time')
            ->get();

        $processedCount = 0;
        $errorCount = 0;

        foreach ($leavesWithTimes as $leave) {
            try {
                $originalStartTime = $leave->start_time;
                $originalEndTime = $leave->end_time;
                
                $standardizedStartTime = null;
                $standardizedEndTime = null;

                // Process start time
                if (!empty($originalStartTime)) {
                    $standardizedStartTime = TimeFormatHelper::standardizeTimeInput($originalStartTime);
                }

                // Process end time
                if (!empty($originalEndTime)) {
                    $standardizedEndTime = TimeFormatHelper::standardizeTimeInput($originalEndTime);
                }

                // Create backup record
                DB::table('leaves_backup_time_migration')->insert([
                    'original_leave_id' => $leave->id,
                    'original_start_time' => $originalStartTime,
                    'original_end_time' => $originalEndTime,
                    'standardized_start_time' => $standardizedStartTime,
                    'standardized_end_time' => $standardizedEndTime,
                    'migrated_at' => now(),
                ]);

                // Update the leave record with standardized times
                $updateData = [];
                
                if ($standardizedStartTime !== null) {
                    $updateData['start_time'] = $standardizedStartTime;
                }
                
                if ($standardizedEndTime !== null) {
                    $updateData['end_time'] = $standardizedEndTime;
                }

                if (!empty($updateData)) {
                    DB::table('leaves')
                        ->where('id', $leave->id)
                        ->update($updateData);
                }

                $processedCount++;

            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Failed to standardize time for leave ID {$leave->id}: " . $e->getMessage(), [
                    'leave_id' => $leave->id,
                    'original_start_time' => $leave->start_time,
                    'original_end_time' => $leave->end_time,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("Leave time format standardization completed", [
            'total_records' => $leavesWithTimes->count(),
            'processed_successfully' => $processedCount,
            'errors' => $errorCount
        ]);
    }

    /**
     * Restore from backup if needed
     */
    private function restoreFromBackup(): void
    {
        if (Schema::hasTable('leaves_backup_time_migration')) {
            Log::info('Restoring leave times from backup');
            
            $backupRecords = DB::table('leaves_backup_time_migration')->get();
            
            foreach ($backupRecords as $backup) {
                DB::table('leaves')
                    ->where('id', $backup->original_leave_id)
                    ->update([
                        'start_time' => $backup->original_start_time,
                        'end_time' => $backup->original_end_time,
                    ]);
            }
            
            // Drop backup table
            Schema::dropIfExists('leaves_backup_time_migration');
            
            Log::info('Leave times restored from backup successfully');
        }
    }
};
