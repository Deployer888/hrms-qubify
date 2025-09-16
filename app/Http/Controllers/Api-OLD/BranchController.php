<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Branch, Department};


class BranchController extends Controller
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
        $user = \Auth::user();

        if ($user->getAllPermissions()->pluck('name')->contains('Manage Branch')) {
            $branches = Branch::where('created_by', $user->creatorId())->get();

            return response()->json([
                'success' => true,
                'data' => $branches
            ], 200);
        } 
        else 
        {
            return response()->json([
                'success' => false,
                'error' => 'Permission denied.'
            ], 403);
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
        $user = \Auth::user();

        if ($user->getAllPermissions()->pluck('name')->contains('Create Branch')) {
            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()->first(), // Returns the first validation error message
                ], 422);
            }

            $branch = new Branch();
            $branch->name = $request->name;
            $branch->created_by = $user->creatorId();
            $branch->save();

            return response()->json([
                'success' => true,
                'message' => __('Branch successfully created.'),
                'data' => $branch,
            ], 201); 
        } 
        else 
        {
            return response()->json([
                'success' => false,
                'error' => __('Permission denied.'),
            ], 403); 
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
    public function edit(Branch $branch)
    {
        $user = \Auth::user();
        if ($user->getAllPermissions()->pluck('name')->contains('Edit Branch')) {
            if ($branch->created_by == $user->creatorId()) {
                return response()->json([
                    'success' => true,
                    'data' => $branch
                ], 200); 
            } else {
                return response()->json([
                    'success' => false,
                    'error' => __('Permission denied.'),
                ], 403);
            }
        } else {
            return response()->json([
                'success' => false,
                'error' => __('Permission denied.'),
            ], 403); 
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
    public function update(Request $request, Branch $branch)
    {
        $user = \Auth::user();

        if ($user->getAllPermissions()->pluck('name')->contains('Edit Branch')) {
            if ($branch->created_by == $user->creatorId()) {
                $validator = \Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()->first(), 
                    ], 422);
                }

                $branch->name = $request->name;
                $branch->save();

                return response()->json([
                    'success' => true,
                    'message' => __('Branch successfully updated.'),
                    'data' => $branch,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => __('Permission denied.'),
                ], 403); 
            }
        } else {
            return response()->json([
                'success' => false,
                'error' => __('Permission denied.'),
            ], 403);
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
    public function destroy(Branch $branch)
    {
        $user = \Auth::user();

        if ($user->getAllPermissions()->pluck('name')->contains('Delete Branch')) {
            if ($branch->created_by == $user->creatorId()) {
                $branch->delete();

                return response()->json([
                    'success' => true,
                    'message' => __('Branch successfully deleted.'),
                ], 200); 
            } else {
                return response()->json([
                    'success' => false,
                    'error' => __('Permission denied.'),
                ], 403); 
            }
        } else {
            return response()->json([
                'success' => false,
                'error' => __('Permission denied.'),
            ], 403); 
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
