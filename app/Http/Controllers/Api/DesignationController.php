<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Designation;
use App\Helpers\Helper;

class DesignationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/designations",
     *     summary="Get a list of designations",
     *     description="Retrieve a list of designations created by the authenticated user.",
     *     tags={"Designations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Designations retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": true,
     *                 "message": "Designations retrieved successfully.",
     *                 "data": {{
     *                     "id": 1,
     *                     "name": "Manager",
     *                     "created_by": 1,
     *                     "created_at": "2023-09-23T10:11:12.000000Z",
     *                     "updated_at": "2023-09-23T10:11:12.000000Z"
     *                 }, {
     *                     "id": 2,
     *                     "name": "Developer",
     *                     "created_by": 1,
     *                     "created_at": "2023-09-23T10:11:12.000000Z",
     *                     "updated_at": "2023-09-23T10:11:12.000000Z"
     *                 }}
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": false,
     *                 "message": "Permission denied."
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": false,
     *                 "message": "An error occurred. Please try again later.",
     *                 "error": "Exception message here"
     *             }
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            if (Helper::check_permissions('Manage Designation')) {
                $designations = Designation::where('created_by', \Auth::user()->creatorId())->get();

                return response()->json([
                    'success' => true,
                    'message' => __('Designations retrieved successfully.'),
                    'data' => $designations,
                ], 200);  
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Permission denied.'),
                ], 403); 
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred. Please try again later.'),
                'error' => $e->getMessage(),
            ], 500); 
        }
    }
    /**
     * @OA\Post(
     *     path="/designation-store",
     *     summary="Create a new designation",
     *     description="Creates a new designation under a specific department. Requires permission to create designation.",
     *     tags={"Designations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass department ID and name of the designation",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"department_id", "name"},
     *             @OA\Property(property="department_id", type="integer", example=1, description="ID of the department"),
     *             @OA\Property(property="name", type="string", example="Manager", description="Name of the designation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Designation successfully created.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": true,
     *                 "message": "Designation successfully created.",
     *                 "data": {
     *                     "id": 1,
     *                     "department_id": 1,
     *                     "name": "Manager",
     *                     "created_by": 1,
     *                     "created_at": "2023-09-23T10:11:12.000000Z",
     *                     "updated_at": "2023-09-23T10:11:12.000000Z"
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": false,
     *                 "message": "Permission denied."
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": false,
     *                 "message": "Validation error.",
     *                 "errors": {
     *                     "department_id": {"The department id field is required."},
     *                     "name": {"The name field is required."}
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": false,
     *                 "message": "An error occurred. Please try again later.",
     *                 "error": "Exception message here"
     *             }
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            if (Helper::check_permissions('Create Designation')) {

                $validator = \Validator::make($request->all(), [
                    'department_id' => 'required',
                    'name' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Validation error.'),
                        'errors' => $validator->errors(),
                    ], 422); 
                }

                $designation = new Designation();
                $designation->department_id = $request->department_id;
                $designation->name = $request->name;
                $designation->created_by = \Auth::user()->creatorId();

                $designation->save();

                return response()->json([
                    'success' => true,
                    'message' => __('Designation successfully created.'),
                    'data' => $designation,
                ], 201);  
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Permission denied.'),
                ], 403);  
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred. Please try again later.'),
                'error' => $e->getMessage(),
            ], 500);  
        }
    }
    /**
     * @OA\Get(
     *     path="/designation/{designation}",
     *     summary="Retrieve designation data",
     *     description="Retrieve details of a specific designation and its related department. Requires permission to edit the designation.",
     *     tags={"Designations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="designation",
     *         in="path",
     *         required=true,
     *         description="ID of the designation",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Designation data retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": true,
     *                 "message": "Designation data retrieved successfully.",
     *                 "data": {
     *                     "designation": {
     *                         "id": 1,
     *                         "department_id": 1,
     *                         "name": "Manager",
     *                         "created_by": 1,
     *                         "created_at": "2023-09-23T10:11:12.000000Z",
     *                         "updated_at": "2023-09-23T10:11:12.000000Z"
     *                     },
     *                     "department": {
     *                         "id": 1,
     *                         "name": "Sales"
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": false,
     *                 "message": "Permission denied."
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Department not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": false,
     *                 "message": "Department not found."
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": false,
     *                 "message": "An error occurred. Please try again later.",
     *                 "error": "Exception message here"
     *             }
     *         )
     *     )
     * )
     */
    public function edit(Designation $designation)
    {
        try {
            if (Helper::check_permissions('Edit Designation')) {
                
                if ($designation->created_by == \Auth::user()->creatorId()) {
                    
                    $department = Department::where('id', $designation->department_id)->first();
                    
                    if ($department) {
                        $departmentInfo = [
                            'id' => $department->id,
                            'name' => $department->name,
                        ];

                        return response()->json([
                            'success' => true,
                            'message' => __('Designation data retrieved successfully.'),
                            'data' => [
                                'designation' => $designation,
                                'department' => $departmentInfo,
                            ]
                        ], 200);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => __('Department not found.')
                        ], 404); 
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => __('Permission denied.')
                    ], 403);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Permission denied.')
                ], 403); 
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred. Please try again later.'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * @OA\Put(
     *     path="/designation-update/{designation}",
     *     summary="Update a designation",
     *     description="Update the details of a specific designation. Requires permission to edit the designation.",
     *     tags={"Designations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="designation",
     *         in="path",
     *         required=true,
     *         description="ID of the designation to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for updating designation",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"department_id", "name"},
     *             @OA\Property(property="department_id", type="integer", example=1, description="ID of the department"),
     *             @OA\Property(property="name", type="string", example="Manager", description="Name of the designation, max 20 characters")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Designation successfully updated.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": true,
     *                 "message": "Designation successfully updated.",
     *                 "data": {
     *                     "id": 1,
     *                     "department_id": 1,
     *                     "name": "Manager",
     *                     "created_by": 1,
     *                     "created_at": "2023-09-23T10:11:12.000000Z",
     *                     "updated_at": "2023-09-23T10:11:12.000000Z"
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": false,
     *                 "message": "Permission denied."
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": false,
     *                 "message": "Validation error.",
     *                 "errors": {
     *                     "department_id": {"The department id field is required."},
     *                     "name": {"The name field must be less than 20 characters."}
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": false,
     *                 "message": "An error occurred. Please try again later.",
     *                 "error": "Exception message here"
     *             }
     *         )
     *     )
     * )
     */
    public function update(Request $request, Designation $designation)
    {
        try {
            if (Helper::check_permissions('Edit Designation')) {

                if ($designation->created_by == \Auth::user()->creatorId()) {

                    $validator = \Validator::make($request->all(), [
                        'department_id' => 'required',
                        'name' => 'required|max:20',
                    ]);

                    if ($validator->fails()) {
                        return response()->json([
                            'success' => false,
                            'message' => __('Validation error.'),
                            'errors' => $validator->errors(),
                        ], 422); 
                    }

                    $designation->name = $request->name;
                    $designation->department_id = $request->department_id;
                    $designation->save();

                    return response()->json([
                        'success' => true,
                        'message' => __('Designation successfully updated.'),
                        'data' => $designation,
                    ], 200);  
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => __('Permission denied.'),
                    ], 403);  
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Permission denied.'),
                ], 403);  
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred. Please try again later.'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * @OA\Delete(
     *     path="/designation-delete/{designation}",
     *     summary="Delete a designation",
     *     description="Delete a specific designation. Requires permission to delete the designation.",
     *     tags={"Designations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="designation",
     *         in="path",
     *         required=true,
     *         description="ID of the designation to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Designation successfully deleted.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": true,
     *                 "message": "Designation successfully deleted."
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": false,
     *                 "message": "Permission denied."
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "success": false,
     *                 "message": "An error occurred. Please try again later.",
     *                 "error": "Exception message here"
     *             }
     *         )
     *     )
     * )
     */
    public function destroy(Designation $designation)
    {
        try {
            if (Helper::check_permissions('Delete Designation')) {
                
                if ($designation->created_by == \Auth::user()->creatorId()) {
                    
                    $designation->delete();

                    return response()->json([
                        'success' => true,
                        'message' => __('Designation successfully deleted.'),
                    ], 200);  
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => __('Permission denied.'),
                    ], 403); 
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Permission denied.'),
                ], 403);  
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred. Please try again later.'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
