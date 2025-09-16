<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Department;
use App\Helpers\Helper;

class DepartmentController extends BaseController
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
            if (Helper::check_permissions('Manage Department')) {
                $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->get();
    
                // Return success response with departments data and a message
                return $this->successResponse(['departments' => $departments], __('Department list.'));
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error fetching departments: ' . $e->getMessage());
    
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
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
            if (Helper::check_permissions('Create Department')) {
                $validator = \Validator::make($request->all(), [
                    'branch_id' => 'required|exists:branches,id',
                    'name' => 'required|max:20',
                ]);
    
                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }
    
                $department = new Department();
                $department->branch_id  = $request->branch_id;
                $department->name       = $request->name;
                $department->created_by = \Auth::user()->creatorId();
                $department->save();
    
                return $this->successResponse(['department'=>$department], __('Department successfully created.'));
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error creating department: ' . $e->getMessage());
    
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
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
    public function edit($id)
    {
        try {
            $department = Department::where('id',$id)->first();
            if (!$department) {
                return $this->errorResponse(__('Department not exists.'), 404);
            }
            if (Helper::check_permissions('Edit Department')) {
                if ($department->created_by == \Auth::user()->creatorId()) {
                    $branches = Branch::where('created_by', \Auth::user()->creatorId())
                        ->get(['id', 'name']); 
    
                    return $this->successResponse([
                        'department' => $department,
                        'branches'   => $branches
                    ]);
                } else {
                    return $this->errorResponse(__('Permission denied.'), 403); 
                }
            } else {
                return $this->errorResponse(__('Permission denied.'), 403); 
            }
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error fetching department for edit: ' . $e->getMessage());
    
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500); 
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
    public function update(Request $request, $id)
    {
        try {
            $department = Department::find($id);
            if (!$department) {
                return $this->errorResponse(__('Department not exists.'), 404);
            }
    
            if (Helper::check_permissions('Edit Department')) {
                if ($department->created_by == \Auth::user()->creatorId()) {
                    
                    $validator = \Validator::make($request->all(), [
                        'branch_id' => 'required',
                        'name' => 'required|max:20',
                    ]);
    
                    if ($validator->fails()) {
                        return $this->errorResponse($validator->errors()->first(), 422);
                    }
    
                    $department->branch_id = $request->branch_id;
                    $department->name = $request->name;
                    $department->save();
    
                    return $this->successResponse(['department'=>$department], __('Department successfully updated.'));
                } else {
                    return $this->errorResponse(__('Permission denied.'), 403); 
                }
            } else {
                return $this->errorResponse(__('Permission denied.'), 403); 
            }
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error updating department: ' . $e->getMessage());
    
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
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
    public function destroy($id)
    {
        try {
            $department = Department::find($id);
            if (!$department) {
                return $this->errorResponse(__('Department not exists.'), 404);
            }
    
            if (Helper::check_permissions('Delete Department')) {
                if ($department->created_by == \Auth::user()->creatorId()) {
                    $department->delete();
    
                    return $this->successResponse(null, __('Department successfully deleted.'));
                } else {
                    return $this->errorResponse(__('Permission denied.'), 403);
                }
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error deleting department: ' . $e->getMessage());
    
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }

}
