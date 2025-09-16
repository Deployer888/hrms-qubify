<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;

class PaySlipErrorHandler
{
    /**
     * Handle payslip generation errors.
     */
    public static function handleGenerationError(Exception $e, Request $request = null)
    {
        $context = [
            'action' => 'payslip_generation',
            'user_id' => auth()->id(),
            'creator_id' => auth()->user()->creatorId(),
            'request_data' => $request ? $request->all() : null,
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString()
        ];

        Log::error('Payslip generation failed', $context);

        // Determine user-friendly error message
        $userMessage = self::getUserFriendlyMessage($e, 'generation');

        return [
            'success' => false,
            'message' => $userMessage,
            'error_code' => 'PAYSLIP_GENERATION_FAILED',
            'technical_details' => config('app.debug') ? $e->getMessage() : null
        ];
    }

    /**
     * Handle payslip update errors.
     */
    public static function handleUpdateError(Exception $e, $payslipId = null, Request $request = null)
    {
        $context = [
            'action' => 'payslip_update',
            'payslip_id' => $payslipId,
            'user_id' => auth()->id(),
            'request_data' => $request ? $request->all() : null,
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString()
        ];

        Log::error('Payslip update failed', $context);

        $userMessage = self::getUserFriendlyMessage($e, 'update');

        return [
            'success' => false,
            'message' => $userMessage,
            'error_code' => 'PAYSLIP_UPDATE_FAILED',
            'technical_details' => config('app.debug') ? $e->getMessage() : null
        ];
    }

    /**
     * Handle payslip data loading errors.
     */
    public static function handleDataLoadError(Exception $e, Request $request = null)
    {
        $context = [
            'action' => 'payslip_data_load',
            'user_id' => auth()->id(),
            'request_data' => $request ? $request->all() : null,
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString()
        ];

        Log::error('Payslip data loading failed', $context);

        $userMessage = self::getUserFriendlyMessage($e, 'data_load');

        return [
            'success' => false,
            'message' => $userMessage,
            'error_code' => 'PAYSLIP_DATA_LOAD_FAILED',
            'technical_details' => config('app.debug') ? $e->getMessage() : null
        ];
    }

    /**
     * Handle bulk payment errors.
     */
    public static function handleBulkPaymentError(Exception $e, Request $request = null)
    {
        $context = [
            'action' => 'bulk_payment',
            'user_id' => auth()->id(),
            'request_data' => $request ? $request->all() : null,
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString()
        ];

        Log::error('Bulk payment failed', $context);

        $userMessage = self::getUserFriendlyMessage($e, 'bulk_payment');

        return [
            'success' => false,
            'message' => $userMessage,
            'error_code' => 'BULK_PAYMENT_FAILED',
            'technical_details' => config('app.debug') ? $e->getMessage() : null
        ];
    }

    /**
     * Handle payslip deletion errors.
     */
    public static function handleDeletionError(Exception $e, $payslipId = null)
    {
        $context = [
            'action' => 'payslip_deletion',
            'payslip_id' => $payslipId,
            'user_id' => auth()->id(),
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString()
        ];

        Log::error('Payslip deletion failed', $context);

        $userMessage = self::getUserFriendlyMessage($e, 'deletion');

        return [
            'success' => false,
            'message' => $userMessage,
            'error_code' => 'PAYSLIP_DELETION_FAILED',
            'technical_details' => config('app.debug') ? $e->getMessage() : null
        ];
    }

    /**
     * Handle PDF generation errors.
     */
    public static function handlePdfError(Exception $e, $payslipId = null)
    {
        $context = [
            'action' => 'pdf_generation',
            'payslip_id' => $payslipId,
            'user_id' => auth()->id(),
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString()
        ];

        Log::error('PDF generation failed', $context);

        $userMessage = self::getUserFriendlyMessage($e, 'pdf');

        return [
            'success' => false,
            'message' => $userMessage,
            'error_code' => 'PDF_GENERATION_FAILED',
            'technical_details' => config('app.debug') ? $e->getMessage() : null
        ];
    }

    /**
     * Get user-friendly error message based on exception type and action.
     */
    private static function getUserFriendlyMessage(Exception $e, string $action)
    {
        $message = $e->getMessage();
        $exceptionType = get_class($e);

        // Database related errors
        if (strpos($exceptionType, 'QueryException') !== false || 
            strpos($exceptionType, 'DatabaseException') !== false) {
            return self::getDatabaseErrorMessage($message, $action);
        }

        // Model not found errors
        if (strpos($exceptionType, 'ModelNotFoundException') !== false) {
            return self::getNotFoundErrorMessage($action);
        }

        // Validation errors
        if (strpos($exceptionType, 'ValidationException') !== false) {
            return 'Please check your input data and try again.';
        }

        // Authorization errors
        if (strpos($exceptionType, 'AuthorizationException') !== false || 
            strpos($exceptionType, 'UnauthorizedException') !== false) {
            return 'You do not have permission to perform this action.';
        }

        // File system errors
        if (strpos($exceptionType, 'FileNotFoundException') !== false || 
            strpos($message, 'file') !== false) {
            return 'A required file could not be found or accessed.';
        }

        // Memory or timeout errors
        if (strpos($message, 'memory') !== false || 
            strpos($message, 'timeout') !== false) {
            return 'The operation took too long or used too much memory. Please try with smaller data sets.';
        }

        // Default messages based on action
        return self::getDefaultErrorMessage($action);
    }

