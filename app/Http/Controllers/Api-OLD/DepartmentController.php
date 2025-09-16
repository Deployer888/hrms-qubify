<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Department;
use App\Helpers\Helper;

class DepartmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/departments",
     *     summary="Retrieve a list of departments",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     description="Returns a list of departments created by the authenticated user.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of departments",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="branch_id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="Human Resources"),
     *                     @OA\Property(property="created_by", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T00:00:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No departments found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No departments found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred: [Error Message]")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            if(Helper::check_permissions('Manage Department')) {
                $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->get();
                
                if($departments->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('No departments found.')
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'data' => $departments
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Permission denied.')
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred: ') . $e->getMessage()
            ], 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/department-store",
     *     summary="Create a new department",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     description="Creates a new department with the provided branch ID and name.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Department details",
     *         @OA\JsonContent(
     *             required={"branch_id", "name"},
     *             @OA\Property(
     *                 property="branch_id",
     *                 type="integer",
     *                 description="ID of the branch",
     *                 example=2
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Name of the department",
     *                 maxLength=20,
     *                 example="Human Resources"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Department successfully created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Department successfully created."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="branch_id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="Human Resources"),
     *                 @OA\Property(property="created_by", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The name field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred: [Error Message]")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            if(Helper::check_permissions('Create Department')) {
                $validator = \Validator::make($request->all(), [
                    'branch_id' => 'required',
                    'name' => 'required|max:20',
                ]);

                if($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validator->errors()->first()
                    ], 422);
                }

                $department = new Department();
                $department->branch_id  = $request->branch_id;
                $department->name       = $request->name;
                $department->created_by = \Auth::user()->creatorId();
                $department->save();

                return response()->json([
                    'success' => true,
                    'message' => __('Department successfully created.'),
                    'data'    => $department
                ], 201); 
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Permission denied.')
                ], 403); 
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred: ') . $e->getMessage()
            ], 500); 
        }
    }
    /**
     * @OA\Get(
     *     path="/api/department/{department}",
     *     summary="Retrieve a specific department and its associated branches",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     description="Returns the details of a specific department along with the branches created by the authenticated user.",
     *     @OA\Parameter(
     *         name="department",
     *         in="path",
     *         description="ID of the department to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of department and branches",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="department",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="branch_id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="Human Resources"),
     *                     @OA\Property(property="created_by", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T00:00:00Z")
     *                 ),
     *                 @OA\Property(
     *                     property="branches",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="Main Branch")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Department not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Department not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred: [Error Message]")
     *         )
     *     )
     * )
     */
    public function edit(Department $department)
    {
        try {
            if(Helper::check_permissions('Edit Department')) {
                if($department->created_by == \Auth::user()->creatorId()) {
                    $branches = Branch::where('created_by', \Auth::user()->creatorId())
                                    ->get(['id', 'name']); 

                    return response()->json([
                        'success'   => true,
                        'data'      => [
                            'department' => $department,
                            'branches'  => $branches
                        ]
                    ], 200);
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
                'message' => __('An error occurred: ') . $e->getMessage()
            ], 500); 
        }
    }
    /**
     * @OA\Put(
     *     path="/api/department-update/{department}",
     *     summary="Update an existing department",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     description="Updates the details of a specific department.",
     *     @OA\Parameter(
     *         name="department",
     *         in="path",
     *         description="ID of the department to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Department details to be updated",
     *         @OA\JsonContent(
     *             required={"branch_id", "name"},
     *             @OA\Property(
     *                 property="branch_id",
     *                 type="integer",
     *                 description="ID of the branch",
     *                 example=2
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Name of the department",
     *                 maxLength=20,
     *                 example="Human Resources"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Department successfully updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Department successfully updated."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="branch_id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="Human Resources"),
     *                 @OA\Property(property="created_by", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The name field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred: [Error Message]")
     *         )
     *     )
     * )
     */
    public function update(Request $request, Department $department)
    {
        try {
            if (Helper::check_permissions('Edit Department')) {
                if ($department->created_by == \Auth::user()->creatorId()) {
                    
                    $validator = \Validator::make($request->all(), [
                        'branch_id' => 'required',
                        'name' => 'required|max:20',
                    ]);

                    if ($validator->fails()) {
                        return response()->json([
                            'success' => false,
                            'message' => $validator->errors()->first()
                        ], 422);
                    }

                    $department->branch_id = $request->branch_id;
                    $department->name = $request->name;
                    $department->save();

                    return response()->json([
                        'success' => true,
                        'message' => __('Department successfully updated.'),
                        'data' => $department
                    ], 200); 
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
                'message' => __('An error occurred: ') . $e->getMessage()
            ], 500);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/department-delete/{department}",
     *     summary="Delete a specific department",
     *     tags={"Departments"},
     *     security={{"bearerAuth":{}}},
     *     description="Deletes a specific department identified by its ID.",
     *     @OA\Parameter(
     *         name="department",
     *         in="path",
     *         description="ID of the department to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Department successfully deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Department successfully deleted.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Department not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Department not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred: [Error Message]")
     *         )
     *     )
     * )
     */
    public function destroy(Department $department)
    {
        try {
            if (Helper::check_permissions('Delete Department')) {
                if ($department->created_by == \Auth::user()->creatorId()) {
                    $department->delete();

                    return response()->json([
                        'success' => true,
                        'message' => __('Department successfully deleted.')
                    ], 200); 
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
                'message' => __('An error occurred: ') . $e->getMessage()
            ], 500);
        }
    }

}
