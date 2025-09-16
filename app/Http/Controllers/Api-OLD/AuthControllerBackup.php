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
     *             @OA\Property(property="g-recaptcha-response", type="string", example="recaptcha-response-token")
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
     *                 @OA\Property(property="g-recaptcha-response", type="array", @OA\Items(type="string"))
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
     *     summary="Authenticate a user and generate an access token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="secretPassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-08-27T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-08-27T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        // Attempt to authenticate the user using the validated credentials
        if (!Auth::attempt($validated)) {
            // Return a JSON response with an error if authentication fails
            return response()->json(["error" => "Unauthorized"], 401);
        }
    
        // Retrieve the authenticated user
        $user = Auth::user();
        
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();
    
        // Create a new personal access token for the user
        $tokenResult = $user->createToken('authToken');
        
        // Store the token in the response array
        $success['token'] = $tokenResult->accessToken;
        
        // Add the user permissions to the response
        $success['permissions'] = $user->getAllPermissions()->pluck('name');
        
        // Add the authenticated user details to the response
        $success['user'] = $user;
        
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
