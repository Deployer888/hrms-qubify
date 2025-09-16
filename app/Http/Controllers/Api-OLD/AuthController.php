<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

class AuthController extends Controller
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
     *             @OA\Property(property="password_confirmation", type="string", example="secretPassword123"),
     *             @OA\Property(property="g_recaptcha_response", type="string", example="recaptcha-response-token")  // Changed to underscore
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
     *                 @OA\Property(property="password_confirmation", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="g_recaptcha_response", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Recaptcha validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Recaptcha validation failed.")
     *         )
     *     )
     * )
     */

    public function register(Request $request)
    {
        $validation = [];

        if (env('RECAPTCHA_MODULE') == 'yes') {
            $validation['g-recaptcha-response'] = 'required|captcha';
        }

        // $this->validate($request, $validation);

        $default_language = \DB::table('settings')->select('value')->where('name', 'default_language')->first();

        /*$request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);*/

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
    public function login(Request $request)
    {
        // Now that the input is validated, attempt authentication with email and password
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Return a JSON response if authentication fails
            return response()->json(["error" => "Unauthorized"], 401);
        }

        // Retrieve the authenticated user
        $user = Auth::user()->makeHidden('password');
        $user->last_login = date('Y-m-d H:i:s');        
        $user->device_id = $request->device_id;
        // $user->firebase_token = $request->firebase_token;
        $user->save();
        

        // Create a new personal access token for the user
        $tokenResult = $user->createToken('authToken');
        if($user->type != 'employee'){
            $success['token'] = $tokenResult->accessToken;
            $success['user_name'] = $user->name;
            $success['email'] = $user->email;
            $success['userID'] = $user->id;
            $success['user_role'] = $user->type;
            $success['avatar_url'] = 'https://hrm.qubifytech.com/storage/uploads/avatar/'.$user->avatar;
            $success['login_status'] = 'true';
            $success['permissions'] = $user->getAllPermissions()->pluck('name');
            $success['result'] = $user;
        }else{
            $departmentID = $user->getUSerEmployee($user->id)->department_id;
            $department = $user->getDepartment($departmentID)->name;
            // Store the token in the response array
            $success['token'] = $tokenResult->accessToken;
            // Add the authenticated user details to the response
            $success['user_name'] = $user->name;
            $success['email'] = $user->email;
            $success['phone'] = $user->getUSerEmployee($user->id)->phone;
            $success['userID'] = $user->id;
            $success['user_role'] = $user->type;
            $success['department'] = $department;
            $success['avatar_url'] = 'https://hrm.qubifytech.com/storage/uploads/avatar/'.$user->avatar;
            $success['login_status'] = 'true';
            $success['permissions'] = $user->getAllPermissions()->pluck('name');
            $success['result'] = $user;
        }
        // Return a JSON response with the generated token and user details
        return response()->json(["success" => $success], 200);
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
        if ($request->user()) {
            $request->user()->token()->delete(); // Use delete() instead of revoke()
            return response()->json(['message' => 'Successfully logged out'], 200);
        }

        return response()->json(['error' => 'Unauthenticated'], 401);
    }


}
