<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\{Branch, Department};
use Log;
use App\Http\Controllers\Api\BaseController;


class BranchController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/branches",
     *     summary="Get List of Branches",
     *     description="Retrieves a list of branches created by the authenticated user. Requires 'Manage Branch' permission.",
     *     operationId="getBranches",
     *     tags={"Branch"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with list of branches",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", description="Branch ID"),
     *                     @OA\Property(property="name", type="string", description="Branch name"),
     *                     @OA\Property(property="created_by", type="integer", description="User ID of the branch creator"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", description="Branch creation date"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", description="Branch last update date")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function index()
    {
        try {
            $user = \Auth::user();

            if ($user->getAllPermissions()->pluck('name')->contains('Manage Branch')) {
                $branches = Branch::where('created_by', $user->creatorId())->get();

                return $this->successResponse(['branches'=>$branches],'Branch list.');
            } else {
                return $this->errorResponse('Permission denied.', 403);
            }
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error fetching branches: ' . $e->getMessage());

            return $this->errorResponse('An error occurred while fetching branches.', 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/branch-store",
     *     summary="Create a new Branch",
     *     description="Allows users with 'Create Branch' permission to create a new branch.",
     *     operationId="createBranch",
     *     tags={"Branch"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Branch creation data",
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="New Branch", description="The name of the branch"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Branch successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Branch successfully created."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="Branch ID"),
     *                 @OA\Property(property="name", type="string", description="Branch name"),
     *                 @OA\Property(property="created_by", type="integer", description="User ID of the creator"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description="Branch creation date"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", description="Branch last update date")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="string", example="The name field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function store(Request $request)
    {
        try {
            $user = \Auth::user();
    
            if ($user->getAllPermissions()->pluck('name')->contains('Create Branch')) {
                $validator = \Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                ]);
    
                if ($validator->fails()) 
                {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }
    
                $branch = new Branch();
                $branch->name = $request->name;
                $branch->created_by = $user->creatorId();
                $branch->save();
    
                return $this->successResponse(['branch'=>$branch], 201);
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error creating branch: ' . $e->getMessage());
    
            return $this->errorResponse(__('An error occurred while creating the branch.'), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/branch/{branch}",
     *     summary="Get Branch Details",
     *     description="Fetches the details of a specific branch if the authenticated user has 'Edit Branch' permission and is the creator of the branch.",
     *     operationId="getBranchDetails",
     *     tags={"Branch"},
     *     @OA\Parameter(
     *         name="branch",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the branch"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Branch details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="Branch ID"),
     *                 @OA\Property(property="name", type="string", description="Branch name"),
     *                 @OA\Property(property="created_by", type="integer", description="User ID of the branch creator"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description="Branch creation date"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", description="Branch last update date")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Branch not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Branch not found.")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function edit($id)
    {
        try {
            $branch = Branch::where('id',$id)->first();
            if (!$branch) {
                return $this->errorResponse(__('Branch not exists.'), 404);
            }
            $user = \Auth::user();
    
            if ($user->getAllPermissions()->pluck('name')->contains('Edit Branch')) {
                if ($branch->created_by == $user->creatorId()) {
                    return $this->successResponse(['branch'=>$branch]);
                } else {
                    return $this->errorResponse(__('Permission denied.'), 403);
                }
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error fetching branch for edit: ' . $e->getMessage());
    
            return $this->errorResponse(__('An error occurred while fetching the branch.'), 500);
        }
    }
    /**
     * @OA\Put(
     *     path="/api/branch-update/{branch}",
     *     summary="Update Branch Details",
     *     description="Updates the details of a specific branch if the authenticated user has 'Edit Branch' permission and is the creator of the branch.",
     *     operationId="updateBranch",
     *     tags={"Branch"},
     *     @OA\Parameter(
     *         name="branch",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the branch to be updated"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Updated Branch Name", description="The new name of the branch")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Branch successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Branch successfully updated."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="Branch ID"),
     *                 @OA\Property(property="name", type="string", description="Updated branch name"),
     *                 @OA\Property(property="created_by", type="integer", description="User ID of the branch creator"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description="Branch creation date"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", description="Branch last update date")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="string", example="The name field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function update(Request $request,$id)
    {
        try {
            $user = \Auth::user();
            $branch = Branch::where('id',$id)->first();
            if (!$branch) {
                return $this->errorResponse(__('Branch not exists.'), 404);
            }
            if ($user->getAllPermissions()->pluck('name')->contains('Edit Branch')) {
                if ($branch->created_by == $user->creatorId()) {
                    $validator = \Validator::make($request->all(), [
                        'name' => 'required|string|max:255',
                    ]);

                    if ($validator->fails()) {
                        return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                    }

                    $branch->name = $request->name;
                    $branch->save();

                    return $this->successResponse(['branch'=>$branch], __('Branch successfully updated.'));
                } else {
                    return $this->errorResponse(__('Permission denied.'), 403);
                }
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error updating branch: ' . $e->getMessage());

            return $this->errorResponse(__('An error occurred while updating the branch.'), 500);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/branch-delete/{branch}",
     *     summary="Delete a Branch",
     *     description="Deletes a specific branch if the authenticated user has 'Delete Branch' permission and is the creator of the branch.",
     *     operationId="deleteBranch",
     *     tags={"Branch"},
     *     @OA\Parameter(
     *         name="branch",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the branch to be deleted"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Branch successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Branch successfully deleted.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Branch not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Branch not found.")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function destroy($id)
    {
        try {
            $user = \Auth::user();
            $branch = Branch::where('id',$id)->first();
            if (!$branch) {
                return $this->errorResponse(__('Branch not exists.'), 404);
            }
            if ($user->getAllPermissions()->pluck('name')->contains('Delete Branch')) {
                if ($branch->created_by == $user->creatorId()) {
                    $branch->delete();

                    return $this->successResponse(null,__('Branch successfully deleted.'));
                } else {
                    return $this->errorResponse(__('Permission denied.'), 403);
                }
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            // Log the exception if needed
            \Log::error('Error deleting branch: ' . $e->getMessage());

            return $this->errorResponse(__('An error occurred while deleting the branch.'), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/get-department",
     *     summary="Get Department List",
     *     description="Retrieves a list of departments for a given branch. If 'branch_id' is 0, all departments are returned.",
     *     operationId="getDepartment",
     *     tags={"Branch"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"branch_id"},
     *             @OA\Property(property="branch_id", type="integer", example=1, description="The ID of the branch. Use 0 to retrieve all departments.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with the list of departments",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 additionalProperties={
     *                     @OA\Property(property="id", type="integer", description="Department ID"),
     *                     @OA\Property(property="name", type="string", description="Department name")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="string", example="The branch_id field is required.")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

}
