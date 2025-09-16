<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Mail\{LeaveActionSend, LeaveRequest};
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Imports\EmployeesImport;
use App\Exports\LeaveExport;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use App\Helpers\Helper;

class LeaveController extends Controller
{
    public function index($eid)
    {
        if (Helper::check_permissions('Manage Leave')) 
        {
            try {
                if (\Auth::user()->type == 'employee') 
                {
                    if (!$eid) {
                        return response()->json([
                            'status' => 'error',
                            'message' => __('Employee not found.')
                        ], 404);
                    }
    
                    $leaves = Leave::where('employee_id', '=', $eid)
                                    ->orderBy('id', 'DESC')
                                    ->get();
                } 
                else 
                {
                    $leaves = Leave::where('created_by', '=', \Auth::user()->creatorId())
                                    ->orderBy('id', 'DESC')
                                    ->get();
                }
    
                return response()->json([
                    'status' => 'success',
                    'data' => $leaves,
                ], 200);
    
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('An error occurred while fetching leaves.'),
                    'error' => $e->getMessage(),
                ], 500);
            }
        } 
        else 
        {
            return response()->json([
                'status' => 'error',
                'message' => __('Permission denied.'),
            ], 403);
        }
    }

    public function create()
    {
        if(\Auth::user()->can('Create Leave'))
        {
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
            $totalLeaveAvailed = '';
            if(Auth::user()->type == 'employee')
            {
                $employees = Employee::where('user_id', '=', \Auth::user()->id)->get()->pluck('name', 'id');
                $employee = Employee::where('user_id', '=', \Auth::user()->id)->first();
                $id = \Auth::user()->id;

                /*$leavetypes = LeaveType::leftJoin('employees', function($join) use ($id) {
                                    $join->on('employees.id', '=', DB::raw($id));
                                })
                                ->leftJoin('leaves', function($join) use ($employee) {
                                    $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                                         ->where('leaves.employee_id', '=', $employee->id);
                                })
                                ->where('leave_types.created_by', '=', \Auth::user()->creatorId())
                                ->select(
                                    'leave_types.id',
                                    'leave_types.title',
                                    DB::raw('
                                        CASE
                                            WHEN leave_types.title = "Paid Leave" THEN employees.paid_leave_balance
                                            ELSE (leave_types.days - COALESCE(SUM(leaves.total_leave_days), 0))
                                        END AS days
                                    ')
                                )
                                ->groupBy('leave_types.id', 'leave_types.title', 'employees.paid_leave_balance', 'leave_types.days')
                                ->get();*/
                                
                $leavetypes = LeaveType::leftJoin('employees', function ($join) use ($id) {
                                    $join->on('employees.id', '=', DB::raw($id));
                                })
                                ->leftJoin('leaves', function ($join) use ($employee) {
                                    $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                                         ->where('leaves.employee_id', '=', $employee->id);
                                })
                                ->where('leave_types.created_by', '=', \Auth::user()->creatorId())
                                ->select(
                                    'leave_types.id',
                                    'leave_types.title',
                                    DB::raw('
                                        CASE
                                            WHEN leave_types.title = "Paid Leave" THEN 
                                                employees.paid_leave_balance - COALESCE(SUM(CASE WHEN leaves.status = "Pending" THEN leaves.total_leave_days ELSE 0 END), 0)
                                            ELSE 
                                                (leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0))
                                        END AS days
                                    ')
                                )
                                ->groupBy('leave_types.id', 'leave_types.title', 'employees.paid_leave_balance', 'leave_types.days')
                                ->get();

                
                $status = ['Pending'];
            
                $totalLeaveAvailed = Leave::where('employee_id', $employee->id)
                            ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                            ->whereIn('status', $status)
                            ->sum('total_leave_days');
            }
            else
            {
                $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $leavetypes = LeaveType::where('created_by', \Auth::user()->creatorId())
                    ->with(['leaves' => function ($query) {
                        $query->selectRaw('leave_type_id, SUM(total_leave_days) as total_leave_days')
                              ->groupBy('leave_type_id');
                    }])->get();

                // Iterate through each LeaveType to update days with total_leave_days or keep original value
                $leavetypes->each(function ($leavetype) {
                    $totalLeaveDays = $leavetype->leaves->sum('total_leave_days');
                    $leavetype->days = $totalLeaveDays > 0 ? $totalLeaveDays : $leavetype->days;
                });

            }
            // $leavetypes      = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();

            // Convert collection to array and then back to objects
            $leavetypes = collect($leavetypes->toArray())->map(function ($item) {
                return (object) $item;
            });

            $leavetypes_days = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('leave.create', compact('employees', 'leavetypes', 'leavetypes_days', 'totalLeaveAvailed', 'startOfMonth', 'endOfMonth'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    
    public function getLeaveBalance(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $leaveTypeId = $request->get('leave_type_id');
        $startOfMonth = $request->get('start_of_month');
        $endOfMonth = $request->get('end_of_month');
    
        $totalLeaveAvailed = Helper::totalLeaveAvailed($employeeId, $startOfMonth, $endOfMonth);

        return response()->json(['totalLeaveAvailed' => $totalLeaveAvailed]);
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Leave')) {
            $validator = \Validator::make(
                $request->all(), [
                    'leave_type_id' => 'required',
                    'start_date' => 'required|date',
                    // 'end_date' => 'required|date|after_or_equal:start_date',
                    'leave_reason' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if(\Auth::user()->type == 'employee') {
                $employee = Employee::where('user_id', \Auth::user()->id)->first();
            }
            else {
                $employee = Employee::where('id', $request->employee_id)->first();
            }
            
            // Calculate total leave days
            $startDate = new \DateTime($request->start_date);
            $endDate = new \DateTime($request->end_date);
            $endDate->modify('+1 day'); // Include end date
            $interval = new \DateInterval('P1D');
            $daterange = new \DatePeriod($startDate, $interval, $endDate);
            $total_leave_days = 0;

            if ($request->is_halfday == 'half') {
                $total_leave_days = 0.5;
            } elseif ($request->is_halfday == 'short') {
                $startTime = Carbon::createFromFormat('h:i A', trim($request->start_time));
                $endTime = Carbon::createFromFormat('h:i A', trim($request->end_time));
                $diffInHours = $startTime->diffInHours($endTime);

                if ($diffInHours == 1) {
                    $total_leave_days = 0.5 / 4;
                } elseif ($diffInHours == 2 || ($diffInHours > 1 && $diffInHours < 2)) {
                    $total_leave_days = 0.5 / 4 * 2;
                } elseif ($diffInHours == 3 || ($diffInHours > 2 && $diffInHours < 3)) {
                    $total_leave_days = 0.5 / 4 * 3;
                }
            } else {
                $interval = $startDate->diff($endDate);
                // Get the number of days as an integer
                $total_leave_days = $interval->days;

                if($total_leave_days <= 7){
                    $total_leave_days = 0;
                    foreach ($daterange as $date) {
                        // Check if the day is not Saturday (6) or Sunday (7)
                        if ($date->format('N') < 6) { // 'N' gives day of the week, 1 (Monday) to 7 (Sunday)
                            $total_leave_days++;
                        }
                    }
                }
            }

            // Fetch allowed leave days for the selected leave type
            $leaveType = LeaveType::find($request->leave_type_id);
            if (!$leaveType) {
                return redirect()->back()->with('error', __('Invalid leave type selected.'));
            }

            // Calculate total leave taken by the employee for the current year for this leave type
            $currentYear = date('Y');
            $status = ['Pending', 'Approve'];
            $leavesTaken = Leave::where('employee_id', $employee->id)
                ->where('leave_type_id', $request->leave_type_id)
                ->whereYear('start_date', $currentYear)
                ->whereIn('status', $status)
                ->sum('total_leave_days');

            if (($leavesTaken + $total_leave_days) > $leaveType->days) {
                return redirect()->back()->with('error', __('You have exceeded the maximum allowed leave days for this leave type.'));
            }

            $leave = new Leave();
            if (\Auth::user()->type == "employee") {
                $leave->employee_id = $employee->id;
            } else {
                $leave->employee_id = $request->employee_id;
            }

            $leave->leave_type_id = $request->leave_type_id;
            $leave->applied_on = date('Y-m-d');
            $leave->start_date = $request->start_date;
            $leave->end_date = $request->end_date;
            $leave->total_leave_days = $total_leave_days;
            $leave->leave_reason = $request->leave_reason;
            $leave->remark = $request->remark;
            $leave->status = 'Pending';
            $leave->created_by = \Auth::user()->creatorId();
            $leave->leavetype = $request->is_halfday;
            if ($request->is_halfday == 'short') {
                $leave->start_time = $request->start_time;
                $leave->end_time = $request->end_time;
            } elseif ($request->is_halfday == 'half') {
                $leave->day_segment = $request->day_segment;
            }
            $leave->save();

            $employee = Employee::find($leave->employee_id);
            try {
                $employee = Employee::find($leave->employee_id);
                $employeeEmail = $employee->email;
                $employeeName = $employee->name;
                $additionalReceivers = [
                    'chitranshu@qubifytech.com',
                    'sharma.chitranshu@gmail.com',
                    'swati@qubifytech.com',
                    'swatichamannegi@gmail.com'
                ];

                if ($employee->id == 7 || $employee->id == 8 || $employee->id == 9 || $employee->id == 11 || $employee->id == 13) {
                    Mail::to('happy@qubifytech.com')
                            ->cc($additionalReceivers)
                            ->send(new LeaveRequest($leave, $employeeEmail, $employeeName));
                } elseif ($employee->id == 5) {
                    Mail::to('abhishek@qubifytech.com')
                        // ->cc($additionalReceivers)
                        ->send(new LeaveRequest($leave, $employeeEmail, $employeeName));
                } elseif ($employee->id == 12) {
                    Mail::to('piyush@qubifytech.com')
                        ->cc($additionalReceivers)
                        ->send(new LeaveRequest($leave, $employeeEmail, $employeeName));
                } else{
                    Mail::to($additionalReceivers)
                        ->send(new LeaveRequest($leave, $employeeEmail, $employeeName));
                }

            } catch (\Exception $e) {
                Log::error('Mail sending failed: ' . $e->getMessage());
            }
            
            return redirect()->route('leave.index')->with('success', __('Leave successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Leave $leave)
    {
        return redirect()->route('leave.index');
    }

    public function edit(Leave $leave)
    {
        if(\Auth::user()->can('Edit Leave'))
        {
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
            if($leave->created_by == \Auth::user()->creatorId())
            {
                $id='';
                $employees  = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                if(Auth::user()->type == 'employee')
                {
                    $employee = Employee::where('user_id', '=', \Auth::user()->id)->first();
                    $id = \Auth::user()->id;
                    $leavetypes = LeaveType::leftJoin('employees', function ($join) use ($id) {
                                        $join->on('employees.id', '=', DB::raw($id));
                                    })
                                    ->leftJoin('leaves', function ($join) use ($employee) {
                                        $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                                             ->where('leaves.employee_id', '=', $employee->id);
                                    })
                                    ->where('leave_types.created_by', '=', \Auth::user()->creatorId())
                                    ->select(
                                        'leave_types.id',
                                        'leave_types.title',
                                        DB::raw('
                                            CASE
                                                WHEN leave_types.title = "Paid Leave" THEN 
                                                    employees.paid_leave_balance - COALESCE(SUM(CASE WHEN leaves.status = "Pending" THEN leaves.total_leave_days ELSE 0 END), 0)
                                                ELSE 
                                                    (leave_types.days - COALESCE(SUM(CASE WHEN leaves.status IN ("Approve", "Pending") THEN leaves.total_leave_days ELSE 0 END), 0))
                                            END AS days
                                        ')
                                    )
                                    ->groupBy('leave_types.id', 'leave_types.title', 'employees.paid_leave_balance', 'leave_types.days')
                                    ->get();
                }
                else {
                    $leavetypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
                }              
                $status = ['Pending'];
            
                $totalLeaveAvailed = Leave::where('employee_id', $leave->employee_id)
                            ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                            ->whereIn('status', $status)
                            ->sum('total_leave_days');

                return view('leave.edit', compact('leave', 'employees', 'leavetypes', 'totalLeaveAvailed', 'startOfMonth', 'endOfMonth'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $leave)
    {
        $leave = Leave::find($leave);
        if(\Auth::user()->can('Edit Leave'))
        {
            if($leave->created_by == Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                        'leave_type_id' => 'required',
                        'start_date' => 'required',
                    //    'end_date' => 'required',
                        'leave_reason' => 'required',
                    ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $startDate = new \DateTime($request->start_date);
                $endDate = new \DateTime($request->end_date);

                // Include end date in the calculation
                $endDate->modify('+1 day');

                $interval = new \DateInterval('P1D');
                $daterange = new \DatePeriod($startDate, $interval, $endDate);

                if ($request->is_halfday == 'half') {
                    $total_leave_days = 0.5;
                } elseif ($request->is_halfday == 'short') {
                    $startTime = Carbon::createFromFormat('h:i A', trim($request->start_time));
                    $endTime = Carbon::createFromFormat('h:i A', trim($request->end_time));
                    $diffInHours = $startTime->diffInHours($endTime);
    
                    if ($diffInHours == 1) {
                        $total_leave_days = 0.5 / 4;
                    } elseif ($diffInHours == 2 || ($diffInHours > 1 && $diffInHours < 2)) {
                        $total_leave_days = 0.5 / 4 * 2;
                    } elseif ($diffInHours == 3 || ($diffInHours > 2 && $diffInHours < 3)) {
                        $total_leave_days = 0.5 / 4 * 3;
                    }
                } else {
                    $interval = $startDate->diff($endDate);
                    // Get the number of days as an integer
                    $total_leave_days = $interval->days;
    
                    if($total_leave_days <= 7){
                        $total_leave_days = 0;
                        foreach ($daterange as $date) {
                            // Check if the day is not Saturday (6) or Sunday (7)
                            if ($date->format('N') < 6) { // 'N' gives day of the week, 1 (Monday) to 7 (Sunday)
                                $total_leave_days++;
                            }
                        }
                    }
                }

                // Fetch allowed leave days for the selected leave type
                $leaveType = LeaveType::find($request->leave_type_id);
                if (!$leaveType) {
                    return redirect()->back()->with('error', __('Invalid leave type selected.'));
                }

                // Calculate total leave taken by the employee for the current year for this leave type
                $currentYear = date('Y');
                $status = ['Pending', 'Approve'];
                $leavesTaken = Leave::where('employee_id', $request->employee_id)
                    ->where('leave_type_id', $request->leave_type_id)
                    ->whereYear('start_date', $currentYear)
                    ->whereIn('status', $status)
                    ->where('id', '!=', $leave->id)
                    ->sum('total_leave_days');

                if (($leavesTaken + $total_leave_days) > $leaveType->days) {
                    return redirect()->back()->with('error', __('You have exceeded the maximum allowed leave days for this leave type.'));
                }

                $leave->employee_id      = $request->employee_id;
                $leave->leave_type_id    = $request->leave_type_id;
                $leave->start_date       = $request->start_date;
                $leave->end_date         = $request->end_date;
                $leave->total_leave_days = $total_leave_days;
                $leave->leave_reason     = $request->leave_reason;
                $leave->remark           = $request->remark;
                $leave->save();

                return redirect()->route('leave.index')->with('success', __('Leave successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Leave $leave)
    {
        if(\Auth::user()->can('Delete Leave'))
        {
            if($leave->created_by == \Auth::user()->creatorId())
            {
                $leave->delete();

                return redirect()->route('leave.index')->with('success', __('Leave successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function export()
    {
        $name = 'leave_' . date('Y-m-d i:h:s');
        $data = Excel::download(new LeaveExport(), $name . '.xlsx'); ob_end_clean();

        return $data;
    }

    public function action($id)
    {
        $leave     = Leave::find($id);
        $employee  = Employee::find($leave->employee_id);
        $leavetype = LeaveType::find($leave->leave_type_id);



        return view('leave.action', compact('employee', 'leavetype', 'leave'));
    }

    public function changeaction(Request $request)
    {

        $leave = Leave::find($request->leave_id);

        $leave->status = $request->status;
        if($leave->status == 'Approval')
        {
            $startDate               = new \DateTime($leave->start_date);
            $endDate                 = new \DateTime($leave->end_date);
            $total_leave_days        = $startDate->diff($endDate)->days;
            $leave->total_leave_days = $total_leave_days;
            $leave->status           = 'Approve';
        }

        $leave->save();

         // twilio
         $setting = Utility::settings(\Auth::user()->creatorId());
         $emp = Employee::find($leave->employee_id);
         if (isset($setting['twilio_leave_approve_notification']) && $setting['twilio_leave_approve_notification'] == 1) {
           $msg = __("Your leave has been").' '.$leave->status.'.';


             Utility::send_twilio_msg($emp->phone,$msg);
         }

        $setings = Utility::settings();
        if($setings['leave_status'] == 1)
        {
            $employee     = Employee::where('id', $leave->employee_id)->where('created_by', '=', \Auth::user()->creatorId())->first();
            $leave->name  = !empty($employee->name) ? $employee->name : '';
            $leave->email = !empty($employee->email) ? $employee->email : '';
            try
            {
                Mail::to($leave->email)->send(new LeaveActionSend($leave));
            }
            catch(\Exception $e)
            {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.') . (isset($smtp_error) ? $smtp_error : ''));

        }

        return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.'));
    }

    public function jsoncount(Request $request)
    {
        $leave_counts = LeaveType::select(\DB::raw('COALESCE(SUM(leaves.total_leave_days),0) AS total_leave, leave_types.title, leave_types.days,leave_types.id'))
                                 ->leftjoin('leaves', function ($join) use ($request){
            $join->on('leaves.leave_type_id', '=', 'leave_types.id');
            $join->where('leaves.employee_id', '=', $request->employee_id);
        }
        )->groupBy('leaves.leave_type_id')->get();

        return $leave_counts;

    }

    public function getPaidLeaveBalance($id)
    {
        $employee = Employee::find($id);
    
        if ($employee) {
            $leavetypes = LeaveType::leftJoin('employees', function($join) use ($id) {
                    $join->on('employees.id', '=', DB::raw($id));
                })
                ->leftJoin('leaves', function($join) use ($employee) {
                    $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                         ->where('leaves.employee_id', '=', $employee->id);
                })
                ->where('leave_types.created_by', '=', \Auth::user()->creatorId())
                ->select(
                    'leave_types.id',
                    'leave_types.title',
                    DB::raw('
                        CASE
                            WHEN leave_types.title = "Paid Leave" THEN employees.paid_leave_balance
                            ELSE (leave_types.days - COALESCE(SUM(leaves.total_leave_days), 0))
                        END AS days
                    ')
                )
                ->groupBy('leave_types.id', 'leave_types.title', 'employees.paid_leave_balance', 'leave_types.days')
                ->get();
    
            return response()->json(['leavetypes' => $leavetypes]);
        }
    
        return response()->json(['error' => 'Employee not found'], 404);
    }

    public function checkExistingLeave(Request $request)
    {
        $employeeId = $request->employee_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $leaveId = $request->leave_id;
    
        $leaveExists = Leave::where('employee_id', $employeeId)
                        ->whereIn('status', ['Pending', 'Approve'])
                        ->where('id', '!=', $leave_id)
                        ->where(function($query) use ($startDate, $endDate) {
                            $query->whereBetween('start_date', [$startDate, $endDate])
                                  ->orWhereBetween('end_date', [$startDate, $endDate])
                                  ->orWhereRaw('? BETWEEN start_date AND end_date', [$startDate])
                                  ->orWhereRaw('? BETWEEN start_date AND end_date', [$endDate]);
                        })
                        ->exists();
    
        return response()->json(['exists' => $leaveExists]);
    }

}
