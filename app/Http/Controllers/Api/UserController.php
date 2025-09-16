<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
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
use Illuminate\Support\Facades\Validator;


class UserController extends BaseController
{
    public function profile()
    {
        try {
            return $this->successResponse(Auth::user());
            //code...
        } catch (\Throwable $th) {
            return errorResponse($th->getMessage());
            //throw $th;
        }
    }
    /**
     * @OA\Get(
     *     path="/api/get-user/{id}",
     *     summary="Get User by ID",
     *     description="Retrieve a user by their ID",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-09-13 12:34:56"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-09-13 12:34:56")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error retrieving user",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving user")
     *         )
     *     )
     * )
     */
    public function getUser($id){
        try {
            $user = User::find($id);
            return $this->successResponse($user);
            //code...
        } catch (\Throwable $th) {
            return errorResponse($th->getMessage());
            //throw $th;
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            // Validate the request data
            $rules = [
                'current_password' => 'required',
                'new_password' => 'required|min:8',
            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->errorResponse('Validation error', 400,$validator->errors()->toarray());
            }
            $user = Auth::User();
            // Get the new and current passwords from the request
            $new_password = $request->new_password;
            $current_password = $request->current_password;

            // Verify the current password
            if (!Hash::check($current_password, $user->password)) {
                return $this->errorResponse('Current password is incorrect', 400);
            }

            // Update the password
            $user->password = Hash::make($new_password);
            $user->save();

            // Return a success response
            return $this->successResponse($user,'Password updated successfully');

        } catch (\Throwable $th) {
            // Catch any unexpected errors and return a 500 error
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    // public function updateProfile(Request $request)
    // {
    //     try {
    //         // Validation rules
    //         $rules = [
    //             'profile_pic' => [
    //                 'required',
    //                 'image',
    //                 'mimes:jpg,png,jpeg,gif,svg',
    //                 'dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000',
    //                 'max:2048'
    //             ]
    //         ];

    //         // Validate the request
    //         $validator = Validator::make($request->all(), $rules);

    //         // If validation fails, return error response
    //         if ($validator->fails()) {
    //             return $this->errorResponse('Validation error!', 422,$validator->errors()->toarray());
    //         }

    //         // Find the user (assuming the user is authenticated)
    //         $user = auth()->user();

    //         // Handle profile picture upload
    //         $dir = storage_path('uploads/avatar/');
    //         if ($request->hasFile('profile_pic')) {

    //             // Ensure the directory exists
    //             if (!file_exists($dir)) {
    //                 mkdir($dir, 0777, true);
    //             }

    //             // Delete the old avatar if it exists
    //             if ($user->avatar && File::exists($dir . $user->avatar)) {
    //                 File::delete($dir . $user->avatar);
    //             }

    //             // Generate a unique filename
    //             $filenameWithExt = $request->file('profile_pic')->getClientOriginalName();
    //             $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
    //             $extension = $request->file('profile_pic')->getClientOriginalExtension();
    //             $fileNameToStore = $filename . '_' . time() . '.' . $extension;

    //             // Store the new avatar
    //             $path = $request->file('profile_pic')->storeAs('uploads/avatar/', $fileNameToStore);
    //             $user->avatar = $fileNameToStore;
    //         }

    //         // Save the user
    //         $user->save();
    //         $data = [
    //             'avatar' => $user->avatar,
    //         ];

    //         // Return success response
    //         return $this->successResponse($data,'Profile updated successfully');

    //     } catch (\Throwable $th) {
    //         // Catch any unexpected errors and return a 500 error
    //         return $this->errorResponse($th->getMessage(), 500);
    //     }
    // }

    public function updateProfile(Request $request)
    {
        try {
            // Validation rules
            $rules = [
                'image_base64' => [
                    'required',
                    'string'
                ]
            ];

            // Validate the request
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, return error response
            if ($validator->fails()) {
                return $this->errorResponse('Validation error!', 422, $validator->errors()->toArray());
            }

            $user = auth()->user();
            $base64Image = $request->input('image_base64');
            $user->base64 = $base64Image; // if you want to store the raw base64 string too
            $user->save();

            return $this->successResponse($user,'Profile updated successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }



}

