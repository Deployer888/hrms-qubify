<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Api\BaseController;
use App\Models\Complaint;
use App\Models\Employee;
use App\Mail\ComplaintsSend;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;


class ComplaintController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/hr/complaint",
     *     tags={"HR Complaint"},
     *     summary="Get all complaints",
     *     description="Returns a list of all complaints based on the authenticated user's permissions.",
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
     *                     @OA\Property(property="title", type="string", example="Complaint Title"),
     *                     @OA\Property(property="description", type="string", example="Complaint description here."),
     *                     @OA\Property(property="complaint_date", type="string", format="date", example="2023-10-01"),
     *                     @OA\Property(property="complaint_against", type="integer", example=101),
     *                     @OA\Property(property="complaint_from", type="integer", example=102),
     *                     @OA\Property(property="created_by", type="integer", example=1)
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Manage Complaint')) {
                if ($user->type == 'employee') {
                    $emp = Employee::where('user_id', $user->id)->first();
                    $complaints = Complaint::where('complaint_from', $emp->id)->get();
                } else {
                    $complaints = Complaint::where('created_by', $user->creatorId())->get();
                }

                return $this->successResponse(['complaints' => $complaints]);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/complaint/create",
     *     tags={"HR Complaint"},
     *     summary="Show form for creating a new complaint",
     *     description="Returns form data for creating a new complaint.",
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Complaint')) {
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
     *     path="/api/hr/complaint",
     *     tags={"HR Complaint"},
     *     summary="Create a new complaint",
     *     description="Creates a new complaint based on the provided data.",
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
     *             @OA\Property(property="title", type="string", example="Complaint Title"),
     *             @OA\Property(property="description", type="string", example="Complaint description here."),
     *             @OA\Property(property="complaint_against", type="integer", example=101),
     *             @OA\Property(property="complaint_from", type="integer", example=102),
     *             @OA\Property(property="complaint_date", type="date", example="2025-04-06")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Complaint created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Complaint Title"),
     *                 @OA\Property(property="description", type="string", example="Complaint description here."),
     *                 @OA\Property(property="complaint_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="complaint_against", type="integer", example=101),
     *                 @OA\Property(property="complaint_from", type="integer", example=102),
     *                 @OA\Property(property="created_by", type="integer", example=1)
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Complaint')) {
                $validator = Validator::make($request->all(), [
                    'complaint_against' => 'required',
                    'title' => 'required',
                    'complaint_date' => 'required',
                    'description' => 'required',
                ]);

                if ($user->type != 'employee') {
                    $validator->after(function ($validator) use ($request) {
                        if (!$request->has('complaint_from')) {
                            $validator->errors()->add('complaint_from', 'The complaint from field is required.');
                        }
                    });
                }

                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }

                $complaint = new Complaint();
                if ($user->type == 'employee') {
                    $emp = Employee::where('user_id', $user->id)->first();
                    $complaint->complaint_from = $emp->id;
                } else {
                    $complaint->complaint_from = $request->complaint_from;
                }

                $complaint->complaint_against = $request->complaint_against;
                $complaint->title = $request->title;
                $complaint->complaint_date = $request->complaint_date;
                $complaint->description = $request->description;
                $complaint->created_by = $user->creatorId();
                $complaint->save();

                // Send email notification if required
                // (Assuming Utility::settings() and Mail::to() are defined elsewhere)

                return $this->successResponse(['complaint' => $complaint], __('Complaint successfully created.'), 201);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/hr/complaint/{id}",
     *     tags={"HR Complaint"},
     *     summary="Get a specific complaint",
     *     description="Returns details of a specific complaint.",
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
     *                 @OA\Property(property="title", type="string", example="Complaint Title"),
     *                 @OA\Property(property="description", type="string", example="Complaint description here."),
     *                 @OA\Property(property="complaint_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="complaint_against", type="integer", example=101),
     *                 @OA\Property(property="complaint_from", type="integer", example=102),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Complaint not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Complaint not found.")
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


    public function show( $id): JsonResponse
    {
        try {
            $complaint = Complaint::where('id',$id)->first();
            if (!$complaint) {
                return $this->errorResponse(__('Complaint not exists.'), 404);
            }
            return $this->successResponse(['complaint' => $complaint]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/complaint/{id}/edit",
     *     tags={"HR Complaint"},
     *     summary="Show form for editing a complaint",
     *     description="Returns form data for editing a specific complaint.",
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
     *                 @OA\Property(property="title", type="string", example="Complaint Title"),
     *                 @OA\Property(property="description", type="string", example="Complaint description here."),
     *                 @OA\Property(property="complaint_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="complaint_against", type="integer", example=101),
     *                 @OA\Property(property="complaint_from", type="integer", example=102),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Complaint not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Complaint not found.")
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
            $complaint = Complaint::where('id',$id)->first();
            if (!$complaint) {
                return $this->errorResponse(__('Complaint not exists.'), 404);
            }
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Complaint')) {
                if ($complaint->created_by == $user->creatorId()) {
                    if ($user->type == 'employee') {
                        $current_employee = Employee::where('user_id', $user->id)->get()->pluck('name', 'id');
                        $employees = Employee::where('user_id', '!=', $user->id)->get()->pluck('name', 'id');
                    } else {
                        $current_employee = Employee::where('user_id', $user->id)->get()->pluck('name', 'id');
                        $employees = Employee::where('created_by', $user->creatorId())->get()->pluck('name', 'id');
                    }

                    return $this->successResponse(['complaint' => $complaint, 'employees' => $employees, 'current_employee' => $current_employee]);
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
     *     path="/api/hr/complaint/{id}",
     *     tags={"HR Complaint"},
     *     summary="Update a specific complaint",
     *     description="Updates a specific complaint based on the provided data.",
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
     *             @OA\Property(property="title", type="string", example="Updated Complaint Title"),
     *             @OA\Property(property="description", type="string", example="Updated complaint description."),
     *             @OA\Property(property="complaint_against", type="integer", example=101),
     *             @OA\Property(property="complaint_from", type="integer", example=102),
     *             @OA\Property(property="complaint_date", type="date", example="2025-04-06")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Complaint updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Updated Complaint Title"),
     *                 @OA\Property(property="description", type="string", example="Updated complaint description."),
     *                 @OA\Property(property="complaint_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="complaint_against", type="integer", example=101),
     *                 @OA\Property(property="complaint_from", type="integer", example=102),
     *                 @OA\Property(property="created_by", type="integer", example=1)
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
     *         description="Complaint not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Complaint not found.")
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

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $complaint = Complaint::where('id',$id)->first();
            if (!$complaint) {
                return $this->errorResponse(__('Complaint not exists.'), 404);
            }
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Complaint')) {
                if ($complaint->created_by == $user->creatorId()) {
                    $validator = Validator::make($request->all(), [
                        'complaint_against' => 'required',
                        'title' => 'required',
                        'complaint_date' => 'required',
                    ]);

                    if ($user->type != 'employee') {
                        $validator->after(function ($validator) use ($request) {
                            if (!$request->has('complaint_from')) {
                                $validator->errors()->add('complaint_from', 'The complaint from field is required.');
                            }
                        });
                    }

                    if ($validator->fails()) {
                        return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                    }

                    if ($user->type == 'employee') {
                        $emp = Employee::where('user_id', $user->id)->first();
                        $complaint->complaint_from = $emp->id;
                    } else {
                        $complaint->complaint_from = $request->complaint_from;
                    }

                    $complaint->complaint_against = $request->complaint_against;
                    $complaint->title = $request->title;
                    $complaint->complaint_date = $request->complaint_date;
                    $complaint->description = $request->description;
                    $complaint->save();

                    return $this->successResponse(['complaint' => $complaint], __('Complaint successfully updated.'));
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
     *     path="/api/hr/complaint/{id}",
     *     tags={"HR Complaint"},
     *     summary="Delete a specific complaint",
     *     description="Deletes a specific complaint identified by its ID.",
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
     *         description="Complaint deleted successfully"
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
     *         description="Complaint not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Complaint not found.")
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
            $complaint = Complaint::where('id',$id)->first();
            if (!$complaint) {
                return $this->errorResponse(__('Complaint not exists.'), 404);
            }
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Delete Complaint')) {
                if ($complaint->created_by == $user->creatorId()) {
                    $complaint->delete();
                    return $this->successResponse(null, __('Complaint successfully deleted.'));
                }

                return $this->errorResponse(__('Permission denied.'), 403);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
