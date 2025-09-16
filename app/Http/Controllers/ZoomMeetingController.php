<?php

namespace App\Http\Controllers;

use App\Traits\ZoomMeetingTrait;
use App\Models\ZoomMeeting;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\AttendanceEmployee;
use App\Helpers\Utility;
use App\Helpers\Helper;

class ZoomMeetingController extends Controller
{

    use ZoomMeetingTrait;
    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;
    const MEETING_URL = "https://api.zoom.us/v2/";


    public function index()
    {
        
        /*Manually Attendance*/
        
        /*$data['PunchData'] = [
            [   "Name" => "Nitesh",
                "Empcode" => "0073",
                "PunchDate" => "05/12/2024 22:14:00",
                "M_Flag" => null,
                "mcid" => "1"
            ],
            [   "Name" => "Nitesh",
                "Empcode" => "0073",
                "PunchDate" => "05/12/2024 20:45:00",
                "M_Flag" => null,
                "mcid" => "2"
            ]
        ];
        
        $data['PunchData'] = array_reverse($data['PunchData']);
                
        
        
        if(count($data['PunchData']) > 0)
        {
            // Assuming $data is an array of attendance records
            foreach ($data['PunchData'] as $key => $record) {
echo $key . "<br>";
                $employee = Employee::select('id', 'shift_start')->where('empcode', $record['Empcode'])->first();
                
                if(!$employee) continue;
                $emp_id = $employee->id;
                
                $punchDate = $record['PunchDate'];
                $punchDateTime = Carbon::createFromFormat('d/m/Y H:i:s', $punchDate);
        
                $date = $punchDateTime->format('Y-m-d');
                $time = $punchDateTime->format('H:i:s');
        
                $isExist = Helper::checkCurrentTimeAttendace($employee->id, $date, $time);
                
                if($record['mcid'] == 2) { // create new clock-in attendance function
                    if($employee && !$isExist)
                    {
                        $date = date("Y-m-d", strtotime($date));
                        $startTime = $employee->shift_start;
                        $totalLateSeconds = strtotime($time) - strtotime($date . $startTime);
                        $hours = floor($totalLateSeconds / 3600);
                        $mins = floor($totalLateSeconds / 60 % 60);
                        $secs = floor($totalLateSeconds % 60);
                        $late = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                        
                        $employeeData = Employee::find($emp_id);
                
                        $employeeAttendance = new AttendanceEmployee();
                        $employeeAttendance->employee_id = $emp_id;
                        $employeeAttendance->employee_name    = $employeeData->name;
                        $employeeAttendance->date = $date;
                        $employeeAttendance->status = 'Present';
                        $employeeAttendance->clock_in = $time;
                        $employeeAttendance->clock_out = '00:00:00';
                        $employeeAttendance->late = $late;
                        $employeeAttendance->early_leaving = '00:00:00';
                        $employeeAttendance->overtime = '00:00:00';
                        $employeeAttendance->total_rest = Helper::TotalRest($time, $emp_id);
                        $employeeAttendance->created_by = $emp_id;
                        $employeeAttendance->save();
                    }
                    else continue;
                } // attendance function
                
                else if($record['mcid'] == 1) {  // update clock-out attendance function
                    // 'clock-Out'
                    $date = date("Y-m-d", strtotime($date));
        
                    // Fetch the last attendance record for the employee where clock_out is '00:00:00'
                    $todayAttendance = AttendanceEmployee::where('employee_id', $emp_id)
                        ->where('date', $date)
                        ->where('clock_out', '00:00:00')
                        ->orderBy('id', 'desc')
                        ->first();
            
                    // Update the attendance record
                    $todayAttendance->clock_out = $time;
                    $todayAttendance->save();
                } // update function
            }
        }*/
        
        
        $created_by = \Auth::user()->creatorId();
        $ZoomMeetings = ZoomMeeting::where('created_by', $created_by)->get();
        // $this->statusUpdate();
        return view('zoom_meeting.index', compact('ZoomMeetings'));
    }


    public function create()
    {
        $created_by = \Auth::user()->creatorId();
        $employee_option = User::where('created_by', $created_by)->pluck('name', 'id');
        return view('zoom_meeting.create', compact('employee_option'));
    }