    /**
     * Get database-specific error messages.
     */
    private static function getDatabaseErrorMessage(string $message, string $action)
    {
        if (strpos($message, 'Duplicate entry') !== false) {
            return 'A record with this information already exists.';
        }

        if (strpos($message, 'foreign key constraint') !== false) {
            return 'Cannot complete this action due to related data dependencies.';
        }

        if (strpos($message, 'Connection refused') !== false || 
            strpos($message, 'server has gone away') !== false) {
            return 'Database connection issue. Please try again in a moment.';
        }

        return 'A database error occurred. Please try again or contact support if the problem persists.';
    }

    /**
     * Get not found error messages.
     */
    private static function getNotFoundErrorMessage(string $action)
    {
        switch ($action) {
            case 'update':
            case 'deletion':
            case 'pdf':
                return 'The requested payslip could not be found.';
            case 'data_load':
                return 'No payslip data found for the selected criteria.';
            default:
                return 'The requested resource could not be found.';
        }
    }

    /**
     * Get default error messages based on action.
     */
    private static function getDefaultErrorMessage(string $action)
    {
        switch ($action) {
            case 'generation':
                return 'Failed to generate payslips. Please check employee salary configurations and try again.';
            case 'update':
                return 'Failed to update payslip. Please check your input and try again.';
            case 'data_load':
                return 'Failed to load payslip data. Please refresh the page and try again.';
            case 'bulk_payment':
                return 'Failed to process bulk payment. Please try again or process payments individually.';
            case 'deletion':
                return 'Failed to delete payslip. Please try again.';
            case 'pdf':
                return 'Failed to generate PDF. Please try again.';
            default:
                return 'An unexpected error occurred. Please try again or contact support.';
        }
    }

    /**
     * Validate employee salary configuration.
     */
    public static function validateEmployeeSalaryConfig($employees)
    {
        $errors = [];
        $employeesWithoutSalary = [];

        foreach ($employees as $employee) {
            if (!$employee->hasSalaryConfigured()) {
                $employeesWithoutSalary[] = $employee->name;
            }
        }

        if (!empty($employeesWithoutSalary)) {
            $errors[] = 'The following employees do not have salary configured: ' . 
                       implode(', ', $employeesWithoutSalary);
        }

        return $errors;
    }

    /**
     * Validate attendance data for payslip generation.
     */
    public static function validateAttendanceData($employee, $month, $year)
    {
        $warnings = [];

        try {
            $attendanceData = $employee->calculateAttendanceForMonth($month, $year);
            
            if ($attendanceData['loss_of_pay_days'] > 0) {
                $warnings[] = "Employee {$employee->name} has {$attendanceData['loss_of_pay_days']} loss of pay days.";
            }

            if ($attendanceData['actual_payable_days'] == 0) {
                $warnings[] = "Employee {$employee->name} has no payable days for this month.";
            }

        } catch (Exception $e) {
            $warnings[] = "Could not calculate attendance for employee {$employee->name}.";
        }

        return $warnings;
    }

    /**
     * Log successful operations for audit trail.
     */
    public static function logSuccess(string $action, array $data = [])
    {
        $context = array_merge([
            'action' => $action,
            'user_id' => auth()->id(),
            'creator_id' => auth()->user()->creatorId(),
            'timestamp' => now()->toISOString()
        ], $data);

        Log::info("Payslip operation successful: {$action}", $context);
    }

    /**
     * Format error response for AJAX requests.
     */
    public static function formatAjaxErrorResponse(Exception $e, string $action, Request $request = null)
    {
        $errorData = self::handleGenerationError($e, $request);

        return response()->json([
            'success' => false,
            'message' => $errorData['message'],
            'error_code' => $errorData['error_code'],
            'technical_details' => $errorData['technical_details']
        ], 500);
    }

    /**
     * Format error response for web requests.
     */
    public static function formatWebErrorResponse(Exception $e, string $action, string $redirectRoute = 'payslip.index')
    {
        $errorData = self::handleGenerationError($e);

        return redirect()->route($redirectRoute)->with('error', $errorData['message']);
    }
}