<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Utility;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\Gate;
use Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreate;
use App\Models\{Branch, Announcement, Meeting, Event, AttendanceEmployee};
use App\Models\Department;
use App\Models\Designation;
use App\Models\Document;
use App\Models\LeaveType;
use App\Models\EmployeeDocument;
use App\Models\DucumentUpload;
use DB, File;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if(!empty($request->token) && !empty($request->user))
        {
            
            $user = json_decode(json_encode($request->user));
            if($user->type == 'employee')
            {
                $emp = Employee::where('user_id', '=', $user->id)->first();

                $announcements = Announcement::orderBy('announcements.id', 'desc')->take(5)->leftjoin('announcement_employees', 'announcements.id', '=', 'announcement_employees.announcement_id')->where('announcement_employees.employee_id', '=', $emp->id)->orWhere(
                    function ($q){
                        $q->where('announcements.department_id', '["0"]')->where('announcements.employee_id', '["0"]');
                    }
                )->get();

                $meetings  = Meeting::orderBy('meetings.id', 'desc')->take(5)->leftjoin('meeting_employees', 'meetings.id', '=', 'meeting_employees.meeting_id')->where('meeting_employees.employee_id', '=', $emp->id)->orWhere(
                    function ($q){
                        $q->where('meetings.department_id', '["0"]')->where('meetings.employee_id', '["0"]');
                    }
                )->get();
                
                $events    = Event::all();

                $arrEvents = [];
                foreach ($events as $event) {
                    $arr['id']    = $event['id'];
                    $arr['title'] = $event['title'];
                    $arr['description'] = $event['description'];
                    $arr['start'] = $event['start_date'];
                    $arr['end']   = $event['end_date'];
                    $arr['backgroundColor'] = $event['color'];
                    $arr['borderColor']     = "#fff";
                    $arr['textColor']       = "white";
                    $arr['url']             = route('event.edit', $event['id']);
    
                    $arrEvents[] = $arr;
                }
                $arrEvents = str_replace('"[', '[', str_replace(']"', ']', json_encode($arrEvents)));
                
                $date = date("Y-m-d");
                $time = date("H:i:s");
                $employeeAttendance = AttendanceEmployee::orderBy('id', 'desc')->where('employee_id', '=', !empty($emp) ? $emp->id : 0)->where('date', '=', $date)->first();
                $employeeAttendanceList = AttendanceEmployee::orderBy('id', 'desc')->where('employee_id', '=', !empty($emp) ? $emp->id : 0)->where('date', '=', $date)->get();
                $officeTime['startTime'] = Utility::getValByName('company_start_time');
                $officeTime['endTime']   = Utility::getValByName('company_end_time');
                
                return ['arrEvents' => $arrEvents, 'announcements' => $announcements, 'meetings' => $meetings, 'employeeAttendance' => $employeeAttendance, 'officeTime' => $officeTime, 'employeeAttendanceList' => $employeeAttendanceList];
            }
            else if($user->type == 'super admin')
            {
                $user                       = $request->user;
                $user['total_user']         = $user->countCompany();
                $user['total_paid_user']    = $user->countPaidCompany();
                $user['total_orders']       = Order::total_orders();
                $user['total_orders_price'] = Order::total_orders_price();
                $user['total_plan']         = Plan::total_plan();
                $user['most_purchese_plan'] = (!empty(Plan::most_purchese_plan()) ? Plan::most_purchese_plan()->name : '');

                $chartData = $this->getOrderChart(['duration' => 'week']);

                return ['user' => $user, 'chartData' => $chartData];
            }
            else
            {
                $events    = Event::where('created_by', '=', $user->creatorId())->get();
                $arrEvents = [];

                foreach($events as $event)
                {
                    $arr['id']    = $event['id'];
                    $arr['title'] = $event['title'];
                    $arr['start'] = $event['start_date'];
                    $arr['end']   = $event['end_date'];
                    $arr['description'] = $event['description'];

                    $arr['backgroundColor'] = $event['color'];
                    $arr['borderColor']     = "#fff";
                    $arr['textColor']       = "white";
                    $arr['url']             = route('event.edit', $event['id']);

                    $arrEvents[] = $arr;
                }


                $announcements = Announcement::orderBy('announcements.id', 'desc')->take(5)->where('created_by', '=', $user->creatorId())->get();


                $emp           = User::where('type', '=', 'employee')->where('created_by', '=', $user->creatorId())->get();
                $countEmployee = count($emp);

                $user      = User::where('type', '!=', 'employee')->where('created_by', '=', $user->creatorId())->get();
                $countUser = count($user);

                $countTicket      = Ticket::where('created_by', '=', $user->creatorId())->count();
                $countOpenTicket  = Ticket::where('status', '=', 'open')->where('created_by', '=', $user->creatorId())->count();
                $countCloseTicket = Ticket::where('status', '=', 'close')->where('created_by', '=', $user->creatorId())->count();

                $currentDate = date('Y-m-d');

                $employees     = User::where('type', '=', 'employee')->where('created_by', '=', $user->creatorId())->get();
                $countEmployee = count($employees);
                $notClockIn    = AttendanceEmployee::where('date', '=', $currentDate)->get()->pluck('employee_id');

                $notClockIns = Employee::where('created_by', '=', $user->creatorId())->whereNotIn('id', $notClockIn)->get();

                $accountBalance = AccountList::where('created_by', '=', $user->creatorId())->sum('initial_balance');

                $totalPayee = Payees::where('created_by', '=', $user->creatorId())->count();
                $totalPayer = Payer::where('created_by', '=', $user->creatorId())->count();

                $meetings = Meeting::where('created_by', '=', $user->creatorId())->limit(5)->get();

                return ['arrEvents' => $arrEvents, 'announcements' => $announcements, 'employees' => $employees, 'meetings' => $meetings, 'countEmployee' => $countEmployee, 'countUser' => $countUser, 'countTicket' => $countTicket, 'countOpenTicket' => $countOpenTicket, 'countCloseTicket' => $countCloseTicket, 'notClockIns' => $notClockIns, 'countEmployee' => $countEmployee, 'accountBalance' => $accountBalance, 'totalPayee' => $totalPayee, 'totalPayer' => $totalPayer];
            }
        }
        else
        {
            if(!file_exists(storage_path() . "/installed"))
            {
                header('location:install');
                die;
            }
            else
            {
                $settings = Utility::settings();
                if($settings['display_landing_page'] == 'on')
                {
                    $plans = Plan::get();
                    $get_section = LandingPageSection::orderBy('section_order', 'ASC')->get();

                    return ['plans' => $plans ,'get_section' => $get_section ];
                }
                else
                {
                    return false;
                }

            }
        }
    }

    public function getOrderChart($arrParam)
    {
        $arrDuration = [];
        if($arrParam['duration'])
        {
            if($arrParam['duration'] == 'week')
            {
                $previous_week = strtotime("-2 week +1 day");
                for($i = 0; $i < 14; $i++)
                {
                    $arrDuration[date('Y-m-d', $previous_week)] = date('d-M', $previous_week);
                    $previous_week                              = strtotime(date('Y-m-d', $previous_week) . " +1 day");
                }
            }
        }

        $arrTask          = [];
        $arrTask['label'] = [];
        $arrTask['data']  = [];
        foreach($arrDuration as $date => $label)
        {

            $data               = Order::select(\DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            $arrTask['label'][] = $label;
            $arrTask['data'][]  = $data->total;
        }

        return $arrTask;
    }
}
