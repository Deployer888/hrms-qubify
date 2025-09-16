<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeBalanceController extends Controller
{
    /**
     * Get employee leave balances for all leave types
     */
    public function getEmployeeLeaveBalances(Request $request): JsonResponse
    {
        $employeeId = $request->get('employee_id');
        
        if (!$employeeId) {
            return response()->json(['error' => 'Employee ID is required'], 400);
        }
        
        $employee = Employee::find($employeeId);
        
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }
        
        // Get all leave types
        $leaveTypes = LeaveType::where('created_by', $employee->created_by)->get();
        $leaveBalances = [];
        
        foreach ($leaveTypes as $leaveType) {
            // Skip gender-specific leave types
            if ($employee->gender == "Male" && $leaveType->title == "Maternity Leaves") {
                continue;
            }
            if ($employee->gender == "Female" && $leaveType->title == "Paternity Leaves") {
                continue;
            }
            
            // Calculate available balance based on leave type
            if ($leaveType->title == 'Paid Leave') {
                // Use real-time balance calculation for paid leave
                if ($employee->is_probation == 1) {
                    $availableBalance = 0;
                } else {
                    $breakdown = $employee->getBalanceBreakdown();
                    $availableBalance = $breakdown['current_balance'];
                }
            } else {
                // Use existing logic for other leave types
                $totalLeaves = $leaveType->days;
                
                // Adjust for probation period
                if ($employee->is_probation == 1 && $leaveType->title != 'Sick Leave') {
                    $totalLeaves = max(0, $totalLeaves - 2);
                }
                
                // Calculate taken leaves using Helper
                $leavesAvailed = \App\Helpers\Helper::totalLeaveAvailed(
                    $employee->id, 
                    $employee->company_doj, 
                    now()->format('Y-m-d'), 
                    $leaveType->id
                );
                
                $availableBalance = max(0, $totalLeaves - $leavesAvailed);
            }
            
            $leaveBalances[] = [
                'id' => $leaveType->id,
                'title' => $leaveType->title,
                'available_balance' => $availableBalance,
                'display_text' => $leaveType->title . ' (' . $availableBalance . ')'
            ];
        }
        
        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'is_probation' => $employee->is_probation
            ],
            'leave_balances' => $leaveBalances
        ]);
    }
}