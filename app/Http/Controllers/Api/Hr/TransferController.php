<?php

namespace App\Http\Controllers\Api\Hr;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Mail\TransferSend;
use App\Models\Transfer;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;

class TransferController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/hr/transfers",
     *     tags={"HR Transfers"},
     *     summary="Get all transfers",
     *     description="Returns a list of all transfers based on the authenticated user's permissions.",
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
     *                     @OA\Property(property="branch_id", type="integer", example=5),
     *                     @OA\Property(property="department_id", type="integer", example=3),
     *                     @OA\Property(property="transfer_date", type="string", format="date", example="2023-10-01"),
     *                     @OA\Property(property="description", type="string", example="Transfer to new department")
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Manage Transfer')) {
                $transfers = $user->type === 'employee'
                    ? Transfer::where('employee_id', Employee::where('user_id', $user->id)->first()->id)->get()
                    : Transfer::with('employee_detail')->where('created_by', $user->creatorId())->get();

                return $this->successResponse(['transfers' => $transfers]);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/transfers/create",
     *     tags={"HR Transfers"},
     *     summary="Show the form for creating a new transfer",
     *     description="Returns the necessary data to create a new transfer.",
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
     *                 @OA\Property(property="departments", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="name", type="string", example="HR Department")
     *                     )
     *                 ),
     *                 @OA\Property(property="branches", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="name", type="string", example="Main Branch")
     *                     )
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Transfer')) 
            {
                $departments = Department::where('created_by', $user->creatorId())->get()->pluck('name', 'id');
                $branches = Branch::where('created_by', $user->creatorId())->get()->pluck('name', 'id');
                $employees = Employee::where('created_by', $user->creatorId())->get()->pluck('name', 'id');

                return $this->successResponse([
                    'departments' => $departments,
                    'branches' => $branches,
                    'employees' => $employees,
                ]);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/hr/transfers",
     *     tags={"HR Transfers"},
     *     summary="Create a new transfer",
     *     description="Stores a new transfer for an employee.",
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
     *             @OA\Property(property="branch_id", type="integer", example=5),
     *             @OA\Property(property="department_id", type="integer", example=3),
     *             @OA\Property(property="transfer_date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="description", type="string", example="Transfer to new department")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transfer successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Transfer successfully created.")
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
     * *     @OA\Response(
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Transfer')) {
                $validator = \Validator::make($request->all(), [
                    'employee_id' => 'required',
                    'branch_id' => 'required',
                    'department_id' => 'required',
                    'transfer_date' => 'required',
                    'description' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }
                $transfer = new Transfer();
                $transfer->employee_id = $request->employee_id;
                $transfer->branch_id = $request->branch_id;
                $transfer->department_id = $request->department_id;
                $transfer->transfer_date = $request->transfer_date;
                $transfer->description = $request->description;
                $transfer->created_by = $user->creatorId();
                $transfer->save();

                return $this->successResponse(['transfer'=>$transfer],__('Transfer successfully created.'), 201);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/transfers/{transfer}",
     *     tags={"HR Transfers"},
     *     summary="Get a specific transfer",
     *     description="Returns details of a specific transfer by ID.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(
     *         name="transfer",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="employee_id", type="integer", example=101),
     *                 @OA\Property(property="branch_id", type="integer", example=5),
     *                 @OA\Property(property="department_id", type="integer", example=3),
     *                 @OA\Property(property="transfer_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="description", type="string", example="Transfer to new department")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transfer not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Transfer not found.")
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
            $transfer = Transfer::where('id',$id)->first();
            if (!$transfer) {
                return $this->errorResponse(__('Transfer not exists.'), 404);
            }
            return $this->successResponse(['transfer' => $transfer]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/transfers/{transfer}/edit",
     *     tags={"HR Transfers"},
     *     summary="Show the form for editing an existing transfer",
     *     description="Returns the necessary data to edit an existing transfer.",
     *     @OA\Parameter(
     *         name="transfer",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the transfer to edit"
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
     *                 @OA\Property(property="transfer", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="employee_id", type="integer", example=101),
     *                     @OA\Property(property="branch_id", type="integer", example=5),
     *                     @OA\Property(property="department_id", type="integer", example=3),
     *                     @OA\Property(property="transfer_date", type="string", format="date", example="2023-10-01"),
     *                     @OA\Property(property="description", type="string", example="Transfer to new department")
     *                 ),
     *                 @OA\Property(property="departments", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="name", type="string", example="HR Department")
     *                     )
     *                 ),
     *                 @OA\Property(property="branches", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="name", type="string", example="Main Branch")
     *                     )
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
     *         description="Transfer not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Transfer not found.")
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
            $transfer = Transfer::where('id',$id)->first();
            if (!$transfer) {
                return $this->errorResponse(__('Transfer not exists.'), 404);
            }
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Transfer')) {
                if ($transfer->created_by == $user->creatorId()) {
                    $departments = Department::where('created_by', $user->creatorId())->get()->pluck('name', 'id');
                    $branches = Branch::where('created_by', $user->creatorId())->get()->pluck('name', 'id');
                    $employees = Employee::where('created_by', $user->creatorId())->get()->pluck('name', 'id');

                    return $this->successResponse([
                        'transfer' => $transfer,
                        'departments' => $departments,
                        'branches' => $branches,
                        'employees' => $employees,
                    ]);
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
     *     path="/api/hr/transfers/{transfer}",
     *     tags={"HR Transfers"},
     *     summary="Update a specific transfer",
     *     description="Updates the details of a specific transfer.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(
     *         name="transfer",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="employee_id", type="integer", example=101),
     *             @OA\Property(property="branch_id", type="integer", example=5),
     *             @OA\Property(property="department_id", type="integer", example=3),
     *             @OA\Property(property="transfer_date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="description", type="string", example="Updated transfer description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transfer successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Transfer successfully updated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transfer not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Transfer not found.")
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
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Transfer')) {
                $transfer = Transfer::where('id',$id)->first();
                if (!$transfer) {
                    return $this->errorResponse(__('Transfer not exists.'), 404);
                }
                if ($transfer->created_by == $user->creatorId()) {
                    $validator = \Validator::make($request->all(), [
                        'employee_id' => 'required',
                        'branch_id' => 'required',
                        'department_id' => 'required',
                        'transfer_date' => 'required',
                        'description' => 'required',
                    ]);

                    if ($validator->fails()) {
                        return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                    }

                    $transfer->employee_id = $request->employee_id;
                    $transfer->branch_id = $request->branch_id;
                    $transfer->department_id = $request->department_id;
                    $transfer->transfer_date = $request->transfer_date;
                    $transfer->description = $request->description;
                    $transfer->save();

                    return $this->successResponse(['transfer'=>$transfer],__('Transfer successfully updated.'));
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
     *     path="/api/hr/transfers/{transfer}",
     *     tags={"HR Transfers"},
     *     summary="Delete a specific transfer",
     *     description="Deletes a specific transfer by ID.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(
     *         name="transfer",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Transfer successfully deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transfer not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Transfer not found.")
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
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Delete Transfer')) {
                $transfer = Transfer::where('id',$id)->first();
                if (!$transfer) {
                    return $this->errorResponse(__('Transfer not exists.'), 404);
                }
                if ($transfer->created_by == $user->creatorId()) {
                    $transfer->delete();
                    return $this->successResponse(null,__('Transfer successfully deleted.'));
                }

                return $this->errorResponse(__('Permission denied.'), 403);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
