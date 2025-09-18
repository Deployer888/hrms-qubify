<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Document;
use App\Models\{Employee, Leave};
use App\Models\LeaveType;
use App\Models\EmployeeDocument;
use App\Mail\UserCreate;
use App\Models\Plan;
use App\Models\User;
use App\Models\Office;  
use App\Models\Utility;
use Carbon\Carbon;
use File;
use DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Imports\EmployeesImport;
use App\Exports\EmployeesExport;
use Maatwebsite\Excel\Facades\Excel;

//use Faker\Provider\File;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (\Auth::user()->can('Manage Employee')) {
            $query = Employee::query();

            if (Auth::user()->type == 'employee') {
                $query->where('user_id', '=', Auth::user()->id);
            } else {
                $query->where('created_by', \Auth::user()->creatorId())->where('is_active', 1);
                if (isset($_GET['type']) && !empty($_GET['type'])) {
                    $query->where('is_probation', 1)->with('branch', 'department', 'designation');
                }else{
                    $query->whereIn('is_probation', [0,1])->with('branch', 'department', 'designation');
                }
            }

            $employees = $query->get();

            return view('employee.index', compact('employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        /*
        try {
            Mail::send([], [], function ($message) {
                $message->to('visionabhi0503@gmail.com')
                        ->subject('Welcome to Test')
                        ->html('
                            <html>
                            <body>
                                <h2>Welcome</h2>
                            </body>
                            </html>');
            });
            return 'Email sent successfully!';
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
            return 'Email sending failed: ' . $e->getMessage();
        }
        */
        if (\Auth::user()->can('Create Employee')) {
            $company_settings = Utility::settings();
            $documents        = Document::where('created_by', \Auth::user()->creatorId())->get();
            $branches         = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments      = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations     = Designation::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $employees        = User::where('created_by', \Auth::user()->creatorId())->get();
            $employeesId      = \Auth::user()->employeeIdFormat($this->employeeNumber());
            $offices          = Office::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('employee.create', compact('employees', 'employeesId', 'departments', 'designations', 'documents', 'branches', 'company_settings', 'offices'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Employee')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'dob' => 'required',
                    'gender' => 'required',
                    'phone' => 'required',
                    'address' => 'required',
                    'salary' => 'required',
                    'email' => 'required|unique:users',
                    'personal_email' => 'required|unique:users',
                    'office_id' => 'required',
                    'department_id' => 'required',
                    'designation_id' => 'required',
                    'shift_start' => 'required',
                    'document.*' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc,zip|max:20480',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->withInput()->with('error', $messages->first());
            }

            $objUser        = User::find(\Auth::user()->creatorId());
            $total_employee = $objUser->countEmployees();
            $plan           = Plan::find($objUser->plan);
            $password = Str::password(12);
            $pin = rand(100000, 999999);

            if ($total_employee < $plan->max_employees || $plan->max_employees == -1) {

                $user = User::create(
                    [
                        'name' => $request['name'],
                        'email' => $request['email'],
                        'personal_email' => $request['personal_email'],
                        'password' => Hash::make($password),
                        'type' => 'employee',
                        'lang' => 'en',
                        'created_by' => \Auth::user()->creatorId(),
                    ]
                );
                // $user->save();
                $user->assignRole('employee');
            } else {
                return redirect()->back()->with('error', __('Your employee limit is over, Please upgrade plan.'));
            }


            if (!empty($request->document) && !is_null($request->document)) {
                $document_implode = implode(',', array_keys($request->document));
            } else {
                $document_implode = null;
            }
            if($request->has('company_doj')){
                $dateOfJoining = Carbon::parse($request->company_doj);
                $currentDate = Carbon::now();

                if ($dateOfJoining->greaterThanOrEqualTo($currentDate->subMonths(3))) {
                    $request['is_probation'] = 1;
                }else{
                    $request['is_probation'] = 0;
                }
            }

            $employee = Employee::create(
                [
                    'clock_in_pin' => $pin,
                    'user_id' => $user->id,
                    'name' => $request['name'],
                    'dob' => Carbon::parse($request->dob)->format('Y-m-d'),
                    'gender' => $request['gender'],
                    'phone' => $request['phone'],
                    'address' => $request['address'],
                    'email' => $request['email'],
                    'salary' => $request['salary'],
                    'password' => Hash::make($password),
                    'employee_id' => $this->employeeNumber(),
                    'branch_id' => $request['branch_id'],
                    'department_id' => $request['department_id'],
                    'designation_id' => $request['designation_id'],
                    'company_doj' => Carbon::parse($request->company_doj)->format('Y-m-d'),
                    'documents' => $document_implode,
                    'account_holder_name' => $request['account_holder_name'],
                    'account_number' => $request['account_number'],
                    'bank_name' => $request['bank_name'],
                    'bank_identifier_code' => $request['bank_identifier_code'],
                    'branch_location' => $request['branch_location'],
                    'tax_payer_id' => $request['tax_payer_id'],
                    'shift_start' => $request['shift_start'],
                    'is_team_leader' => $request['is_team_leader'] ?? 'default_value',
                    'team_leader_id' => $request['team_leader'],
                    'is_probation' => $request['is_probation'],
                    'created_by' => \Auth::user()->creatorId(),
                ]
            );

            if ($request->hasFile('document')) {
                foreach ($request->document as $key => $document) {
                    $filenameWithExt = $request->file('document')[$key]->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('document')[$key]->getClientOriginalExtension();
                    $docName = Document::where('id', $key)->pluck('name')->first();
                    $fileNameToStore = $request->name . '_' . $docName . '.' . $extension;
                    
                    // Change directory to public/document
                    $dir = public_path('document/');
                    $image_path = $dir . $filenameWithExt;

                    // Create directory if it doesn't exist
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }

                    // Move uploaded file to public directory
                    $request->file('document')[$key]->move($dir, $fileNameToStore);

                    $employee_document = EmployeeDocument::create([
                        'employee_id' => $employee['employee_id'],
                        'document_id' => $key,
                        'document_value' => $fileNameToStore,
                        'created_by' => \Auth::user()->creatorId(),
                    ]);
                    $employee_document->save();
                }
            }

            $setings = Utility::settings();
            if ($setings['employee_create'] == 1) {
                $user->type     = 'Employee';
                $user->password = $password;
                $user->pin = $pin;
                try {
                    Mail::to($user->email)->send(new UserCreate($user));
                } catch (\Exception $e) {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }

                return redirect()->route('employee.index')->with('success', __('Employee successfully created.') . (isset($smtp_error) ? $smtp_error : ''));
            }

            return redirect()->route('employee.index')->with('success', __('Employee successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function removeDocument($employeeId, $documentId)
    {
        try {
            // Find the employee document
            $employeeDocument = EmployeeDocument::where('employee_id', $employeeId)
                ->where('document_id', $documentId)
                ->first();
                
            if (!$employeeDocument) {
                return redirect()->back()->with('error', 'Document not found.');
            }

            // Get the filename and validate it
            $filename = $employeeDocument->document_value;
            
            // Now try to delete the physical file
            $filePath = public_path('document/' . $filename);
            
            chmod($filePath, 0777);
            if (unlink($filePath)) {
                $employeeDocument->delete();
                return redirect()->back()->with('success', 'Document removed successfully.');
            } else {
                $error = error_get_last();
                \Log::error('Unlink failed: ' . print_r($error, true));
                return redirect()->back()->with('warning', 'File deletion failed: ' . ($error['message'] ?? 'Unknown error'));
            }
            
        } catch (\Exception $e) {
            \Log::error('Error removing document: ' . $e->getMessage(), [
                'employee_id' => $employeeId,
                'document_id' => $documentId,
                'filename' => $filename ?? 'null'
            ]);
            
            return redirect()->back()->with('error', 'Error removing document: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        if (\Auth::user()->can('Edit Employee')) {
            $documents    = Document::where('created_by', \Auth::user()->creatorId())->get();
            $branches     = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments  = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations = Designation::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // $employee     = Employee::find($id);
            $employee     = Employee::with('teamLeader', 'user')->find($id);
            $employeesId  = \Auth::user()->employeeIdFormat($employee->employee_id);
            $teamLeaderDetails = $employee->getTeamLeaderNameAndId();
            $offices      = Office::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            // dd($employee);
            return view('employee.edit', compact('employee', 'employeesId', 'branches', 'departments', 'designations', 'documents', 'teamLeaderDetails', 'offices'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('Edit Employee')) {
            $employee = Employee::findOrFail($id);
            $userId = $employee->user_id;
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'dob' => 'required',
                    'personal_email' => \Illuminate\Validation\Rule::unique('users', 'personal_email')->ignore($userId),
                    'gender' => 'required',
                    'phone' => 'required|numeric',
                    'address' => 'required',
                    // 'salary' => 'required',
                    'document.*' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc,zip|max:20480',
                ]
            );

            // $validator->sometimes('shift_start', 'required', function ($input) {
            //     return empty($input->shift_start);
            // });

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $employee = Employee::findOrFail($id);
            $user = User::findOrFail($employee->user_id);
            if($request->personal_email){
                $user['personal_email'] = $request->personal_email;
            }
            $user->update();

            // if ($request->document) {

            //     foreach ($request->document as $key => $document) {
            //         if (!empty($document)) {
            //             $filenameWithExt = $request->file('document')[$key]->getClientOriginalName();
            //             $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            //             $extension       = $request->file('document')[$key]->getClientOriginalExtension();
            //             $docName = Document::where('id', $key)->pluck('name')->first();
            //             $fileNameToStore = $request->name. '-' . $docName . '.' . $extension;

            //             $dir        = storage_path('uploads/document/');
            //             $image_path = $dir . $filenameWithExt;

            //             if (File::exists($image_path)) {
            //                 File::delete($image_path);
            //             }
            //             if (!file_exists($dir)) {
            //                 mkdir($dir, 0777, true);
            //             }
            //             $path = $request->file('document')[$key]->storeAs('uploads/document/', $fileNameToStore);


            //             $employee_document = EmployeeDocument::where('employee_id', $employee->employee_id)->where('document_id', $key)->first();

            //             if (!empty($employee_document)) {
            //                 $employee_document->document_value = $fileNameToStore;
            //                 $employee_document->save();
            //             } else {
            //                 $employee_document                 = new EmployeeDocument();
            //                 $employee_document->employee_id    = $employee->employee_id;
            //                 $employee_document->document_id    = $key;
            //                 $employee_document->document_value = $fileNameToStore;
            //                 $employee_document->save();
            //             }
            //         }
            //     }
            // }

            if ($request->document) {
                foreach ($request->document as $key => $document) {
                    if (!empty($document)) {
                        $filenameWithExt = $request->file('document')[$key]->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('document')[$key]->getClientOriginalExtension();
                        $docName = Document::where('id', $key)->pluck('name')->first();
                        $fileNameToStore = $request->name . '-' . $docName . '.' . $extension;

                        // Change directory to public/document
                        $dir = public_path('document/');
                        $image_path = $dir . $filenameWithExt;

                        // Delete existing file if it exists
                        if (File::exists($dir . $fileNameToStore)) {
                            File::delete($dir . $fileNameToStore);
                        }

                        // Create directory if it doesn't exist
                        if (!file_exists($dir)) {
                            mkdir($dir, 0777, true);
                        }

                        // Move uploaded file to public directory
                        $request->file('document')[$key]->move($dir, $fileNameToStore);

                        $employee_document = EmployeeDocument::where('employee_id', $employee->employee_id)
                                                        ->where('document_id', $key)
                                                        ->first();

                        if (!empty($employee_document)) {
                            $employee_document->document_value = $fileNameToStore;
                            $employee_document->save();
                        } else {
                            $employee_document = new EmployeeDocument();
                            $employee_document->employee_id = $employee->employee_id;
                            $employee_document->document_id = $key;
                            $employee_document->document_value = $fileNameToStore;
                            $employee_document->save();
                        }
                    }
                }
            }

            $employee = Employee::findOrFail($id);
            $input    = $request->all();
             if ($request->dob) {
                $input['dob'] = Carbon::parse($request->dob)->format('Y-m-d');
            }
            if ($request->company_doj) {
                $input['company_doj'] = Carbon::parse($request->company_doj)->format('Y-m-d');
            }
            if (!$request->has('is_team_leader')) {
                $input['is_team_leader'] = null;
            }

            if (!empty($input['team_leader'])) {
                $input['team_leader_id'] = $input['team_leader'];
            }
            
            if($request->has('company_doj')){
                $dateOfJoining = Carbon::parse($request->company_doj);
                $currentDate = Carbon::now();

                if ($dateOfJoining->greaterThanOrEqualTo($currentDate->subMonths(3))) {
                    $input['is_probation'] = 1;
                }else{
                    $input['is_probation'] = 0;
                }
            }

            if ($request->date_of_exit) {
                $input['date_of_exit'] = Carbon::parse($request->date_of_exit)->format('Y-m-d');
            }

            // dd($input);
            $employee->fill($input)->save();
            // if ($request->salary) {
            //     return redirect()->route('setsalary.index')->with('success', 'Employee successfully updated.');
            // }

            if (\Auth::user()->type != 'employee') {
                return redirect()->route('employee.index')->with('success', 'Employee successfully updated.');
            } else {
                return redirect()->route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id))->with('success', 'Employee successfully updated.');
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function updatetest(Request $request, $id)
    {
        if (\Auth::user()->can('Edit Employee')) {
            $employee = Employee::findOrFail($id);
            $userId = $employee->user_id;
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'dob' => 'required',
                    'personal_email' => \Illuminate\Validation\Rule::unique('users', 'personal_email')->ignore($userId),
                    'gender' => 'required',
                    'phone' => 'required|numeric',
                    'address' => 'required',
                    // 'salary' => 'required',
                    'document.*' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc,zip|max:20480',
                ]
            );

            // $validator->sometimes('shift_start', 'required', function ($input) {
            //     return empty($input->shift_start);
            // });

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $employee = Employee::findOrFail($id);
            $user = User::findOrFail($employee->user_id);
            if($request->personal_email){
                $user['personal_email'] = $request->personal_email;
            }
            $user->update();

            if ($request->document) {

                foreach ($request->document as $key => $document) {
                    if (!empty($document)) {
                        $filenameWithExt = $request->file('document')[$key]->getClientOriginalName();
                        $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension       = $request->file('document')[$key]->getClientOriginalExtension();
                        $docName = Document::where('id', $key)->pluck('name')->first();
                        $fileNameToStore = $request->name. '-' . $docName . '.' . $extension;

                        $dir        = storage_path('uploads/document/');
                        $image_path = $dir . $filenameWithExt;

                        if (File::exists($image_path)) {
                            File::delete($image_path);
                        }
                        if (!file_exists($dir)) {
                            mkdir($dir, 0777, true);
                        }
                        $path = $request->file('document')[$key]->storeAs('uploads/document/', $fileNameToStore);


                        $employee_document = EmployeeDocument::where('employee_id', $employee->employee_id)->where('document_id', $key)->first();

                        if (!empty($employee_document)) {
                            $employee_document->document_value = $fileNameToStore;
                            $employee_document->save();
                        } else {
                            $employee_document                 = new EmployeeDocument();
                            $employee_document->employee_id    = $employee->employee_id;
                            $employee_document->document_id    = $key;
                            $employee_document->document_value = $fileNameToStore;
                            $employee_document->save();
                        }
                    }
                }
            }

            $employee = Employee::findOrFail($id);
            $input    = $request->all();
             if ($request->dob) {
                $input['dob'] = Carbon::parse($request->dob)->format('Y-m-d');
            }
            if ($request->company_doj) {
                $input['company_doj'] = Carbon::parse($request->company_doj)->format('Y-m-d');
            }
            if (!$request->has('is_team_leader')) {
                $input['is_team_leader'] = null;
            }

            if (!empty($input['team_leader'])) {
                $input['team_leader_id'] = $input['team_leader'];
            }
            
            if($request->has('company_doj')){
                $dateOfJoining = Carbon::parse($request->company_doj);
                $currentDate = Carbon::now();

                if ($dateOfJoining->greaterThanOrEqualTo($currentDate->subMonths(3))) {
                    $input['is_probation'] = 1;
                }else{
                    $input['is_probation'] = 0;
                }
            }

            if ($request->date_of_exit) {
                $input['date_of_exit'] = Carbon::parse($request->date_of_exit)->format('Y-m-d');
            }

            // dd($input);
            $employee->fill($input)->save();
            // if ($request->salary) {
            //     return redirect()->route('setsalary.index')->with('success', 'Employee successfully updated.');
            // }

            if (\Auth::user()->type != 'employee') {
                return redirect()->route('employee.index')->with('success', 'Employee successfully updated.');
            } else {
                return redirect()->route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employee->id))->with('success', 'Employee successfully updated.');
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->can('Delete Employee')) {
            $employee      = Employee::findOrFail($id);
            $user          = User::where('id', '=', $employee->user_id)->first();
            $emp_documents = EmployeeDocument::where('employee_id', $employee->employee_id)->get();
            
            // Update directory path
            $dir = public_path('document/');
            
            foreach ($emp_documents as $emp_document) {
                if (!empty($emp_document->document_value)) {
                    if (File::exists($dir . $emp_document->document_value)) {
                        File::delete($dir . $emp_document->document_value);
                    }
                }
                $emp_document->delete();
            }
            
            $employee->delete();
            $user->delete();

            return redirect()->route('employee.index')->with('success', 'Employee successfully deleted.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
 
    public function show($id)
    {
   
        if (\Auth::user()->can('Show Employee')) {
        
        //    return $empId        = $id;
            $empId        = Crypt::decrypt($id);
            $documents    = Document::where('created_by', \Auth::user()->creatorId())->get();
            $branches     = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments  = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations = Designation::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $employee     = Employee::with('termination','resignation')->find($empId);
            if(isset(\Auth::user()->employee)){
                $employee['currentUEmpID']     = \Auth::user()->employee->id;
            }
            if ($employee) {
                $teamLeaderDetails = $employee->getTeamLeaderNameAndId();
                $employeesId  = \Auth::user()->employeeIdFormat($employee->employee_id);
                $leaves = LeaveType::leftJoin('employees', function($join) use ($empId) {
                        $join->on('employees.id', '=', DB::raw($empId));
                    })
                    ->leftJoin('leaves', function($join) use ($employee) {
                        $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                            ->where('leaves.employee_id', '=', $employee->id);
                    })
                    ->where('leave_types.created_by', '=', \Auth::user()->creatorId())
                    ->select('leave_types.id', 'leave_types.title', 'leave_types.days')
                    ->groupBy('leave_types.id', 'leave_types.title', 'employees.paid_leave_balance', 'leave_types.days')
                    ->get();
            } else {
                $teamLeaderDetails = '';
                $employeesId = '';
                $leaves = [];
            }

            return view('employee.show', compact('employee', 'employeesId', 'branches', 'departments', 'designations', 'documents', 'leaves', 'teamLeaderDetails'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function employeeDeactivateLeaves($id)
    {
        $employee     = Employee::find($id);
        $user = User::find($employee['user_id']);

        $employee['is_active'] = 0;
        $employee->update();

        $user['is_active'] = 0;
        $user->update();

        return redirect()->back()->with('success', 'Employee successfully deactivated.');
    }

    public function employeeActivateLeaves($id)
    {
        $employee     = Employee::find($id);
        $user = User::find($employee['user_id']);

        $employee['is_active'] = 1;
        $employee->update();

        $user['is_active'] = 1;
        $user->update();

        return redirect()->back()->with('success', 'Employee successfully activated.');
    }

    function employeeNumber()
    {
        $latest = Employee::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if (!$latest) {
            return 1;
        }
        
        if($latest->id > 0 && $latest->id < 10)
            $latest->id = '0'.$latest->id;

        return $latest->id + 1;
    }

    public function export()
    {
        $name = 'employee_' . date('Y-m-d i:h:s');
        $data = Excel::download(new EmployeesExport(), $name . '.xlsx'); ob_end_clean();

        return $data;
    }

    public function importFile()
    {
        return view('employee.import');
    }

    public function import(Request $request)
    {
        $rules = [
            'file' => 'required|mimes:csv,txt',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $employees = (new EmployeesImport())->toArray(request()->file('file'))[0];
        $totalCustomer = count($employees) - 1;
        $errorArray    = [];

        for ($i = 1; $i <= count($employees) - 1; $i++) {

            $employee = $employees[$i];
            $employeeByEmail = Employee::where('email', $employee[5])->first();
            $userByEmail = User::where('email', $employee[5])->first();
            // dd($userByEmail);

            if (!empty($employeeByEmail) && !empty($userByEmail)) {

                $employeeData = $employeeByEmail;
            } else {


                $user = new User();
                $user->name = $employee[0];
                $user->email = $employee[5];
                $user->password = Hash::make($employee[6]);
                $user->type = 'employee';
                $user->lang = 'en';
                $user->created_by = \Auth::user()->creatorId();
                $user->save();
                $user->assignRole('Employee');

                $employeeData = new Employee();
                $employeeData->employee_id      = $this->employeeNumber();
                $employeeData->user_id             = $user->id;
            }


            $employeeData->name                = $employee[0];
            $employeeData->dob                 = $employee[1];
            $employeeData->gender              = $employee[2];
            $employeeData->phone               = $employee[3];
            $employeeData->address             = $employee[4];
            $employeeData->email               = $employee[5];
            $employeeData->password            = Hash::make($employee[6]);
            $employeeData->branch_id           = $employee[8];
            $employeeData->department_id       = $employee[9];
            $employeeData->designation_id      = $employee[10];
            $employeeData->company_doj         = $employee[11];
            $employeeData->account_holder_name = $employee[12];
            $employeeData->account_number      = $employee[13];
            $employeeData->bank_name           = $employee[14];
            $employeeData->bank_identifier_code = $employee[15];
            $employeeData->branch_location     = $employee[16];
            $employeeData->tax_payer_id        = $employee[17];
            $employeeData->created_by          = \Auth::user()->creatorId();

            if (empty($employeeData)) {

                $errorArray[] = $employeeData;
            } else {

                $employeeData->save();
            }
        }

        $errorRecord = [];

        if (empty($errorArray)) {
            $data['status'] = 'success';
            $data['msg']    = __('Record successfully imported');
        } else {
            $data['status'] = 'error';
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalCustomer . ' ' . 'record');


            foreach ($errorArray as $errorData) {

                $errorRecord[] = implode(',', $errorData);
            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }

    /**
     * Get designations by department with enhanced validation and error handling
     */
    public function json(Request $request)
    {
        try {
            // Validate the request
            $validator = \Validator::make($request->all(), [
                'department_id' => 'required|integer|exists:departments,id'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Invalid department selection',
                    'details' => $validator->errors()->first()
                ], 400);
            }
            
            // Check user permissions
            if (!\Auth::user()->can('Create Employee')) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
            
            // Fetch designations with proper filtering
            $designations = Designation::where('department_id', $request->department_id)
                ->where('created_by', \Auth::user()->creatorId())
                ->orderBy('name', 'asc')
                ->get()
                ->pluck('name', 'id')
                ->toArray();
                
            // Log the action for debugging
            \Log::info('Designations fetched by department', [
                'department_id' => $request->department_id,
                'user_id' => \Auth::user()->id,
                'designations_count' => count($designations)
            ]);
            
            return response()->json($designations);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching designations by department: ' . $e->getMessage(), [
                'department_id' => $request->department_id ?? null,
                'user_id' => \Auth::user()->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Server error occurred while fetching designations'
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        if (\Auth::user()->can('Manage Employee Profile')) {
            $employees = Employee::where('created_by', \Auth::user()->creatorId());
            if (!empty($request->branch)) {
                $employees->where('branch_id', $request->branch);
            }
            if (!empty($request->department)) {
                $employees->where('department_id', $request->department);
            }
            if (!empty($request->designation)) {
                $employees->where('designation_id', $request->designation);
            }
            $employees = $employees->get();

            $brances = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $brances->prepend('All', '');

            $departments = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments->prepend('All', '');

            $designations = Designation::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations->prepend('All', '');

            $leaves='';
            /*$leaves = LeaveType::leftJoin('employees', function($join) use ($empId) {
                    $join->on('employees.id', '=', DB::raw($empId));
                })
                ->leftJoin('leaves', function($join) use ($employee) {
                    $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                        ->where('leaves.employee_id', '=', $employee->id);
                })
                ->where('leave_types.created_by', '=', \Auth::user()->creatorId())
                ->select('leave_types.id', 'leave_types.title', 'leave_types.days')
                ->groupBy('leave_types.id', 'leave_types.title', 'employees.paid_leave_balance', 'leave_types.days')
                ->get();*/

            return view('employee.profile', compact('employees', 'departments', 'designations', 'brances', 'leaves'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function profileShow($id)
    {
        if (\Auth::user()->can('Show Employee Profile')) {
            $empId        = Crypt::decrypt($id);
            $documents    = Document::where('created_by', \Auth::user()->creatorId())->get();
            $branches     = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments  = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $designations = Designation::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $employee     = Employee::find($empId);
            $employeesId  = \Auth::user()->employeeIdFormat($employee->employee_id);

            $leaves = LeaveType::leftJoin('employees', function($join) use ($empId) {
                    $join->on('employees.id', '=', DB::raw($empId));
                })
                ->leftJoin('leaves', function($join) use ($employee) {
                    $join->on('leave_types.id', '=', 'leaves.leave_type_id')
                        ->where('leaves.employee_id', '=', $employee->id);
                })
                ->where('leave_types.created_by', '=', \Auth::user()->creatorId())
                ->select('leave_types.id', 'leave_types.title', 'leave_types.days')
                ->groupBy('leave_types.id', 'leave_types.title', 'employees.paid_leave_balance', 'leave_types.days')
                ->get();

            return view('employee.show', compact('employee', 'employeesId', 'branches', 'departments', 'designations', 'documents', 'leaves'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function lastLogin()
    {
        // $users = User::where('created_by', \Auth::user()->creatorId())->where('is_active', 1)->orderBy('last_login', 'DESC')->get();
        $users = User::where('is_active', 1)->orderBy('last_login', 'DESC')->get();

        return view('employee.lastLogin', compact('users'));
    }

    public function employeeJson(Request $request)
    {
        $employees = Employee::where('branch_id', $request->branch)->get()->pluck('name', 'id')->toArray();

        return response()->json($employees);
    }

    /**
     * Get team leaders by branch and department with enhanced validation and error handling
     */
    public function getTeamLeader(Request $request)
    {
        try {
            // Validate the request
            $validator = \Validator::make($request->all(), [
                'branchId' => 'required|integer|exists:branches,id',
                'departmentId' => 'required|integer|exists:departments,id'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Invalid branch or department selection',
                    'details' => $validator->errors()->first()
                ], 400);
            }
            
            // Check user permissions
            if (!\Auth::user()->can('Create Employee')) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
            
            // Fetch team leaders with proper filtering
            $teamLeaders = Employee::select('id', 'name', 'employee_id')
                ->where('branch_id', $request->branchId)
                ->where('department_id', $request->departmentId)
                ->where('is_team_leader', 1)
                ->where('is_active', 1)
                ->where('created_by', \Auth::user()->creatorId())
                ->orderBy('name', 'asc')
                ->get();
                
            // Log the action for debugging
            \Log::info('Team leaders fetched by branch and department', [
                'branch_id' => $request->branchId,
                'department_id' => $request->departmentId,
                'user_id' => \Auth::user()->id,
                'team_leaders_count' => $teamLeaders->count()
            ]);
            
            return response()->json($teamLeaders);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching team leaders: ' . $e->getMessage(), [
                'branch_id' => $request->branchId ?? null,
                'department_id' => $request->departmentId ?? null,
                'user_id' => \Auth::user()->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Server error occurred while fetching team leaders'
            ], 500);
        }
    }

    public function getMyTeam()
    {
        $query = Employee::select('*')->with('attendanceEmployees', 'branch', 'department', 'designation')->where('is_active', 1)->where('created_by', \Auth::user()->creatorId())->where('team_leader_id', \Auth::user()->employee->id);
        if (isset($_GET['type']) && !empty($_GET['type'])) {
            $query->where('is_probation', 1);
        }else{
            $query->whereIn('is_probation', [0,1]);
        }

        $employees = $query->get();
        return view('employee.team-members', compact('employees'));
    }

    public function memberLeaves()
    {
        // $employees = Employee::with('employeeLeaves')->where('team_leader_id', \Auth::user()->employee->id)->where('is_active', 1)->get();
        // $Empleaves = collect();
        // foreach($employees as $employee){
        //     $Empleaves[] = $Empleaves->merge($employee['employeeLeaves']);
        // }
        // $leaves = $Empleaves->flatten()->unique()->sortByDesc('start_date');
        $leaves = Employee::with('employeeLeaves')->where('team_leader_id', \Auth::user()->employee->id)->where('is_active', 1)->get()->sortByDesc(function($employee) {
            return $employee->employeeLeaves->isEmpty()
                ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', '1900-01-01 00:00:00')
                : $employee->employeeLeaves->first()->applied_on;
        });
        $selfLeaves = false;
        return view('leave.index', compact('leaves', 'selfLeaves'));

    }

    public function getExitEmployee()
    {
        $employees = Employee::select('*')->with('attendanceEmployees')->where('is_active', 0)->where('created_by', \Auth::user()->creatorId())->get();
        return view('employee.exit-employee', compact('employees'));
    }

    /**
     * Get departments by office with enhanced validation and error handling
     */
    public function getDepartmentsByOffice(Request $request)
    {
        try {
            // Validate the request
            $validator = \Validator::make($request->all(), [
                'office_id' => 'required|integer|exists:offices,id'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Invalid office selection',
                    'details' => $validator->errors()->first()
                ], 400);
            }
            
            // Check user permissions
            if (!\Auth::user()->can('Create Employee')) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
            
            // Fetch departments with proper filtering
            $departments = Department::where('office_id', $request->office_id)
                ->where('created_by', \Auth::user()->creatorId())
                ->orderBy('name', 'asc')
                ->get()
                ->pluck('name', 'id')
                ->toArray();
                
            // Log the action for debugging
            \Log::info('Departments fetched by office', [
                'office_id' => $request->office_id,
                'user_id' => \Auth::user()->id,
                'departments_count' => count($departments)
            ]);
            
            return response()->json($departments);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching departments by office: ' . $e->getMessage(), [
                'office_id' => $request->office_id ?? null,
                'user_id' => \Auth::user()->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Server error occurred while fetching departments'
            ], 500);
        }
    }

    /**
     * Get departments by branch with enhanced validation and error handling
     */
    public function getDepartmentsByBranch(Request $request)
    {
        try {
            // Validate the request
            $validator = \Validator::make($request->all(), [
                'branch_id' => 'required|integer|exists:branches,id'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Invalid branch selection',
                    'details' => $validator->errors()->first()
                ], 400);
            }
            
            // Check user permissions
            if (!\Auth::user()->can('Create Employee')) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
            
            // Fetch departments with proper filtering
            $departments = Department::where('branch_id', $request->branch_id)
                ->where('created_by', \Auth::user()->creatorId())
                ->orderBy('name', 'asc')
                ->get()
                ->pluck('name', 'id')
                ->toArray();
                
            // Log the action for debugging
            \Log::info('Departments fetched by branch', [
                'branch_id' => $request->branch_id,
                'user_id' => \Auth::user()->id,
                'departments_count' => count($departments)
            ]);
            
            return response()->json($departments);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching departments by branch: ' . $e->getMessage(), [
                'branch_id' => $request->branch_id ?? null,
                'user_id' => \Auth::user()->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Server error occurred while fetching departments'
            ], 500);
        }
    }

    /**
     * Get team leaders by branch with enhanced validation and error handling
     */
    public function getTeamLeadersByBranch(Request $request)
    {
        try {
            // Validate the request
            $validator = \Validator::make($request->all(), [
                'branch_id' => 'required|integer|exists:branches,id'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Invalid branch selection',
                    'details' => $validator->errors()->first()
                ], 400);
            }
            
            // Check user permissions
            if (!\Auth::user()->can('Create Employee')) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
            
            // Fetch team leaders with proper filtering
            $teamLeaders = Employee::select('id', 'name', 'employee_id')
                ->where('branch_id', $request->branch_id)
                ->where('is_team_leader', 1)
                ->where('is_active', 1)
                ->where('created_by', \Auth::user()->creatorId())
                ->orderBy('name', 'asc')
                ->get();
                
            // Log the action for debugging
            \Log::info('Team leaders fetched by branch', [
                'branch_id' => $request->branch_id,
                'user_id' => \Auth::user()->id,
                'team_leaders_count' => $teamLeaders->count()
            ]);
            
            return response()->json($teamLeaders);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching team leaders by branch: ' . $e->getMessage(), [
                'branch_id' => $request->branch_id ?? null,
                'user_id' => \Auth::user()->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Server error occurred while fetching team leaders'
            ], 500);
        }
    }
}
