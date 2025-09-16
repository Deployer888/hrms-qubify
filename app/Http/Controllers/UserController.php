<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Invoice;
use App\Mail\UserCreate;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use App\Helpers\Helper;

class UserController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('Manage User')) {
            $user = \Auth::user();
            if (\Auth::user()->type == 'super admin') {
                $users = User::where('created_by', '=', $user->creatorId())->where('type', '=', 'company')->get();
            } else {
                $users = User::where('created_by', '=', $user->creatorId())->where('type', '!=', 'employee')->get();
            }

            return view('user.index', compact('users'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function updateFcmToken(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        // if(!$user->fcm_token || $user->fcm_token == NULL){
            $user->fcm_token = $request->fcm_token;
            $user->save();
        // }

        return response()->json(['message' => 'FCM token updated successfully']);
    }

    public function create()
    {
        if (\Auth::user()->can('Create User')) {
            $user  = \Auth::user();
            $roles = Role::where('created_by', '=', $user->creatorId())->where('guard_name', 'web')->where('name', '!=', 'employee')->get()->pluck('name', 'id');

            return view('user.create', compact('roles'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('Create User')) {
            $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->first();
            
            // Dynamic validation rules based on user type
            $validationRules = [
                'name' => 'required',
                'email' => 'required|unique:users',
                'password' => 'required',
            ];
            
            // Add company_name validation for super admin
            if (\Auth::user()->type == 'super admin') {
                $validationRules['company_name'] = 'required|string|max:255';
            }
            
            $validator = \Validator::make($request->all(), $validationRules);
            
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if (\Auth::user()->type == 'super admin') {
                // Create company user with company_name
                $user = User::create([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => Hash::make($request['password']),
                    'company_name' => $request['company_name'], // Store company name
                    'type' => 'company',
                    'plan' => 0,
                    'lang' => !empty($default_language) ? $default_language->value : '',
                    'created_by' => \Auth::user()->id,
                ]);

                $user->assignRole('Company');
                Utility::jobStage($user->id);
                $role_r = Role::findById(2);
                
            } else {
                // Regular user creation logic
                $objUser = \Auth::user();
                $total_user = $objUser->countUsers();
                $plan = Plan::find($objUser->plan);
                
                if ($total_user < $plan->max_users || $plan->max_users == -1) {
                    $role_r = Role::findById($request->role);
                    
                    $userData = [
                        'name' => $request['name'],
                        'email' => $request['email'],
                        'password' => Hash::make($request['password']),
                        'type' => $role_r->name,
                        'lang' => !empty($default_language) ? $default_language->value : '',
                        'created_by' => \Auth::user()->id,
                    ];
                    
                    // Add company_name if provided (optional for regular users)
                    if ($request->filled('company_name')) {
                        $userData['company_name'] = $request['company_name'];
                    }
                    
                    $user = User::create($userData);
                    $user->assignRole($role_r);
                    
                    // If $role_r is already an array of roles
                    $roles = is_array($role_r) ? $role_r : [$role_r];
                    $roles[] = 'employee';

                    // Assign all roles at once
                    $user->syncRoles($roles);

                    // Create employee record for the user
                    $employee = Employee::create([
                        'user_id' => $user->id,
                        'name' => $request['name'],
                        'email' => $request['email'],
                        'password' => Hash::make($request['password']),
                        'employee_id' => $this->employeeNumber(),
                        'created_by' => \Auth::user()->creatorId(),
                    ]);
                    
                } else {
                    return redirect()->back()->with('error', __('Your user limit is over, Please upgrade plan.'));
                }
            }

            // Email and notification logic
            $setings = Utility::settings();
            if ($setings['user_create'] == 1) {
                $user->type = $role_r->name;
                $user->password = $request['password'];
                
                try {
                    Mail::to($user->email)->send(new UserCreate($user));
                } catch (\Exception $e) {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }

                // Send Notification
                $notificationData = [
                    'title' => \Auth::user()->type == 'super admin' ? 'New Company Created' : 'New User Created',
                    'body' => \Auth::user()->type == 'super admin' 
                        ? "A new company '{$user->company_name}' with admin {$user->name} has been created successfully."
                        : "A new user named {$user->name} has been created successfully.",
                    'user_id' => [2,3,5],
                    'created_by' => \Auth::user()->name,
                ];
                
                try {
                    Helper::sendNotification($notificationData);
                } catch (\Exception $e) {
                    \Log::error("Notification Error: " . $e->getMessage());
                }

                $successMessage = \Auth::user()->type == 'super admin' 
                    ? __('Company successfully created.') 
                    : __('User successfully created.');
                    
                return redirect()->route('user.index')->with('success', $successMessage . (isset($smtp_error) ? $smtp_error : ''));
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
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

    public function show(User $user)
    {
        return view('profile.index');
    }

    public function edit($id)
    {
        if (\Auth::user()->can('Edit User')) {
            $user  = User::find($id);
            $roles = Role::where('created_by', '=', $user->creatorId())->where('guard_name', 'web')->where('name', '!=', 'employee')->get()->pluck('name', 'id');

            return view('user.edit', compact('user', 'roles'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'unique:users,email,' . $id,
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        if (\Auth::user()->type == 'super admin') {
            $user  = User::findOrFail($id);
            $input = $request->all();
            $user->fill($input)->save();
        } else {
            $user = User::findOrFail($id);

            $role          = Role::findById($request->role);
            $input         = $request->all();
            $input['type'] = $role->name;
            $user->fill($input)->save();

            $user->assignRole($role);
        }

        return redirect()->route('user.index')->with('success', 'User successfully updated.');
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('Delete User')) {
            $user = User::findOrFail($id);
            $user->delete();
            $employee = Employee::where('user_id', $id)->first();
            if($employee) {
                $employee->delete();
            }
            return redirect()->route('user.index')->with('success', 'User successfully deleted.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function profile()
    {
        $userDetail = \Auth::user();

        return view('user.profile')->with('userDetail', $userDetail);
    }

    public function editprofile(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = User::findOrFail($userDetail['id']);

        $validator = \Validator::make(
            $request->all(),
            [
                // 'name' => 'required|max:120',
                // 'email' => 'required|email|unique:users,email,' . $userDetail['id'],
                'profile' => 'image|mimes:jpeg,png,jpg,svg|max:3072',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        if ($request->hasFile('profile')) {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            
            // Define the correct public directory path
            $dir = public_path('storage/uploads/avatar/');
            $image_path = $dir . $userDetail['avatar'];

            // Delete existing file if it exists
            if (File::exists($image_path)) {
                File::delete($image_path);
            }
            
            // Create directory if it doesn't exist
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            
            // Move the uploaded file to the public directory
            $request->file('profile')->move($dir, $fileNameToStore);
            
            // Optional: Store the filename in database or return it
            // $userDetail['avatar'] = $fileNameToStore;
        }

        if (!empty($request->profile)) {
            $user['avatar'] = $fileNameToStore;
        }
        // $user['name']  = $request['name'];
        // $user['email'] = $request['email'];
        $user->save();

        if (\Auth::user()->type == 'employee') {
            $employee        = Employee::where('user_id', $user->id)->first();
            $employee->email = $request['email'];
            $employee->save();
        }

        return redirect()->back()->with(
            'success',
            'Profile successfully updated.'
        );
    }


    public function employeePassword($id)
    {
        $eId        = \Crypt::decrypt($id);

        $user = User::find($eId);

        $employee = User::where('id', $eId)->first();

        return view('user.reset', compact('user', 'employee'));
    }

    public function employeePasswordReset(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'password' => 'required|confirmed|same:password_confirmation',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }


        $user                 = User::where('id', $id)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->route('user.index')->with(
            'success',
            'Employee Password successfully updated.'
        );
    }


    public function updatePassword(Request $request)
    {
        if (\Auth::Check()) {
            $request->validate(
                [
                    'current_password' => 'required',
                    'new_password' => 'required|min:6',
                    'confirm_password' => 'required|same:new_password',
                ]
            );
            $objUser          = Auth::user();
            $request_data     = $request->All();
            $current_password = $objUser->password;
            if (Hash::check($request_data['current_password'], $current_password)) {
                $user_id            = Auth::User()->id;
                $obj_user           = User::find($user_id);
                $obj_user->password = Hash::make($request_data['new_password']);;
                $obj_user->save();

                return redirect()->route('profile', $objUser->id)->with('success', __('Password successfully updated.'));
            } else {
                return redirect()->route('profile', $objUser->id)->with('error', __('Please enter correct current password.'));
            }
        } else {
            return redirect()->route('profile', \Auth::user()->id)->with('error', __('Something is wrong.'));
        }
    }


    public function upgradePlan($user_id)
    {
        $user = User::find($user_id);

        $plans = Plan::get();

        return view('user.plan', compact('user', 'plans'));
    }

    public function activePlan($user_id, $plan_id)
    {
        $user       = User::find($user_id);
        $assignPlan = $user->assignPlan($plan_id);
        $plan       = Plan::find($plan_id);
        if ($assignPlan['is_success'] == true && !empty($plan)) {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            Order::create(
                [
                    'order_id' => $orderID,
                    'name' => null,
                    'card_number' => null,
                    'card_exp_month' => null,
                    'card_exp_year' => null,
                    'plan_name' => $plan->name,
                    'plan_id' => $plan->id,
                    'price' => $plan->price,
                    'price_currency' => !empty(env('CURRENCY')) ? env('CURRENCY') : '$',
                    'txn_id' => '',
                    'payment_status' => 'succeeded',
                    'receipt' => null,
                    'user_id' => $user->id,
                ]
            );

            return redirect()->back()->with('success', 'Plan successfully upgraded.');
        } else {
            return redirect()->back()->with('error', 'Plan fail to upgrade.');
        }
    }

    public function notificationSeen($user_id)
    {
        Notification::where('user_id', '=', $user_id)->update(['is_read' => 1]);

        return response()->json(['is_success' => true], 200);
    }

    public function updateOrganizationInfo(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'employees_count' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->company_name = $request->company_name;
        $user->employees_count = $request->employees_count;
        $user->save();

        return response()->json(['success' => true]);
    }
}
