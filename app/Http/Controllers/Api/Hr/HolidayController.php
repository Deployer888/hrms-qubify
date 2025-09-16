<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use App\Exports\HolidayExport;
use App\Imports\HolidayImport;
use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class HolidayController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/hr/holidays",
     *     tags={"Holidays"},
     *     summary="Get all holidays",
     *     description="Retrieve a list of holidays based on user permissions and optional date filters.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="Start date for filtering holidays"
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="End date for filtering holidays"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Holiday list."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="check", type="string", example="If data is not coming, check request should be like this: api/hr/holidays?start_date=2025-01-01&end_date=2025-12-31."),
     *                 @OA\Property(property="holidays", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="date", type="string", format="date", example="2025-01-01"),
     *                         @OA\Property(property="occasion", type="string", example="New Year"),
     *                         @OA\Property(property="created_by", type="integer", example=1)
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
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Manage Holiday')) {
                $holidays = Holiday::where('created_by', $user->creatorId());

                if ($request->has('start_date'))
                {
                    $holidays->where('date', '>=', $request->start_date);
                }
                if ($request->has('end_date'))
                {
                    $holidays->where('date', '<=', $request->end_date);
                }
                $holidays = $holidays->get()->toarray();
                $data = [
                    'check'=> 'If data in not comming check request should like this api/hr/holidays?start_date=2025-01-01&end_date=2025-12-31.',
                    'holidays'=>$holidays,
                ];
                return $this->successResponse($data,'Holiday list.');
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }

    public function create(): JsonResponse
    {
        // This method is typically not needed in an API context
        return $this->errorResponse(__('This action is not allowed in API context.'), 405);
    }

    /**
     * @OA\Post(
     *     path="/api/hr/holidays",
     *     tags={"Holidays"},
     *     summary="Create a new holiday",
     *     description="Create a new holiday with the specified date and occasion.",
     *     security={{"bearerAuth": {}}},
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
     *             @OA\Property(property="date", type="string", format="date", example="2025-01-01"),
     *             @OA\Property(property="occasion", type="string", example="New Year")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Holiday successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Holiday successfully created."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="date", type="string", format="date", example="2025-01-01"),
     *                 @OA\Property(property="occasion", type="string", example="New Year"),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
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
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */

    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Create Holiday')) {
                $validator = \Validator::make($request->all(), [
                    'date' => 'required|date',
                    'occasion' => 'required|string',
                ]);

                if ($validator->fails())
                {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }

                $holiday = new Holiday();
                $holiday->date = $request->date;
                $holiday->occasion = $request->occasion;
                $holiday->created_by = $user->creatorId();
                $holiday->save();

                return $this->successResponse(['holiday'=>$holiday], __('Holiday successfully created.'), 201);
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/hr/holidays/{holiday}",
     *     tags={"Holidays"},
     *     summary="Get a specific holiday",
     *     description="Retrieve a specific holiday by its ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="holiday",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the holiday"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Holiday retrieved successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="date", type="string", format="date", example="2025-01-01"),
     *                 @OA\Property(property="occasion", type="string", example="New Year"),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Holiday not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Holiday not exists.")
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
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        // Implement if needed
        $holiday = Holiday::where('id',$id)->first();
        if (!$holiday) {
            return $this->errorResponse(__('Holiday not exists.'), 404);
        }
        return $this->successResponse(['holiday'=>$holiday]);
    }
    /**
     * @OA\Put(
     *     path="/api/hr/holidays/{holiday}",
     *     tags={"Holidays"},
     *     summary="Update a specific holiday",
     *     description="Update the details of a specific holiday.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="holiday",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the holiday"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="date", type="string", format="date", example="2025-01-01"),
     *             @OA\Property(property="occasion", type="string", example="New Year")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Holiday successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Holiday successfully updated."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="date", type="string", format="date", example="2025-01-01"),
     *                 @OA\Property(property="occasion", type="string", example="New Year"),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Holiday not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Holiday not exists.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
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
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */
    
    public function update(Request $request,$id): JsonResponse
    {
        try {
            $user = Auth::user();
            $holiday = Holiday::where('id',$id)->first();
            if (!$holiday) {
                return $this->errorResponse(__('Holiday not exists.'), 404);
            }
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Edit Holiday')) {
                $validator = \Validator::make($request->all(), [
                    'date' => 'required|date',
                    'occasion' => 'required|string',
                ]);

                if ($validator->fails())
                {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }

                $holiday->date = $request->date;
                $holiday->occasion = $request->occasion;
                $holiday->save();

                return $this->successResponse(['holiday'=>$holiday], __('Holiday successfully updated.'));
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/hr/holidays/{holiday}",
     *     tags={"Holidays"},
     *     summary="Delete a specific holiday",
     *     description="Delete a specific holiday by its ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="holiday",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the holiday"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Holiday successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Holiday successfully deleted.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Holiday not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Holiday not exists.")
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
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */
    public function delete($id): JsonResponse
    {
        try {
            $user = Auth::user();
            $holiday = Holiday::where('id',$id)->first();
            if (!$holiday) {
                return $this->errorResponse(__('Holiday not exists.'), 404);
            }
            if ($user->getAllPermissions()->where('guard_name', 'web')->pluck('name')->contains('Delete Holiday')) {
                $holiday->delete();

                return $this->successResponse(null, __('Holiday successfully updated.'));
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }
}
