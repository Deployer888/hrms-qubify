<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Event;
use App\Models\EventEmployee;
use App\Models\Projects;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Utility;
use App\Imports\EventImport;
use App\Exports\EventExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\EventNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EventCreatedNotification;
use Illuminate\Support\Facades\Log;
use App\Helpers\Helper;

class EventController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('Manage Event')) {
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get();
            $events    = Event::where('created_by', '=', \Auth::user()->creatorId())->get();

            $arrEvents = [];
            foreach ($events as $event) {
                $arr['id']    = $event['id'];
                $arr['title'] = $event['title'];
                $arr['description'] = $event['description'];
                $arr['start'] = $event['start_date'];
                $arr['end']   = $event['end_date'];
                //                $arr['allDay']    = !0;
                //                $arr['className'] = 'bg-danger';
                $arr['backgroundColor'] = $event['color'];
                $arr['borderColor']     = "#fff";
                $arr['textColor']       = "white";
                $arr['url']             = route('event.edit', $event['id']);

                $arrEvents[] = $arr;
            }
            $arrEvents = str_replace('"[', '[', str_replace(']"', ']', json_encode($arrEvents)));
            return view('event.index', compact('arrEvents', 'employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('Create Event')) {
            $employees   = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch      = Branch::where('created_by', '=', \Auth::user()->creatorId())->get();
            $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('event.create', compact('employees', 'branch', 'departments'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Event')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'branch_id' => 'required', 
                    'department_id' => 'required',
                    'employee_id' => 'required', 
                    'title' => 'required|string|max:255',
                    'start_date' => 'required|date|date_format:Y-m-d', 
                    'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
                    'audio_file' => 'nullable|mimes:mp3,wav|max:5120',
                    'color' => 'required', 
                ]
            );
            
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            
            $imageFileNameToStore = null;
            $defaultImage = 'test_.jpeg';
            if ($request->file('image')) {
                $imageExtension = $request->file('image')->getClientOriginalExtension();
                $imageFileNameToStore = 'image_.' . time() . $imageExtension;
                $request->file('image')->storeAs('uploads/event/images', $imageFileNameToStore);
            }
            
            $audioFileNameToStore = null;
            $defaultAudio = 'default_audio.mp3';
            if ($request->file('audio_file')) {
                $audioExtension = $request->file('audio_file')->getClientOriginalExtension();
                $audioFileNameToStore = 'audio_.' . time() . '.' . $audioExtension; 
                $request->file('audio_file')->storeAs('uploads/event/audio', $audioFileNameToStore);
            }
            
            $event = new Event();
            $event->branch_id     = $request->branch_id;
            $event->department_id = json_encode($request->department_id);
            $event->employee_id   = json_encode($request->employee_id);
            $event->title         = $request->title;
            $event->start_date    = $request->start_date;
            $event->end_date      = $request->end_date;
            $event->color         = $request->color;
            $event->description   = $request->description;
            $event->image         = $imageFileNameToStore ? $imageFileNameToStore : $defaultImage;
            $event->audio_file    = $audioFileNameToStore ? $audioFileNameToStore : $defaultAudio;
            $event->created_by    = \Auth::user()->creatorId();
            $event->save();

            $setting = Utility::settings(\Auth::user()->creatorId());
            $branch = Branch::find($request->branch_id);
            if (isset($setting['event_notification']) && $setting['event_notification'] == 1) {
                $msg = $request->title . ' ' . __("for branch") . ' ' . $branch->name . ' ' . ("from") . ' ' . $request->start_date . ' ' . __("to") . ' ' . $request->end_date . '.';
                Utility::send_slack_msg($msg);
            }

            //telegram
            $setting = Utility::settings(\Auth::user()->creatorId());
            $branch = Branch::find($request->branch_id);
            if (isset($setting['telegram_ticket_notification']) && $setting['telegram_ticket_notification'] == 1) {
                $msg = $request->title . ' ' . __("for branch") . ' ' . $branch->name . ' ' . ("from") . ' ' . $request->start_date . ' ' . __("to") . ' ' . $request->end_date . '.';
                Utility::send_telegram_msg($msg);
            }


            //twilio
            $setting = Utility::settings(\Auth::user()->creatorId());
            $branch = Branch::find($request->branch_id);
            $departments = Department::where('branch_id', $request->branch_id)->first();
            $employee = Employee::where('branch_id', $request->branch_id)->first();
            $employeess = '';
            if (isset($setting['twilio_event_notification']) && $setting['twilio_event_notification'] == 1) {
                $employeess = Employee::whereIn('branch_id', $request->employee_id)->get();
                foreach ($employeess as $key => $employee) {
                    $msg = $request->title . ' ' . __("for branch") . ' ' . $branch->name . ' ' . ("from") . ' ' . $request->start_date . ' ' . __("to") . ' ' . $request->end_date . '.';
                    Utility::send_twilio_msg($employee->phone, $msg);
                }
            }

            if (in_array('0', $request->employee_id)) {
                $departmentEmployee = Employee::whereIn('department_id', $request->department_id)->get()->pluck('id');
                $departmentEmployee = $departmentEmployee;
            } else {
                $departmentEmployee = $request->employee_id;
            }
            foreach ($departmentEmployee as $employee) {
                $eventEmployee              = new EventEmployee();
                $eventEmployee->event_id    = $event->id;
                $eventEmployee->employee_id = $employee;
                $eventEmployee->created_by  = \Auth::user()->creatorId();
                $eventEmployee->save();
            }

            $employeesEmails = Employee::whereIn('id', $departmentEmployee)->pluck('email')->toArray();
            foreach ($employeesEmails as $email) {
                Mail::to($email)->queue(new EventNotification($event));
            }

            if (in_array('0', $request->input('employee_id', []))) {
                $allemployees = Employee::pluck('email')->toArray();
                
                Mail::to('abhishek@qubifytech.com')->send(new EventNotification($event));
                
                /*foreach ($allemployees as $email) {
                    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::to($email)->send(new EventNotification($event));
                        } catch (\Exception $e) {
                            // Log the error message
                            Log::error("Failed to send mail to: {$email}. Error: " . $e->getMessage());
                        }
                    }
                }*/
            }


            $employees = User::where('type', 'employee')->pluck('fcm_token')->toArray();
            foreach ($employees as $fcm_token) {
                $notificationData = [
                    'title' => 'New Event Added',
                    'body' => $event->title,
                    'fcm_token' => $fcm_token,
                ];
                try {
                    Helper::sendNotification($notificationData); // Call the helper function
                } catch (\Exception $e) {
                    \Log::error("Notification Error: " . $e->getMessage());
                }
            }

            Notification::send($employee, new EventCreatedNotification($event));
            return redirect()->route('event.index')->with('success', __('Event  successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Event $event)
    {
        return redirect()->route('event.index');
    }

    public function edit($event)
    {

        if (\Auth::user()->can('Edit Event')) {
            $event = Event::find($event);
            if ($event->created_by == Auth::user()->creatorId()) {
                $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

                return view('event.edit', compact('event', 'employees'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, Event $event)
    {
        if (\Auth::user()->can('Edit Event')) {
            if ($event->created_by == \Auth::user()->creatorId()) {
                // Validate the request data
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'title' => 'required|string|max:255',
                        'start_date' => 'required|date|date_format:Y-m-d', 
                        'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
                        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                        'audio_file' => 'nullable|mimes:mp3,wav|max:5120', 
                        'color' => 'required', 
                    ]
                );

                // If validation fails, return with the first error message
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }
                if (!$event) {
                    return redirect()->back()->with('error', 'Event not found.');
                }

                // Handle Image Upload (if a new image is provided)
                if ($request->file('image')) {
                    // Delete the old image if it exists
                    if ($event->image && $event->image !== 'test_.jpeg') {
                        $oldImagePath = storage_path('app/uploads/event/images/' . $event->image);
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    // Upload the new image
                    $imageExtension = $request->file('image')->getClientOriginalExtension();
                    $imageFileNameToStore = 'image_.' . time() . $imageExtension;
                    $request->file('image')->storeAs('uploads/event/images', $imageFileNameToStore);
                    $event->image = $imageFileNameToStore;
                }

                // Handle Audio File Upload (if a new audio file is provided)
                if ($request->file('audio_file')) {
                    // Delete the old audio file if it exists
                    if ($event->audio_file && $event->audio_file !== 'default_audio.mp3') {
                        $oldAudioPath = storage_path('app/uploads/events/audio/' . $event->audio_file);
                        if (file_exists($oldAudioPath)) {
                            unlink($oldAudioPath);
                        }
                    }

                    // Upload the new audio file
                    $audioExtension = $request->file('audio_file')->getClientOriginalExtension();
                    $audioFileNameToStore = 'audio_.' . time() . '.' . $audioExtension; // Unique name using timestamp
                    $request->file('audio_file')->storeAs('uploads/events/audio', $audioFileNameToStore);
                    $event->audio_file = $audioFileNameToStore;
                }

                // Update event details
                $event->branch_id     = $request->branch_id;
                $event->department_id = json_encode($request->department_id);
                $event->employee_id   = json_encode($request->employee_id);
                $event->title         = $request->title;
                $event->start_date    = $request->start_date;
                $event->end_date      = $request->end_date;
                $event->color         = $request->color;
                $event->description   = $request->description;
                $event->save();
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'title' => 'required',
                        'start_date' => 'required',
                        'end_date' => 'required',
                        'color' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $event->title       = $request->title;
                $event->start_date  = $request->start_date;
                $event->end_date    = $request->end_date;
                $event->color       = $request->color;
                $event->description = $request->description;
                $event->save();
                $departmentIds = json_decode($event->department_id, true);
                $departmentEmployee = Employee::whereIn('department_id',$departmentIds??[])->get()->pluck('id');
                $employeesEmails = Employee::whereIn('id', $departmentEmployee??[])->pluck('email')->toArray();
                foreach ($employeesEmails as $email) {
                    Mail::to($email)->queue(new EventNotification($event));
                }

                return redirect()->route('event.index')->with('success', __('Event successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Event $event)
    {
        if (\Auth::user()->can('Delete Event')) {
            if ($event->created_by == \Auth::user()->creatorId()) {
                $event->delete();

                return redirect()->route('event.index')->with('success', __('Event successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function export()
    {
        $name = 'event' . date('Y-m-d i:h:s');
        $data = Excel::download(new EventExport(), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }

    public function importFile()
    {
        return view('event.import');
    }

    public function import(Request $request)
    {
        // dd('here');
        $rules = [
            'file' => 'required|mimes:csv,txt',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $events = (new EventImport())->toArray(request()->file('file'))[0];
        // dd($events);
        $totalEvents = count($events) - 1;
        $errorArray    = [];

        for ($i = 1; $i <= count($events) - 1; $i++) {

            $event = $events[$i];
            // dd($event[2]);
            $eventsByTitle = Event::where('title', $event[2])->first();

            if (!empty($eventsByTitle)) {
                $eventData = $eventsByTitle;
            } else {
                $eventData = new Event();
            }

            $eventData->branch_id           = $event[0];
            $eventData->department_id       = $event[1];
            $eventData->employee_id         = '["0"]';
            $eventData->title               = $event[2];
            $eventData->start_date          = $event[3];
            $eventData->end_date            = $event[4];
            $eventData->color               = $event[5];
            $eventData->description         = $event[6];
            $eventData->created_by          = $event[7];

            if (empty($eventData)) {
                $errorArray[] = $eventData;
            } else {
                $eventData->save();
            }
        }

        $errorRecord = [];
        if (empty($errorArray)) {
            $data['status'] = 'success';
            $data['msg']    = __('Record successfully imported');
        } else {
            $data['status'] = 'error';
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalEvents . ' ' . 'record');


            foreach ($errorArray as $errorData) {

                $errorRecord[] = implode(',', $errorData);
            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }

    public function getdepartment(Request $request)
    {

        if ($request->branch_id == 0) {
            $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } else {
            $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->where('branch_id', $request->branch_id)->get()->pluck('name', 'id')->toArray();
        }

        return response()->json($departments);
    }

    public function getemployee(Request $request)
    {
        if (in_array('0', $request->department_id)) {
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } else {
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->whereIn('department_id', $request->department_id)->get()->pluck('name', 'id')->toArray();
        }

        return response()->json($employees);
    }
}
