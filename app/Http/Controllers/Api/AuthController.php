<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Hash;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="secretPassword123"),
     *             @OA\Property(property="password_confirmation", type="string", example="secretPassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="A verification link has been sent to your email address.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="password_confirmation", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unprocessable Entity")
     *         )
     *     )
     * )
     */

    public function register(Request $request)
    {
         $validation = [];

         $default_language = \DB::table('settings')->select('value')->where('name', 'default_language')->first();

         $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
         ];

         $validator = Validator::make($request->all(), $rules);

         // If validation fails, return a JSON response with errors
         if ($validator->fails()) {
             return response()->json([
                 'success' => false,
                 'message' => 'Validation error',
                 'errors' => $validator->errors()
             ], 400);
         }

         $user = User::create([
             'name' => $request->name,
             'email' => $request->email,
             'password' => Hash::make($request->password),
             'type' => 'company',
             'lang' => !empty($default_language) ? $default_language->value : '',
             'plan' => 0,
             'created_by' => 1,
         ]);

         $role_r = Role::findByName('company');

         $user->assignRole($role_r);

         event(new Registered($user));

         $user->sendEmailVerificationNotification();

         return response()->json(['success' => true, 'message' => 'A verification link has been sent to your email address.'], 201);
     }

    /**
      * @OA\Post(
      *     path="/api/login",
      *     summary="User Login",
      *     description="Logs in a user and returns the authentication token along with user details.",
      *     operationId="loginUser",
      *     tags={"Authentication"},
      *     @OA\RequestBody(
      *         required=true,
      *         description="User login credentials and device details",
      *         @OA\JsonContent(
      *             required={"email", "password", "device_id", "firebase_token"},
      *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="User's email address"),
      *             @OA\Property(property="password", type="string", example="password123", description="User's password"),
      *             @OA\Property(property="device_id", type="string", example="device123", description="User's device ID"),
      *             @OA\Property(property="firebase_token", type="string", example="firebase_token_abc", description="User's Firebase token"),
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Successful login",
      *         @OA\JsonContent(
      *             @OA\Property(property="success", type="object",
      *                 @OA\Property(property="token", type="string", description="Access token"),
      *                 @OA\Property(property="user_name", type="string", description="Authenticated user's name"),
      *                 @OA\Property(property="email", type="string", description="Authenticated user's email"),
      *                 @OA\Property(property="userID", type="integer", description="Authenticated user's ID"),
      *                 @OA\Property(property="user_role", type="string", description="Authenticated user's role"),
      *                 @OA\Property(property="avatar_url", type="string", description="URL of the user's avatar"),
      *                 @OA\Property(property="login_status", type="string", example="true", description="Status of login"),
      *                 @OA\Property(property="result", type="object", description="Authenticated user's full details")
      *             )
      *         )
      *     ),
      *     @OA\Response(
      *         response=401,
      *         description="Unauthorized",
      *         @OA\JsonContent(
      *             @OA\Property(property="error", type="string", example="Unauthorized")
      *         )
      *     ),
      *     @OA\Response(
      *         response=422,
      *         description="Validation Error",
      *         @OA\JsonContent(
      *             @OA\Property(property="success", type="boolean", example=false),
      *             @OA\Property(property="errors", type="object",
      *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field is required.")),
      *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password field is required.")),
      *                 @OA\Property(property="device_id", type="array", @OA\Items(type="string", example="The device_id field is required.")),
      *                 @OA\Property(property="firebase_token", type="array", @OA\Items(type="string", example="The firebase_token field is required."))
      *             )
      *         )
      *     ),
      *     security={
      *         {"bearerAuth": {}}
      *     }
      * )
      */
    public function login(Request $request): JsonResponse
    {
        try {

            $rules = [
                'email' => 'required|string|email|max:255',
                'password' => ['required', Rules\Password::defaults()],
                'device_type' => 'required|max:255',
                'device_id' => 'required|max:255',
                'fcm_token' => 'required|string|max:1024',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->errorResponse(__('Validation error.'),200, $validator->errors()->toArray());
            }

            if ($request->device_type == 'web') {
                # code...
            }
            if($request->device_type == 'Android')
            {
                $user = User::where('email', $request->email)->first();

                if (!$user || !Hash::check($request->password, $user->password)) {
                    return $this->errorResponse(__('Unauthorized.'),200);
                }

                $user               = $user->makeHidden('password');
                $user->last_login   = date('Y-m-d H:i:s');
                $user->device_id    = $request->device_id;
                $user->device_type  = 1;
                $user->fcm_token    = $request->fcm_token;
                $user->save();

                // $user->tokens()->delete();

                $tokenResult = $user->createToken('authToken');
                $role = $this->role($user->type);

                /* $success = [
                    'token'         => $tokenResult->accessToken,
                    'role'          => $role,
                    'user_role'     => $user->type,
                    'avatar_url'    => 'https://hrm.qubifytech.com/storage/uploads/avatar/' . $user->avatar,
                    'is_login'      => true,
                    // 'permissions'    => $user->getAllPermissions()->pluck('name'),
                ]; */

                $utype = $user->type;
                if($utype == 'director'){
                    $utype = 'employee';
                }
                $success = [
                    'token'         => $tokenResult->accessToken,
                    'role'          => $role,
                    'user_role'     => $utype,
                    'avatar_url'    => 'https://hrms.qubifytech.com/storage/uploads/avatar/' . $user->avatar,
                    'is_login'      => true,
                    // 'permissions'    => $user->getAllPermissions()->pluck('name'),
                ];

                if ($user->type == 'employee') {
                    $employee               = $user->getUSerEmployee($user->id);
                    $success['phone']       = $employee->phone;
                    $success['employee_id'] = $employee->id;
                    $success['department_name']  = $user->getDepartment($employee->department_id)->name;
                    $success['department_id']  = $employee->department_id;
                    $success['is_teamLeader']  = $employee->is_team_leader;
                }
                $success['user'] = $user;
                return $this->successResponse($success);
            }
            return $this->errorResponse("The device_type support only 'Android' for now.",200);

        } catch (\Throwable $th) {
            return $this->errorResponse(__('Something went wrong.'),500);
            //throw $th;
        }
     }

    /**
      * @OA\Get(
      *     path="/api/update-last-login",
      *     summary="Update Last Login Time",
      *     description="Update the last login time of the authenticated user",
      *     tags={"User"},
      *     security={{ "bearerAuth":{} }},
      *     @OA\Response(
      *         response=200,
      *         description="Last login time updated successfully",
      *         @OA\JsonContent(
      *             @OA\Property(property="success", type="boolean", example=true),
      *             @OA\Property(property="message", type="string", example="Last login time updated successfully")
      *         )
      *     ),
      *     @OA\Response(
      *         response=401,
      *         description="User is not authenticated",
      *         @OA\JsonContent(
      *             @OA\Property(property="success", type="boolean", example=false),
      *             @OA\Property(property="message", type="string", example="User is not authenticated")
      *         )
      *     ),
      *     @OA\Response(
      *         response=500,
      *         description="An error occurred while updating last login time",
      *         @OA\JsonContent(
      *             @OA\Property(property="success", type="boolean", example=false),
      *             @OA\Property(property="message", type="string", example="An error occurred while updating last login time")
      *         )
      *     )
      * )
      */
    public function updateLastLogin(Request $request ){
        if ($request->user_id) {
            try {
                 $user = User::find($request->user_id);

                 $user->last_login = date('Y-m-d H:i:s');
                 $user->save();

                 return response()->json([
                     'success' => true,
                     'message' => 'Last login time updated successfully'
                 ], 200);

             } catch (\Exception $e) {
                 return response()->json([
                     'success' => false,
                     'message' => 'An error occurred while updating last login time: ' . $e->getMessage()
                 ], 500);
             }
        } else {
             return response()->json([
                 'success' => false,
                 'message' => 'User is not authenticated'
             ], 401);
        }
    }

     /**
      * @OA\Post(
      *     path="/api/logout",
      *     summary="Logout user",
      *     tags={"Auth"},
      *     description="Logs out the currently authenticated user by invalidating their session token.",
      *     security={{"passport":{}}},
      *     @OA\Response(
      *         response=200,
      *         description="Successfully logged out",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string", example="Successfully logged out")
      *         )
      *     ),
      *     @OA\Response(
      *         response=401,
      *         description="Unauthenticated",
      *         @OA\JsonContent(
      *             @OA\Property(property="error", type="string", example="Unauthenticated")
      *         )
      *     )
      * )
      */
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
            
            // Clear FCM token from user record
            $user->update(['fcm_token' => null]);
            
            
            return response()->json(['message' => 'Successfully logged out'], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Logout failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function addFcmToken(Request $request)
    {
        try {
            $request->validate([
                'fcm_token' => 'required|string|max:500',
            ]);
    
            $user = Auth::user();
    
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
    
            // Option 1: If FCM token is stored as a field in users table
            $user->update([
                'fcm_token' => $request->fcm_token,
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'FCM token updated successfully',
                'user_id' => $user->id
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update FCM token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function role($typ)
    {
        $userRoleMap = [
            'director' => 1,
            'employee' => 1,
            'hr'       => 2,
            'company'  => 3,
        ];
        return $userRoleMap[$typ] ?? null;
    }

    public function forgetPassword(Request $request): JsonResponse
    {
        try {
            // Handle reCAPTCHA validation if enabled
            $validation = [];
            if (env('RECAPTCHA_MODULE') == 'yes') {
                $validation['g-recaptcha-response'] = 'required|captcha';
            }
            // Validate reCAPTCHA if required
            if (!empty($validation)) {
                $request->validate($validation);
            }
            // Validate email
            $request->validate([
                'email' => 'required|email',
            ]);
 
            // Send the password reset link
            $status = Password::sendResetLink(
                $request->only('email')
            );
 
            // Return appropriate JSON response
            if ($status == Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => __($status),
                    'status' => 'success'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __($status),
                    'status' => 'error',
                    'errors' => [
                        'email' => [__($status)]
                    ]
                ], 422);
            }
 
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request',
                'status' => 'error'
            ], 500);
        }
    }

}
