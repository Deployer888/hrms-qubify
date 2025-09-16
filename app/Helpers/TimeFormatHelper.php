<?php

namespace App\Helpers;

use Carbon\Carbon;
use Exception;

class TimeFormatHelper
{
    /**
     * Parse time input from various formats and return Carbon instance
     * 
     * @param string $timeInput
     * @return Carbon|null
     */
    public static function parseTimeInput(string $timeInput): ?Carbon
    {
        if (empty(trim($timeInput))) {
            return null;
        }

        $timeString = trim($timeInput);
        
        try {
            // Try 12-hour format with AM/PM and space (h:i A)
            if (preg_match('/^(1[0-2]|0?[1-9]):[0-5][0-9]\s?(AM|PM)$/i', $timeString)) {
                return Carbon::createFromFormat('h:i A', $timeString);
            }
            
            // Try 12-hour format without space before AM/PM (h:iA)
            if (preg_match('/^(1[0-2]|0?[1-9]):[0-5][0-9](AM|PM)$/i', $timeString)) {
                return Carbon::createFromFormat('h:iA', $timeString);
            }
            
            // Try 24-hour format (H:i) with proper validation
            if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $timeString)) {
                return Carbon::createFromFormat('H:i', $timeString);
            }
            
            return null;
            
        } catch (Exception $e) {
            \Log::warning("TimeFormatHelper: Failed to parse time input: {$timeString}", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Format time for consistent storage (24-hour format)
     * 
     * @param Carbon $time
     * @return string
     */
    public static function formatTimeForStorage(Carbon $time): string
    {
        return $time->format('H:i');
    }

    /**
     * Format stored time for display (12-hour format with AM/PM)
     * 
     * @param string|null $storedTime
     * @return string
     */
    public static function formatTimeForDisplay(?string $storedTime): string
    {
        if (empty($storedTime)) {
            return '';
        }

        try {
            // Try to parse the stored time
            $carbon = self::parseTimeInput($storedTime);
            
            if ($carbon) {
                return $carbon->format('g:i A');
            }
            
            return $storedTime; // Return as-is if parsing fails
            
        } catch (Exception $e) {
            \Log::warning("TimeFormatHelper: Failed to format time for display: {$storedTime}", [
                'error' => $e->getMessage()
            ]);
            return $storedTime;
        }
    }

    /**
     * Calculate end time by adding hours to start time
     * 
     * @param string $startTime
     * @param int $hoursToAdd
     * @return string|null
     */
    public static function calculateEndTime(string $startTime, int $hoursToAdd = 2): ?string
    {
        try {
            $startCarbon = self::parseTimeInput($startTime);
            
            if (!$startCarbon) {
                return null;
            }
            
            $endCarbon = $startCarbon->copy()->addHours($hoursToAdd);
            
            return self::formatTimeForStorage($endCarbon);
            
        } catch (Exception $e) {
            \Log::warning("TimeFormatHelper: Failed to calculate end time", [
                'start_time' => $startTime,
                'hours_to_add' => $hoursToAdd,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Validate if time string is in valid format
     * 
     * @param string $timeInput
     * @return bool
     */
    public static function isValidTimeFormat(string $timeInput): bool
    {
        return self::parseTimeInput($timeInput) !== null;
    }

    /**
     * Convert time input to standardized storage format
     * 
     * @param string $timeInput
     * @return string|null
     */
    public static function standardizeTimeInput(string $timeInput): ?string
    {
        $carbon = self::parseTimeInput($timeInput);
        
        if (!$carbon) {
            return null;
        }
        
        return self::formatTimeForStorage($carbon);
    }

    /**
     * Get formatted time range for display
     * 
     * @param string|null $startTime
     * @param string|null $endTime
     * @return string
     */
    public static function getFormattedTimeRange(?string $startTime, ?string $endTime): string
    {
        if (empty($startTime) || empty($endTime)) {
            return '';
        }

        $formattedStart = self::formatTimeForDisplay($startTime);
        $formattedEnd = self::formatTimeForDisplay($endTime);

        if (empty($formattedStart) || empty($formattedEnd)) {
            return '';
        }

        return "{$formattedStart} to {$formattedEnd}";
    }
}