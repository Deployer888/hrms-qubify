<?php

namespace App\Http\Controllers\Api\Hr;

use App\Models\Resignation;
use App\Models\User;
use App\Models\Employee;
use App\Mail\ResignationSend;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Api\BaseController;


class ResignationController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/hr/rasignation",
     *     tags={"HR Resignation"},
     *     summary="Get all resignations",
     *     description="Returns a list of all resignations based on the authenticated user's permissions.",
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
     *                     @OA\Property(property="notice_date", type="string", format="date", example="2023-10-01"),
     *                     @OA\Property(property="resignation_date", type="string", format="date", example="2023-10-15"),
     *                     @OA\Property(property="description", type="string", example="Resignation due to personal reasons")
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Manage Resignation'))
            {
                if (Auth::user()->type == 'employee') {
                    $emp = Employee::where('user_id', Auth::user()->id)->first();
                    $resignations = Resignation::where('created_by', Auth::user()->creatorId())
                        ->where('employee_id', $emp->id)
                        ->get();
                } else {
                    $resignations = Resignation::where('created_by', Auth::user()->creatorId())->get();
                }

                return $this->successResponse(['resignations' => $resignations]);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/hr/rasignation/create",
     *     tags={"HR Resignation"},
     *     summary="Show the form for creating a new resignation",
     *     description="Returns the necessary data to create a new resignation.",
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Resignation'))
            {
                $employees = Employee::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
                return $this->successResponse(['employees' => $employees]);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/hr/rasignation",
     *     tags={"HR Resignation"},
     *     summary="Store a new resignation",
     *     description="Creates a new resignation record.",
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
     *             @OA\Property(property="notice_date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="resignation_date", type="string", format="date", example="2023-10-15"),
     *             @OA\Property(property="description", type="string", example="Resignation due to personal reasons")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Resignation created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="employee_id", type="integer", example=101),
     *                 @OA\Property(property="notice_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="resignation_date", type="string", format="date", example="2023-10-15"),
     *                 @OA\Property(property="description", type="string", example="Resignation due to personal reasons")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Resignation'))
            {
                $validator = Validator::make($request->all(), [
                    'notice_date' => 'required',
                    'resignation_date' => 'required',
                    'description' => 'required',
                    'employee_id' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }

                $resignation = new Resignation();
                $user = Auth::user();
                if ($user->type == 'employee') {
                    $employee = Employee::where('user_id', $user->id)->first();
                    $resignation->employee_id = $employee->id;
                } else {
                    $resignation->employee_id = $request->employee_id;
                }
                $resignation->notice_date = $request->notice_date;
                $resignation->resignation_date = $request->resignation_date;
                $resignation->description = $request->description;
                $resignation->created_by = $user->creatorId();
                $resignation->save();

                $setings = Utility::settings();
                if($setings['employee_resignation'] == 1)
                {
                    $employee           = Employee::find($resignation->employee_id);
                    $resignation->name  = $employee->name;
                    $resignation->email = $employee->email;
                    try
                    {
                        Mail::to($resignation->email)->send(new ResignationSend($resignation));
                    }
                    catch(\Exception $e)
                    {
                        $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                    }


                    $user           = User::find($employee->created_by);
                    $resignation->name  = $user->name;
                    $resignation->email = $user->email;
                    try
                    {
                        Mail::to($resignation->email)->send(new ResignationSend($resignation));
                    }
                    catch(\Exception $e)
                    {
                        $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                    }
                    $message = 'Resignation successfully created.' . (isset($smtp_error) ? $smtp_error : '');
                    return $this->successResponse(['resignation' => $resignation], $message, 201);

                }

                return $this->successResponse(__('Resignation successfully created.'), 201);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/rasignation/{resignation}",
     *     tags={"HR Resignation"},
     *     summary="Get a specific resignation",
     *     description="Returns details of a specific resignation by ID.",
     *     @OA\Parameter(
     *         name="resignation",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the resignation"
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
     *                 @OA\Property(property="employee_id", type="integer", example=101),
     *                 @OA\Property(property="notice_date", type *                 = "string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="resignation_date", type="string", format="date", example="2023-10-15"),
     *                 @OA\Property(property="description", type="string", example="Resignation due to personal reasons")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resignation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Resignation not found.")
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
            $resignation = Resignation::where('id',$id)->first();
            if (!$resignation) {
                return $this->errorResponse(__('Resignation not exists.'), 404);
            }
            return $this->successResponse(['resignation' => $resignation]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/hr/rasignation/{resignation}/edit",
     *     tags={"HR Resignation"},
     *     summary="Show the form for editing an existing resignation",
     *     description="Returns the necessary data to edit an existing resignation.",
     *     @OA\Parameter(
     *         name="resignation",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the resignation to edit"
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
     *                 @OA\Property(property="resignation", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="employee_id", type="integer", example=101),
     *                     @OA\Property(property="notice_date", type="string", format="date", example="2023-10-01"),
     *                     @OA\Property(property="resignation_date", type="string", format="date", example="2023-10-15"),
     *                     @OA\Property(property="description", type="string", example="Resignation due to personal reasons")
     *                 ),
     *                 @OA\Property(property="employees", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=101),
     *                         @OA\Property(property="name", type="string", example="John Doe")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resignation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Resignation not found.")
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
            $resignation = Resignation::where('id',$id)->first();
            if (!$resignation) {
                return $this->errorResponse(__('Resignation not exists.'), 404);
            }
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Resignation'))
            {
                if ($resignation->created_by == Auth::user()->creatorId()) {
                    $employees = Employee::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
                    return $this->successResponse(['resignation' => $resignation, 'employees' => $employees]);
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
     *     path="/api/hr/rasignation/{resignation}",
     *     tags={"HR Resignation"},
     *     summary="Update a specific resignation",
     *     description="Updates the details of a specific resignation by ID.",
     *     @OA\Parameter(
     *         name="resignation",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the resignation"
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
     *             @OA\Property(property="notice_date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="resignation_date", type="string", format="date", example="2023-10-15"),
     *             @OA\Property(property="description", type="string", example="Updated resignation reason")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resignation updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="employee_id", type="integer", example=101),
     *                 @OA\Property(property="notice_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="resignation_date", type="string", format="date", example="2023-10-15"),
     *                 @OA\Property(property="description", type="string", example="Updated resignation reason")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resignation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Resignation not found.")
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
    public function update(Request $request,$id): JsonResponse
    {
        try {
            $resignation = Resignation::where('id',$id)->first();
            if (!$resignation) {
                return $this->errorResponse(__('Resignation not exists.'), 404);
            }
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Resignation'))
            {
                if ($resignation->created_by == Auth::user()->creatorId()) {
                    $validator = Validator::make($request->all(), [
                        'notice_date' => 'required',
                        'resignation_date' => 'required',
                        'description' => 'required',
                        'employee_id' => 'required',
                    ]);

                    if ($validator->fails()) {
                        return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                    }

                    if (Auth::user()->type != 'employee') {
                        $resignation->employee_id = $request->employee_id;
                    }

                    $resignation->notice_date = $request->notice_date;
                    $resignation->resignation_date = $request->resignation_date;
                    $resignation->description = $request->description;
                    $resignation->save();

                    return $this->successResponse(['resignation'=>$resignation],__('Resignation successfully updated.'));
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
     *     path="/api/hr/rasignation/{resignation}",
     *     tags={"HR Resignation"},
     *     summary="Delete a specific resignation",
     *     description="Deletes a specific resignation by ID.",
     *     @OA\Parameter(
     *         name="resignation",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the resignation"
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
     *         description="Resignation deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resignation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Resignation not found.")
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
    public function destroy($id): JsonResponse
    {
        try {
            $resignation = Resignation::where('id',$id)->first();
            if (!$resignation) {
                return $this->errorResponse(__('Resignation not exists.'), 404);
            }
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Delete Resignation'))
            {
                if ($resignation->created_by == Auth::user()->creatorId()) {
                    $resignation->delete();
                    return $this->successResponse(null,__('Resignation successfully deleted.'));
                }
                return $this->errorResponse(__('Permission denied.'), 403);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
