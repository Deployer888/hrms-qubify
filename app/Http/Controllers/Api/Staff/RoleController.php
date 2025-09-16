<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;


class RoleController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/staff/role/permission-list",
     *     tags={"Staff Sidebar"},
     *     summary="Get current user permissions",
     *     description="Retrieves the list of permissions for the current user based on their roles.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permissions retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\AdditionalProperties(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Manage Role")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function permissionList()
    {
        try {
            //code...
            $auth = Auth::user();
            if (!$auth->getAllPermissions()->where('guard_name','web')->pluck('name')->contains('Manage Role')) {
                return $this->errorResponse('Permission denied.', 403);
            }
            if($auth->type == 'super admin' || $auth->type == 'company')
            {
                $permissions = Permission::all()->pluck('name', 'id')->toArray();
            }
            else
            {
                $permissions = new Collection();
                foreach($auth->roles as $role)
                {
                    $permissions = $permissions->merge($role->permissions);
                }
                $permissions = $permissions->pluck('name', 'id')->toArray();
            }
            return $this->successResponse($permissions);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }

    }

    /**
     * @OA\Get(
     *     path="/api/staff/role",
     *     tags={"Staff Sidebar"},
     *     summary="Get list of roles",
     *     description="Retrieves a list of roles created by the authenticated user.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Roles retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Admin"),
     *                     @OA\Property(property="permissions", type="array",
     *                         @OA\Items(type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Manage Role")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function index(Request $request)
    {
        try {
            $auth = Auth::user();
            if (!$auth->getAllPermissions()->where('guard_name','web')->pluck('name')->contains('Manage Role')) {
                return $this->errorResponse('Permission denied.', 403);
            }
            $data = Role::with('permissions')->where('created_by', '=', \Auth::user()->creatorId())->where('guard_name','web')->get()->toarray();

            return $this->successResponse($data);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/staff/role",
     *     tags={"Staff Sidebar"},
     *     summary="Create a new role",
     *     description="Creates a new role with the specified name and permissions.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "permissions"},
     *             @OA\Property(property="name", type="string", example="Admin", description="The name of the role"),
     *             @OA\Property(property="permissions", type="array", @OA\Items(type="integer"), description="Array of permission IDs")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Role created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Admin"),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or role already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The Role has Already Been Taken."),
     *             @OA\Property(property="errors", type="object", additionalProperties={"type":"string"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function store(Request $request)
    {
        try {
            $auth = Auth::user();
            if (!$auth->getAllPermissions()->pluck('name')->contains('Create Role')) {
                return $this->errorResponse('Permission denied.', 403);
            }
            $role = Role::where('name', $request->name)->first();
            if ($role) {
                return $this->errorResponse('The Role has Already Been Taken.', 400);
            }
            // Define the validation rules
            $rules = [
                'name' => 'required|max:100|unique:roles,name,NULL,id,created_by,' . Auth::user()->creatorId(),
                'permissions' => 'required',
            ];

            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);
            // Check if validation fails
            if ($validator->fails()) {
                return $this->errorResponse('Validation error', 400, $validator->errors()->toArray());
            }

            $role = new Role();
            $role->name = $request['name'];
            $role->created_by = Auth::user()->creatorId();
            $role->save();
            $missingPermissions = [];
            foreach ($request['permissions'] as $permission) {
                $p = Permission::find($permission); // Use find instead of findOrFail

                if ($p) {
                    $role->givePermissionTo($p);
                } else {
                    $missingPermissions[] = $permission;
                }
            }
            if (!empty($missingPermissions)) {
                return $this->successResponse($missingPermissions,'Some permissions were not found: ',201);
            }
            return $this->successResponse($role);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/staff/role/{id}",
     *     tags={"Staff Sidebar"},
     *     summary="Update an existing role",
     *     description="Updates the details of a specific role.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the role to update"
     *     ),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "permissions"},
     *             @OA\Property(property="name", type="string", example="Admin", description="The name of the role"),
     *             @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"create", "edit", "delete"}, description="List of permissions assigned to the role")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Role updated successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1, description="The ID of the updated role"),
     *                 @OA\Property(property="name", type="string", example="Admin", description="The name of the role"),
     *                 @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"create", "edit", "delete"}, description="Updated list of permissions")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or role already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Role already exists."),
     *             @OA\Property(property="errors", type="object", additionalProperties={"type":"string"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function update(Request $request, Role $role): JsonResponse
    {
        try {
            if (Auth::user()->can('Edit Role')) {
                if ($role->name == 'employee') {
                    $this->validate($request, [
                        'permissions' => 'required',
                    ]);
                } else {
                    $validator = Validator::make($request->all(), [
                        'name' => [
                            'required',
                            'max:100',
                            Rule::unique('roles', 'name')
                                ->ignore($role['id'], 'id')
                                ->where(function ($query) {
                                    $query->where('created_by', Auth::user()->creatorId());
                                }),
                        ],
                        'permissions' => 'required',
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['error' => $validator->errors()], 400);
                    }
                }

                $input = $request->except(['permissions']);
                $permissions = $request['permissions'];
                $role->fill($input)->save();

                $role->permissions()->detach();

                foreach ($permissions as $permission) {
                    $p = Permission::findOrFail($permission);
                    $role->givePermissionTo($p);
                }

                return response()->json(['success' => 'Role successfully updated.'], 200);
            } else {
                return response()->json(['error' => 'Permission denied.'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/staff/delete/role/{id}",
     *     tags={"Staff Sidebar"},
     *     summary="Delete a role",
     *     description="Deletes a specific role by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the role to delete"
     *     ),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="string", example="Role successfully deleted.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="An error occurred: Error message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function destroy(Role $role): JsonResponse
    {
        try {
            if (Auth::user()->can('Delete Role')) {
                $role->delete();
                return response()->json(['success' => 'Role successfully deleted.'], 200);
            } else {
                return response()->json(['error' => 'Permission denied.'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
