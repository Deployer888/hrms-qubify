<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Api\BaseController;
use App\Models\Promotion;
use App\Models\Employee;
use App\Models\Designation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class PromotionController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/hr/promotion",
     *     tags={"HR Promotion"},
     *     summary="Get all promotions",
     *     description="Returns a list of all promotions based on the authenticated user's permissions.",
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
     *                     @OA\Property(property="designation_id", type="integer", example=5),
     *                     @OA\Property(property="promotion_title", type="string", example="Senior Developer"),
     *                     @OA\Property(property="promotion_date", type="string", format="date", example="2023-10-01"),
     *                     @OA\Property(property="description", type="string", example="Promoted to Senior Developer")
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Manage Promotion')) {
                if ($user->type == 'employee') {
                    $emp = Employee::where('user_id', $user->id)->first();
                    $promotions = Promotion::where('created_by', $user->creatorId())
                        ->where('employee_id', $emp->id)
                        ->get();
                } else {
                    $promotions = Promotion::where('created_by', $user->creatorId())->get();
                }

                return $this->successResponse(['promotions' => $promotions]);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/promotion/create",
     *     tags={"HR Promotion"},
     *     summary="Show form for creating a new promotion",
     *     description="Returns form data for creating a new promotion.",
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
     *                 @OA\Property(property="designations", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="name", type="string", example="Senior Developer")
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Promotion')) {
                $designations = Designation::where('created_by', $user->creatorId())->get()->pluck('name', 'id');
                $employees = Employee::where('created_by', $user->creatorId())->get()->pluck('name', 'id');

                return $this->successResponse(['employees' => $employees, 'designations' => $designations]);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/hr/promotion",
     *     tags={"HR Promotion"},
     *     summary="Create a new promotion",
     *     description="Creates a new promotion based on the provided data.",
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
     *             required={"employee_id", "designation_id", "promotion_title", "promotion_date", "description"},
     *             @OA\Property(property="employee_id", type="integer"),
     *             @OA\Property(property="designation_id", type="integer"),
     *             @OA\Property(property="promotion_title", type="string"),
     *             @OA\Property(property="promotion_date", type="string", format="date"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Promotion successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="employee_id", type="integer", example=101),
     *                 @OA\Property(property="designation_id", type="integer", example=5),
     *                 @OA\Property(property="promotion_title", type="string", example="Senior Developer"),
     *                 @OA\Property(property="promotion_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="description", type="string", example="Promoted to Senior Developer")
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
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Promotion'))
            {
                $validator = Validator::make($request->all(), [
                    'employee_id' => 'required',
                    'designation_id' => 'required',
                    'promotion_title' => 'required',
                    'promotion_date' => 'required',
                    'description' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }

                $promotion = new Promotion();
                $promotion->employee_id = $request->employee_id;
                $promotion->designation_id = $request->designation_id;
                $promotion->promotion_title = $request->promotion_title;
                $promotion->promotion_date = $request->promotion_date;
                $promotion->description = $request->description;
                $promotion->created_by = $user->creatorId();
                $promotion->save();

                // Send email notifications if required
                // (Assuming Utility::settings() and Mail::to() are defined elsewhere)

                return $this->successResponse(['promotion'=>$promotion],__('Promotion successfully created.'), 201);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/promotion/{id}",
     *     tags={"HR Promotion"},
     *     summary="Get a specific promotion",
     *     description="Returns details of a specific promotion.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="employee_id", type="integer", example=101),
     *                 @OA\Property(property="designation_id", type="integer", example=5),
     *                 @OA\Property(property="promotion_title", type="string", example="Senior Developer"),
     *                 @OA\Property(property="promotion_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="description", type="string", example="Promoted to Senior Developer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Promotion not exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Promotion not exists.")
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
            $promotion = Promotion::where('id',$id)->first();
            if (!$promotion) {
                return $this->errorResponse(__('Promotion not exists.'), 404);
            }
            return $this->successResponse(['promotion' => $promotion]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/promotion/{id}/edit",
     *     tags={"HR Promotion"},
     *     summary="Show form for editing a promotion",
     *     description="Returns form data for editing a specific promotion.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="promotion", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="employee_id", type="integer", example=101),
     *                     @OA\Property(property="designation_id", type="integer", example=5),
     *                     @OA\Property(property="promotion_title", type="string", example="Senior Developer"),
 *                     @OA\Property(property="promotion_date", type="string", format="date", example="2023-10-01"),
     *                     @OA\Property(property="description", type="string", example="Promoted to Senior Developer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Promotion not exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Promotion not exists.")
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
            $promotion = Promotion::where('id',$id)->first();
            if (!$promotion) {
                return $this->errorResponse(__('Promotion not exists.'), 404);
            }
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Promotion')) {
                if ($promotion->created_by == $user->creatorId()) {
                    $designations = Designation::where('created_by', $user->creatorId())->get()->pluck('name', 'id');
                    $employees = Employee::where('created_by', $user->creatorId())->get()->pluck('name', 'id');

                    return $this->successResponse(['promotion' => $promotion, 'employees' => $employees, 'designations' => $designations]);
                }

                return $this->errorResponse(__('Permission denied.1'), 403);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Put(
     *     path="/api/hr/promotion/{id}",
     *     tags={"HR Promotion"},
     *     summary="Update a specific promotion",
     *     description="Updates a specific promotion based on the provided data.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"employee_id", "designation_id", "promotion_title", "promotion_date", "description"},
     *             @OA\Property(property="employee_id", type="integer"),
     *             @OA\Property(property="designation_id", type="integer"),
     *             @OA\Property(property="promotion_title", type="string"),
     *             @OA\Property(property="promotion_date", type="string", format="date"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Promotion successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="employee_id", type="integer", example=101),
     *                 @OA\Property(property="designation_id", type="integer", example=5),
     *                 @OA\Property(property="promotion_title", type="string", example="Senior Developer"),
     *                 @OA\Property(property="promotion_date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="description", type="string", example="Promoted to Senior Developer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Promotion not exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Promotion not exists.")
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
     *             @OA\ Property(property="message", type="string", example="Validation error message")
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
            $promotion = Promotion::where('id',$id)->first();
            if (!$promotion) {
                return $this->errorResponse(__('Promotion not exists.'), 404);
            }
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Promotion')) {
                if ($promotion->created_by == $user->creatorId()) {
                    $validator = Validator::make($request->all(), [
                        'employee_id' => 'required',
                        'designation_id' => 'required',
                        'promotion_title' => 'required',
                        'promotion_date' => 'required',
                        'description' => 'required',
                    ]);

                    if ($validator->fails()) {
                        return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                    }

                    $promotion->employee_id = $request->employee_id;
                    $promotion->designation_id = $request->designation_id;
                    $promotion->promotion_title = $request->promotion_title;
                    $promotion->promotion_date = $request->promotion_date;
                    $promotion->description = $request->description;
                    $promotion->save();

                    return $this->successResponse(['promotion'=>$promotion],__('Promotion successfully updated.'));
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
     *     path="/api/hr/promotion/{id}",
     *     tags={"HR Promotion"},
     *     summary="Delete a specific promotion",
     *     description="Deletes a specific promotion based on the provided ID.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Promotion successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Promotion deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Promotion not exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Promotion not exists.")
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
            $promotion = Promotion::where('id',$id)->first();
            if (!$promotion) {
                return $this->errorResponse(__('Promotion not exists.'), 404);
            }
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Delete Promotion')) {
                if ($promotion->created_by == $user->creatorId()) {
                    $promotion->delete();

                    return $this->successResponse(null,__('Promotion successfully deleted.'));
                }

                return $this->errorResponse(__('Permission denied.'), 403);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
