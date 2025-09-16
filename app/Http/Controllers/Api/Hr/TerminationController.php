<?php

namespace App\Http\Controllers\Api\Hr;

use App\Models\Termination;
use App\Models\Employee;
use App\Models\TerminationType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Api\BaseController;


class TerminationController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/hr/termination",
     *     tags={"HR Termination"},
     *     summary="Get all terminations",
     *     description="Returns a list of all terminations based on the authenticated user's permissions.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="employee_id", type="integer", example=101),
     *                     @OA\Property(property="termination_type", type="integer", example=1),
     *                     @OA\Property(property="termination_date", type="string", format="date", example="2023-10-01"),
     *                     @OA\Property(property="description", type="string", example="Termination description here.")
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

    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Manage Termination')) 
            {
                $terminations = $user->type === 'employee'
                    ? Termination::with(['termination_type','employee_detail'])->where('created_by', $user->creatorId())
                        ->where('employee_id', Employee::where('user_id', $user->id)->first()->id)
                        ->get()
                    : Termination::with(['termination_type','employee_detail'])->where('created_by', $user->creatorId())->get();

                return $this->successResponse(['terminations' => $terminations]);
            }
            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/termination/create",
     *     tags={"HR Termination"},
     *     summary="Show form for creating a new termination",
     *     description="Returns form data for creating a new termination.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="employees", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=101),
     *                         @OA\Property(property="name", type="string", example="John Doe")
     *                     )
     *                 ),
     *                 @OA\Property(property="termination_types", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Voluntary")
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
    public function create(): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Termination')) {
                $employees = Employee::where('created_by', $user->creatorId())->get()->pluck('name', 'id');
                $terminationTypes = TerminationType::where('created_by', $user->creatorId())->get()->pluck('name', 'id');

                return $this->successResponse(['employees' => $employees, 'termination_types' => $terminationTypes]);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/hr/termination",
     *     tags={"HR Termination"},
     *     summary="Create a new termination",
     *     description="Creates a new termination based on the provided data.",
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
     *             @OA\Property(property="employee_id", type="integer", example=101),
     *             @OA\Property(property="termination_type", type="integer", example=1),
     *             @OA\Property(property="termination_date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="description", type="string", example="Termination description here.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Termination created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="employee_id", type="integer", example=101),
     *                 @OA\Property(property="termination_type", type="integer", example=1),
     *                 @OA\Property(property="termination_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="description", type="string", example="Termination description here.")
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
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
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

    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Termination')) {
                $validator = Validator::make($request->all(), [
                    'employee_id' => 'required',
                    'termination_type' => 'required|exists:termination_types,id',
                    'termination_date' => 'required',
                    'description' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }

                $termination = new Termination();
                $termination->employee_id = $request->employee_id;
                $termination->termination_type = $request->termination_type;
                $termination->termination_date = $request->termination_date;
                $termination->description = $request->description;
                $termination->created_by = $user->creatorId();
                $termination->save();

                // Send email notification if required
                // (Assuming Utility::settings() and Mail::to() are defined elsewhere)

                return $this->successResponse(['termination' => $termination], __('Termination successfully created.'), 201);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/termination/{id}",
     *     tags={"HR Termination"},
     *     summary="Get a specific termination",
     *     description="Returns details of a specific termination.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @ OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="employee_id", type="integer", example=101),
     *                 @OA\Property(property="termination_type", type="integer", example=1),
     *                 @OA\Property(property="termination_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="description", type="string", example="Termination description here.")
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
     *         response=404,
     *         description="Termination not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Termination not found.")
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
    public function show($id): JsonResponse
    {
        try {
            $termination = Termination::with(['termination_type','employee_detail'])->where('id',$id)->first();
            if (!$termination) {
                return $this->errorResponse(__('Termination not exists.'), 404);
            }
            return $this->successResponse(['termination' => $termination]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/termination/{id}/edit",
     *     tags={"HR Termination"},
     *     summary="Show form for editing a termination",
     *     description="Returns form data for editing a specific termination.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="termination", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="employee_id", type="integer", example=101),
     *                     @OA\Property(property="termination_type", type="integer", example=1),
     *                     @OA\Property(property="termination_date", type="string", format="date", example="2023-10-01"),
     *                     @OA\Property(property="description", type="string", example="Termination description here.")
     *                 ),
     *                 @OA\Property(property="employees", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=101),
     *                         @OA\Property(property="name", type="string", example="John Doe")
     *                     )
     *                 ),
     *                 @OA\Property(property="termination_types", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Voluntary")
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
     *         response=404,
     *         description="Termination not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Termination not found.")
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

    public function edit($id): JsonResponse
    {
        try {
            $user = Auth::user();
            $termination = Termination::with(['termination_type','employee_detail'])->where('id',$id)->first();
            if (!$termination) {
                return $this->errorResponse(__('Termination not exists.'), 404);
            }
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Termination')) {
                if ($termination->created_by == $user->creatorId()) {
                    $employees = Employee::where('created_by', $user->creatorId())->get()->pluck('name', 'id');
                    $terminationTypes = TerminationType::where('created_by', $user->creatorId())->get()->pluck('name', 'id');

                    return $this->successResponse(['termination' => $termination, 'employees' => $employees, 'termination_types' => $terminationTypes]);
                }

                return $this->errorResponse(__('Permission denied.'), 403);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Put(
     *     path="/api/hr/termination/{id}",
     *     tags={"HR Termination"},
     *     summary="Update a specific termination",
     *     description="Updates a specific termination based on the provided data.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *             @OA\Property(property="employee_id", type="integer", example=101),
     *             @OA\Property(property="termination_type", type="integer", example=1),
     *             @OA\Property(property="termination_date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="description", type="string", example="Termination description here.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Termination updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="employee_id", type="integer", example=101),
     *                 @OA\Property(property="termination_type", type="integer", example=1),
     *                 @OA\Property(property="termination_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="description", type="string", example="Updated termination description here.")
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
     *         response=404,
     *         description="Termination not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Termination not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
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
    public function update(Request $request,$id): JsonResponse
    {
        try {
            $user = Auth::user();
            $termination = Termination::with(['termination_type','employee_detail'])->where('id',$id)->first();
            if (!$termination) {
                return $this->errorResponse(__('Termination not exists.'), 404);
            }
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Termination')) 
            {
                if ($termination->created_by == $user->creatorId()) {
                    $validator = Validator::make($request->all(), [
                        'employee_id' => 'required',
                        'termination_type' => 'required|string',
                        'termination_date' => 'required',
                        'description' => 'required'
                    ]);

                    if ($validator->fails()) {
                        return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                    }

                    $termination->employee_id = $request->employee_id;
                    $termination->termination_type = $request->termination_type;
                    $termination->termination_date = $request->termination_date;
                    $termination->description = $request->description;
                    $termination->save();

                    return $this->successResponse(['termination' => $termination], __('Termination successfully updated.'));
                }

                return $this->errorResponse(__('Permission denied.'), 403);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/hr/termination/{id}",
     *     tags={"HR Termination"},
     *     summary="Delete a specific termination",
     *     description="Deletes a specific termination.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *         description="Termination deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Termination successfully deleted.")
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
     *         response=404,
     *         description="Termination not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Termination not found.")
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

    public function destroy($id): JsonResponse
    {
        try {
            $user = Auth::user();
            $termination = Termination::where('id',$id)->first();
            if (!$termination) {
                return $this->errorResponse(__('Termination not exists.'), 404);
            }
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Delete Termination')) {
                if ($termination->created_by == $user->creatorId()) {
                    $termination->delete();
                    return $this->successResponse(null, __('Termination successfully deleted.'));
                }

                return $this->errorResponse(__('Permission denied.'), 403);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
