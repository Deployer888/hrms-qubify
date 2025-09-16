<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Api\BaseController;
use App\Models\Warning;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class WarningController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/hr/warning",
     *     tags={"HR Warning"},
     *     summary="Get all warnings",
     *     description="Returns a list of all warnings based on the authenticated user's permissions.",
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
     *                     @OA\Property(property="warning_to", type="integer", example=101),
     *                     @OA\Property(property="subject", type="string", example="Warning Subject"),
     *                     @OA\Property(property="warning_date", type="string", format="date", example="2023-10-01"),
     *                     @OA\Property(property="description", type="string", example="Warning description here.")
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Manage Warning')) {
                $warnings = $user->type === 'employee'
                    ? Warning::where('warning_by', Employee::where('user_id', $user->id)->first()->id)->get()
                    : Warning::with(['warning_to' => function($query) {
                        $query->select('id', 'name','user_id','empcode');
                    },'warning_by'=>function($query){
                        return $query->select('id','name','user_id','empcode');
                    }])->where('created_by', $user->creatorId())->get();

                return $this->successResponse(['warnings' => $warnings]);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/warning/create",
     *     tags={"HR Warning"},
     *     summary="Show form for creating a new warning",
     *     description="Returns form data for creating a new warning.",
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
     *                 @OA\Property(property="current_employee", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=102),
     *                         @OA\Property(property="name", type="string", example="Jane Smith")
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Warning')) {
                if ($user->type == 'employee') {
                    $current_employee = Employee::where('user_id', $user->id)->get()->pluck('name', 'id');
                    $employees = Employee::where('user_id', '!=', $user->id)->get()->pluck('name', 'id');
                } else {
                    $current_employee = Employee::where('user_id', $user->id)->get()->pluck('name', 'id');
                    $employees = Employee::where('created_by', $user->creatorId())->get()->pluck('name', 'id');
                }

                return $this->successResponse(['employees' => $employees, 'current_employee' => $current_employee]);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/hr/warning",
     *     tags={"HR Warning"},
     *     summary="Create a new warning",
     *     description="Creates a new warning based on the provided data.",
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
     *             @OA\Property(property="warning_to", type="integer", example=101),
     *             @OA\Property(property="warning_by", type="integer", example=101),
     *             @OA\Property(property="subject", type="string", example="Warning Subject"),
     *             @OA\Property(property="warning_date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="description", type="string", example="Warning description here.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Warning created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="warning_to", type="integer", example=101),
     *                 @OA\Property(property="warning_by", type="integer", example=101),
     *                 @OA\Property(property="subject", type="string", example="Warning Subject"),
     *                 @OA\Property(property="warning_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="description", type="string", example="Warning description here.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="warning_to", type="array", @OA\Items(type="string", example="The warning to field is required.")),
     *                 @OA\Property(property="warning_by", type="array", @OA\Items(type="string", example="The warning to field is required.")),
     *                 @OA\Property(property="subject", type="array", @OA\Items(type="string", example="The subject field is required.")),
     *                 @OA\Property(property="warning_date", type="array", @OA\Items(type="string", example="The warning date field is required."))
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

    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Warning')) {
                $validator = Validator::make($request->all(), [
                    'warning_to' => 'required',
                    'subject' => 'required',
                    'warning_date' => 'required',
                    'description' => 'required',
                ]);

                if ($user->type != 'employee') {
                    $validator->after(function ($validator) use ($request) {
                        if (!$request->has('warning_by')) {
                            $validator->errors()->add('warning_by', 'The warning by field is required.');
                        }
                    });
                }

                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }

                $warning = new Warning();
                if ($user->type == 'employee') {
                    $emp = Employee::where('user_id', $user->id)->first();
                    $warning->warning_by = $emp->id;
                } else {
                    $warning->warning_by = $request->warning_by;
                }

                $warning->warning_to = $request->warning_to;
                $warning->subject = $request->subject;
                $warning->warning_date = $request->warning_date;
                $warning->description = $request->description;
                $warning->created_by = $user->creatorId();
                $warning->save();

                // Send email notification if required
                // (Assuming Utility::settings() and Mail::to() are defined elsewhere)

                return $this->successResponse(['warning' => $warning], __('Warning successfully created.'), 201);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/warning/{id}",
     *     tags={"HR Warning"},
     *     summary="Get a specific warning",
     *     description="Returns details of a specific warning.",
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
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="warning_to", type="integer", example=101),
     *                 @OA\Property(property="warning_by", type="integer", example=101),
     *                 @OA\Property(property="subject", type="string", example="Warning Subject"),
     *                 @OA\Property(property="warning_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="description", type="string", example="Warning description here.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Warning not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Warning not found.")
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
    public function show($id): JsonResponse
    {
        try {
            $warning = Warning::with(['warning_to' => function($query) {
                $query->select('id', 'name','user_id','empcode');
            },'warning_by'=>function($query){
                return $query->select('id','name','user_id','empcode');
            }])->where('id',$id)->first();
            if (!$warning) {
                return $this->errorResponse(__('Warning not exists.'), 404);
            }
            return $this->successResponse(['warning' => $warning]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/warning/{id}/edit",
     *     tags={"HR Warning"},
     *     summary="Show form for editing a warning",
     *     description="Returns form data for editing a specific warning.",
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
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="warning_to", type="integer", example=101),
     *                 @OA\Property(property="warning_by", type="integer", example=101),
     *                 @OA\Property(property="subject", type="string", example="Warning Subject"),
     *                 @OA\Property(property="warning_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="description", type="string", example="Warning description here.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Warning not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Warning not found.")
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
    public function edit($id): JsonResponse
    {
        try {
            $user = Auth::user();
            $warning = Warning::with(['warning_to' => function($query) {
                $query->select('id', 'name','user_id','empcode');
            },'warning_by'=>function($query){
                return $query->select('id','name','user_id','empcode');
            }])->where('id',$id)->first();
            if (!$warning) {
                return $this->errorResponse(__('Warning not exists.'), 404);
            }
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Warning')) {
                if ($warning->created_by == $user->creatorId()) {
                    if ($user->type == 'employee') {
                        $current_employee = Employee::where('user_id', $user->id)->get()->pluck('name', 'id');
                        $employees = Employee::where('user_id', '!=', $user->id)->get()->pluck('name', 'id');
                    } else {
                        $current_employee = Employee::where('user_id', $user->id)->get()->pluck('name', 'id');
                        $employees = Employee::where('created_by', $user->creatorId())->get()->pluck('name', 'id');
                    }

                    return $this->successResponse(['warning' => $warning, 'employees' => $employees, 'current_employee' => $current_employee]);
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
     *     path="/api/hr/warning/{id}",
     *     tags={"HR Warning"},
     *     summary="Update a specific warning",
     *     description="Updates a specific warning based on the provided data.",
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
     *             @OA\Property(property="warning_to", type="integer", example=101),
     *             @OA\Property(property="warning_by", type="integer", example=101),
     *             @OA\Property(property="subject", type="string", example="Warning Subject"),
     *             @OA\Property(property="warning_date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="description", type="string", example="Warning description here.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Warning updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="warning_to", type="integer", example=101),
     *                 @OA\Property(property="warning_by", type="integer", example=101),
     *                 @OA\Property(property="subject", type="string", example="Updated Warning Subject"),
     *                 @OA\Property(property="warning_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="description", type="string", example="Updated warning description.")
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
     *         description="Warning not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Warning not found.")
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
            $warning = Warning::with(['warning_to' => function($query) {
                $query->select('id', 'name','user_id','empcode');
            },'warning_by'=>function($query){
                return $query->select('id','name','user_id','empcode');
            }])->where('id',$id)->first();
            if (!$warning) {
                return $this->errorResponse(__('Warning not exists.'), 404);
            }
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Warning')) {
                if ($warning->created_by == $user->creatorId()) {
                    $validator = Validator::make($request->all(), [
                        'warning_to' => 'required',
                        'subject' => 'required',
                        'warning_date' => 'required',
                    ]);

                    if ($user->type != 'employee') {
                        $validator->after(function ($validator) use ($request) {
                            if (!$request->has('warning_by')) {
                                $validator->errors()->add('warning_by', 'The warning by field is required.');
                            }
                        });
                    }

                    if ($validator->fails()) {
                        return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                    }

                    if ($user->type == 'employee') {
                        $emp = Employee::where('user_id', $user->id)->first();
                        $warning->warning_by = $emp->id;
                    } else {
                        $warning->warning_by = $request->warning_by;
                    }

                    $warning->warning_to = $request->warning_to;
                    $warning->subject = $request->subject;
                    $warning->warning_date = $request->warning_date;
                    $warning->description = $request->description;
                    $warning->save();

                    return $this->successResponse(['warning' => $warning], __('Warning successfully updated.'));
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
     *     path="/api/hr/warning/{id}",
     *     tags={"HR Warning"},
     *     summary="Delete a specific warning",
     *     description="Deletes a specific warning identified by its ID.",
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
     *         response=204,
     *         description="Warning deleted successfully"
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
     *         description="Warning not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Warning not found.")
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
    public function destroy(Warning $warning): JsonResponse
    {
        try {
            $user = Auth::user();
            $warning = Warning::where('id',$id)->first();
            if (!$warning) {
                return $this->errorResponse(__('Warning not exists.'), 404);
            }
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Delete Warning')) {
                if ($warning->created_by == $user->creatorId()) {
                    $warning->delete();
                    return $this->successResponse(null, __('Warning successfully deleted.'));
                }

                return $this->errorResponse(__('Permission denied.'), 403);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
