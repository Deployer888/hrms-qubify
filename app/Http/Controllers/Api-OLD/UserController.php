<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function updatePassword(Request $request)
    {
        $requestData = json_decode(json_encode($request->all()));
        $objUser = User::find($requestData->userID);

        if ($objUser) 
        {
            $new_password = $request->new_password;
            $current_password = $request->current_password;
            $password = $objUser->password;
            
            if (Hash::check($current_password, $password)) {
                $objUser->password = Hash::make($new_password);
                $objUser->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Password updated successfully'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating password. Please verify data!'
                ], 500);
            }
        } else {
            return redirect()->route('profile', \Auth::user()->id)->with('error', __('Something is wrong.'));
        }
    }
    
    public function editprofile(Request $request)
    {
return        $requestData = json_decode($request->all());
        $user = User::findOrFail($requestData->userID);
        $fileNameToStore = '';
        
if($requestData->profile){
    return response()->json([
            'success' => true,
            'message' => 'IN'
        ], 200);
}
   return response()->json([
            'message' => $requestData
        ], 200);
        
        if ($requestData->hasFile('profile')) 
        {
            $filenameWithExt = $requestData->file('profile')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $requestData->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $dir             = storage_path('uploads/avatar/');
            $image_path      = $dir . $user->avatar;

            if (File::exists($image_path)) {
                File::delete($image_path);
            }
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            
            $path = $requestData->file('profile')->storeAs('uploads/avatar/', $fileNameToStore);
            $user['avatar'] = $fileNameToStore;
        }
       
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ], 200);
    }
}