    public function store(Request $request)
    {
        $data['topic'] = $request->title;
        $data['start_time'] = date('y:m:d H:i:s', strtotime($request->start_date));
        $data['duration'] = (int)$request->duration;
        $data['password'] = $request->password; 
        $data['host_video'] = 0;
        $data['participant_video'] = 0;
        $meeting_create = $this->createmitting($data);

        \Log::info('Meeting');
        \Log::info((array)$meeting_create);
        if (isset($meeting_create['success']) &&  $meeting_create['success'] == true) {
            $meeting_id = isset($meeting_create['data']['id']) ? $meeting_create['data']['id'] : 0;
            $start_url = isset($meeting_create['data']['start_url']) ? $meeting_create['data']['start_url'] : '';
            $join_url = isset($meeting_create['data']['join_url']) ? $meeting_create['data']['join_url'] : '';
            $status = isset($meeting_create['data']['status']) ? $meeting_create['data']['status'] : '';
            
            $created_by = \Auth::user()->creatorId();
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'user_id' => 'required',
                    'start_date' => 'required',
                    'duration' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $user_id = 0;
            if (!empty($request->user_id)) {
                $user_id = implode(',', $request->user_id);
            }


            $ZoomMeeting = new ZoomMeeting();
            $ZoomMeeting->title = $request->title;
            $ZoomMeeting->meeting_id = $meeting_id;
            $ZoomMeeting->user_id = $user_id;
            $ZoomMeeting->password = $request->password;
            $ZoomMeeting->join_url = $join_url;
            $ZoomMeeting->start_date = $request->start_date;
            $ZoomMeeting->duration = $request->duration;
            $ZoomMeeting->start_url = $start_url;
            $ZoomMeeting->status = $status;
            $ZoomMeeting->created_by = $created_by;

            $ZoomMeeting->save();
            return redirect()->back()->with('success', __('Meeting created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Meeting not created.'));
        }
    }


    public function show(ZoomMeeting $ZoomMeeting)
    {
        if ($ZoomMeeting->created_by == \Auth::user()->creatorId()) {

            return view('zoom_meeting.view', compact('ZoomMeeting'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }


    public function edit(ZoomMeeting $ZoomMeeting)
    {
        $created_by = \Auth::user()->creatorId();
        $employee_option = User::where('created_by', $created_by)->pluck('name', 'id');
        return view('zoom_meeting.edit', compact('employee_option', 'ZoomMeeting'));
    }


    public function update(Request $request, ZoomMeeting $ZoomMeeting)
    {
        $created_by = \Auth::user()->creatorId();
        $validator = \Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'user_id' => 'required',
                // 'password' => 'required',
                'start_date' => 'required',
                'duration' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $ZoomMeeting = new ZoomMeeting();
        $ZoomMeeting->title = $request->title;
        $ZoomMeeting->user_id = $request->user_id;
        $ZoomMeeting->password = $request->password;
        $ZoomMeeting->start_date = $request->start_date;
        $ZoomMeeting->duration = $request->duration;
        $ZoomMeeting->created_by = $created_by;

        $ZoomMeeting->save();
        return redirect()->back()->with('success', __('Zoom Meeting update Successfully'));
    }


    public function destroy(ZoomMeeting $ZoomMeeting)
    {
        $ZoomMeeting->delete();
        return redirect()->back()->with('success', __('Zoom Meeting Delete Succsefully'));
    }

    public function statusUpdate()
    {
        $meetings = ZoomMeeting::where('created_by', \Auth::user()->id)->pluck('meeting_id');
        foreach ($meetings as $meeting) {
            $data = $this->get($meeting);
            if (isset($data['data']) && !empty($data['data'])) {
                $meeting = ZoomMeeting::where('meeting_id', $meeting)->update(['status' => $data['data']['status']]);
            }
        }
    }

    public function calender()
    {

        $created_by = Auth::user()->creatorId();
        $ZoomMeetings = ZoomMeeting::where('created_by', $created_by)->get();

        $arrMeeting[] = '';

        foreach ($ZoomMeetings as $zoommeeting) {
            $arr['id']        = $zoommeeting['id'];
            $arr['title']     = $zoommeeting['title'];
            $arr['start']     = $zoommeeting['start_date'];
            $arr['className'] = 'bg-primary';
            $arr['url']       = route('zoom-meeting.show', $zoommeeting['id']);
            $arrMeeting[]        = $arr;
        }

        $calandar = array_merge($arrMeeting);
        $calandar = str_replace('"[', '[', str_replace(']"', ']', json_encode($calandar)));

        return view('zoom_meeting.calendar', compact('calandar'));
    }
}
