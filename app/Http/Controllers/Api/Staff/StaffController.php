<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\UserCreate;
use App\Models\Notification;
use App\Models\Utility;
use App\Helpers\Helper;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;


class StaffController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/staff/user/role",
     *     tags={"Staff Sidebar"},
     *     summary="Get user roles",
     *     description="Returns a list of user roles based on the authenticated user's permissions.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(
     *                     property="roles",
     *                     type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=11),
     *                         @OA\Property(property="name", type="string", example="HR Growzify"),
     *                         @OA\Property(property="created_by", type="integer", example=71)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function role()
    {
        try {
            $auth = Auth::user();
            $role = Role::where([
                'guard_name'=> 'web',
                'created_by'=> $auth->creatorId(),
            ])->select('id','name','created_by')->get();
            $data = [
                'roles'=>$role
            ];
            return $this->successResponse($data);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/staff/user",
     *     tags={"Staff Sidebar"},
     *     summary="Get list of users",
     *     description="Returns a list of users based on the authenticated user's permissions.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="user_id", type="integer", example=3),
     *                     @OA\Property(property="name", type="string", example="Swati Negi"),
     *                     @OA\Property(property="type", type="string", example="hr"),
     *                     @OA\Property(property="email", type="string", example="swati@qubifytech.com"),
     *                     @OA\Property(property="avatar", type="string", example="")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function user(Request $request)
    {
        try {
            $auth = \Auth::user();
            if ($auth->getAllPermissions()->pluck('name')->contains('Manage User'))
            {
                if ($auth->type == 'super admin') {
                    $users = User::where('created_by', '=', $auth->creatorId())
                    ->where('type', '=', 'company')->get();
                } else {
                    $users = User::where('created_by', '=', $auth->creatorId())
                    ->where('type', '!=', 'employee')->get();
                }
                $users = array_map(function($user) {
                    return [
                        'user_id' => $user['id'],
                        'name' => $user['name'],
                        'type' => $user['type'],
                        'email' => $user['email'],
                        'avatar' => $user['avatar'],
                    ];
                }, $users->toarray()??[]);
                return $this->successResponse($users);
            }
            else {
                return $this->errorResponse('Permission denied.', 403);
            }
        } catch (\Throwable $th) {
            // Catch any other unexpected errors
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/staff/user",
     *     tags={"Staff Sidebar"},
     *     summary="Create a new user",
     *     description="Creates a new user based on the authenticated user's permissions.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123"),
     *             @OA\Property(property="role", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User  successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User  successfully created."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                 @OA\Property(property="type", type="string", example="company"),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email has already been taken.")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password must be at least 8 characters.")),
     *                 @OA\Property(property="role", type="array", @OA\Items(type="string", example="The selected role is invalid."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function store(Request $request)
    {
        try {
            $auth = \Auth::user();

            // Check if the authenticated user has permission to create a user
            if (!$auth->getAllPermissions()->pluck('name')->contains('Create User')) {
                return $this->errorResponse('Permission denied.', 403);
            }

            // Get the default language setting
            $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->first();

            // Validate the incoming request data
            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|numeric|exists:roles,id',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return $this->errorResponse('Validation error', 400, $validator->errors()->toArray());
            }

            // Create the user based on the type of authenticated user
            if ($auth->type == 'super admin') {
                $user = User::create([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => Hash::make($request['password']),
                    'type' => 'company',
                    'plan' => 0,
                    'lang' => !empty($default_language) ? $default_language->value : '',
                    'created_by' => $auth->id,
                ]);

                $user->assignRole('Company');
                Utility::jobStage($user->id);
            } else {
                $total_user = $auth->countUsers();
                $plan = Plan::find($auth->plan);
                if ($total_user < $plan->max_users || $plan->max_users == -1) {
                    $role_r = Role::findById($request->role);
                    $user = User::create([
                        'name' => $request['name'],
                        'email' => $request['email'],
                        'password' => Hash::make($request['password']),
                        'type' => $role_r->name,
                        'lang' => !empty($default_language) ? $default_language->value : '',
                        'created_by' => $auth->id,
                    ]);
                    $user->assignRole($role_r);
                } else {
                    return $this->errorResponse(__('Your user limit is over, Please upgrade plan.'), 403);
                }
            }

            // Send email and notification if settings allow
            $settings = Utility::settings();
            if ($settings['user_create'] == 1) {
                try {
                    Mail::to($user->email)->send(new UserCreate($user));
                } catch (\Exception $e) {
                    \Log::error("E-Mail Error: " . $e->getMessage());
                    return $this->errorResponse(__('E-Mail has not been sent due to SMTP configuration'), 500);
                }

                // Prepare notification data
                $notificationData = [
                    'title' => 'New User Created',
                    'body' => "A new user named {$user->name} has been created successfully.",
                    'user_id' => [2, 3, 5], // Specify the user to send to
                    'created_by' => $auth->name,
                ];
                try {
                    Helper::sendNotification($notificationData);
                } catch (\Exception $e) {
                    \Log::error("Notification Error: " . $e->getMessage());
                }
            }

            return $this->successResponse($user, __('User  successfully created.'));
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/staff/user/{id}",
     *     tags={"Staff Sidebar"},
     *     summary="Update an existing user",
     *     description="Updates the details of a user based on the provided user ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="role", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User  successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User  successfully updated."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                 @OA\Property(property="type", type="string", example="company"),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email has already been taken.")),
     *                 @OA\Property(property="role", type="array", @OA\Items(type="string", example="The selected role is invalid."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied or User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    // Update an existing user
    public function update(Request $request, $id)
    {
        try {
            $auth = \Auth::user();
            if (!$auth->getAllPermissions()->pluck('name')->contains('Edit User')) {
                return $this->errorResponse('Permission denied.', 403);
            }

            // Validate the incoming request data
            $validator = \Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users,email,' . $id,
                'role' => 'nullable|numeric|exists:roles,id', // Optional role validation
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return $this->errorResponse('Validation error', 400, $validator->errors()->toArray());
            }

            // Find the user by ID
            $user = User::find($id);
            if (!$user) {
                return $this->errorResponse('User not found.', 403);
            }
            // Update user details
            if ($auth->type == 'super admin') {
                $user->fill($request->all());
            } else {
                // If not super admin, update the role as well
                if ($request->has('role')) {
                    $role = Role::findById($request->role);
                    $user->type = $role->name;
                    $user->assignRole($role);
                }
                $user->fill($request->all());
            }
            // Save the updated user
            $user->save();

            return $this->successResponse($user, 'User  successfully updated.');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/staff/delete/user/{id}",
     *     tags={"Staff Sidebar"},
     *     summary="Delete a user",
     *     description="Deletes a user based on the provided user ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User  deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User  deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User  not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User  not exists.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    // Delete a user
    public function delete($id)
    {
        try {
            $auth = \Auth::user();
            if ($auth->getAllPermissions()->pluck('name')->contains('Delete User')) {
                $user = User::find($id);
                if(!$user)
                {
                    return $this->errorResponse('User not exists.', 404);
                }
                $user->delete();

                return $this->successResponse(null, 'User  deleted successfully.');
            } else {
                return $this->errorResponse('Permission denied.', 403);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    // Change user password
    public function changePass(Request $request, $id)
    {
        try {
            $auth = \Auth::user();
            if ($auth->getAllPermissions()->pluck('name')->contains('Edit User')) {
                $user = User::find($id);
                if(!$user)
                {
                    return $this->errorResponse('User not exists.', 404);
                }
                 // Validate the incoming request data
                $validator = \Validator::make($request->all(), [
                    'password' => 'required|string|min:8|confirmed',
                ]);

                $user->password = Hash::make($request->password);
                $user->save();

                return $this->successResponse(null, 'Password changed successfully.');
            } else {
                return $this->errorResponse('Permission denied.', 403);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }
}
