<?php

namespace App\Http\Controllers\Api\Hr;

use App\Models\Award;
use App\Models\AwardType;
use App\Models\Employee;
use App\Mail\AwardSend;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\AwardRequest;
use Illuminate\Support\Facades\Validator;


class AwardController extends BaseController
{

    /**
     * Display a listing of the awards.
     *
     * @return JsonResponse
     */

     /**
     * @OA\Get(
     *     path="/api/hr/awards",
     *     tags={"HR Awards"},
     *     summary="Get all HR awards",
     *     description="Returns a list of all HR awards based on the authenticated user's permissions.",
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
     *                     @OA\Property(property="award_type", type="integer", example=5),
     *                     @OA\Property(property="date", type="string", format="date", example="2023-10-01"),
     *                     @OA\Property(property="gift", type="string", example="Gift Card"),
     *                     @OA\Property(property="description", type="string", example="Employee of the Month")
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
            if ($user->getAllPermissions()->where('guard_name','web')->pluck('name')->contains('Manage Role'))
            {

                $awards = $user->type === 'employee'
                    ? Award::where('employee_id', Employee::where('user_id', $user->id)->first()->id)->get()
                    : Award::with('employee_detail')->where('created_by', $user->creatorId())->get();

                return $this->successResponse([
                    'awards' => $awards,
                ]);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new award.
     *
     * @return JsonResponse
     */

    /**
     * @OA\Get(
     *     path="/api/hr/awards/create",
     *     tags={"HR Awards"},
     *     summary="Show the form for creating a new HR award",
     *     description="Returns the necessary data to create a new HR award, including employees and award types.",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form data for creating a new award",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="employees", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=101),
     *                         @OA\Property(property="name", type="string", example="John Doe")
     *                     )
     *                 ),
     *                 @OA\Property(property="awardTypes", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="name", type="string", example="Employee of the Month")
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
            if ($user->getAllPermissions()->where('guard_name','web')->pluck('name')->contains('Create Award'))
            {
                $employees = Employee::where('created_by', Auth::user()->creatorId())->active()->pluck('name', 'id');
                $awardTypes = AwardType::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');

                return $this->successResponse([
                    'employees' => $employees,
                    'awardTypes' => $awardTypes,
                ]);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created award in storage.
     *
     * @param AwardRequest $request
     * @return JsonResponse
     */

    /**
     * @OA\Post(
     *     path="/api/hr/awards",
     *     tags={"HR Awards"},
     *     summary="Create a new HR award",
     *     description="Creates a new HR award for an employee.",
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
     *             @OA\Property(property="award_type", type="integer", example=5),
     *             @OA\Property(property="date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="gift", type="string", example="Gift Card"),
     *             @OA\Property(property="description", type="string", example="Employee of the Month")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Award successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Award successfully created.")
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
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->getAllPermissions()->where('guard_name','web')->pluck('name')->contains('Create Award'))
            {
                $rules = [
                    'employee_id' => 'required|exists:employees,id', // Ensure employee exists
                    'award_type' => 'required|exists:award_types,id', // Ensure award type exists
                    'date' => 'required|date', // Ensure date is provided and is a valid date
                    'gift' => 'required|string|max:255', // Ensure gift is provided and is a string
                    'description' => 'nullable|string|max:500', // Description is optional
                ];

                // Create a validator instance
                $validator = Validator::make($request->all(), $rules);
                // Check if validation fails
                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }
                $award = Award::create([
                    'employee_id' => $request->employee_id,
                    'award_type' => $request->award_type,
                    'date' => $request->date,
                    'gift' => $request->gift,
                    'description' => $request->description,
                    'created_by' => Auth::user()->creatorId(),
                ]);

                $data = [
                    'award' => $award,
                ];

                return $this->successResponse($data,__('Award successfully created.'), 201);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified award.
     *
     * @param Award $award
     * @return JsonResponse
     */

    /**
     * @OA\Get(
     *     path="/api/hr/awards/{award}",
     *     tags={"HR Awards"},
     *     summary="Get a specific HR award",
     *     description="Returns details of a specific HR award.",
     *     @OA\Parameter(
     *         name="award",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the award to retrieve"
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
     *         description="Award details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="employee_id", type="integer", example=101),
     *                 @OA\Property(property="award_type", type="integer", example=5),
     *                 @OA\Property(property="date", type="string", format="date", example="2023-10-01"),
     *                 @OA\Property(property="gift", type="string", example="Gift Card"),
     *                 @OA\Property(property="description", type="string", example="Employee of the Month")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Award not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Award not found.")
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
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $award = Award::where('id',$id)->first();
            if (!$award) {
                return $this->errorResponse(__('Award not exists.'), 404);
            }
            return $this->successResponse(['award' => $award]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Show the form for editing the specified award.
     *
     * @param Award $award
     * @return JsonResponse
     */

     /**
     * @OA\Get(
     *     path="/api/hr/awards/{award}/edit",
     *     tags={"HR Awards"},
     *     summary="Show the form for editing an existing HR award",
     *     description="Returns the necessary data to edit an existing HR award.",
     *     @OA\Parameter(
     *         name="award",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the award to edit"
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
     *         description="Form data for editing an existing award",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="award", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="employee_id", type="integer", example=101),
     *                     @OA\Property(property="award_type", type="integer", example=5),
     *                     @OA\Property(property="date", type="string", format="date", example="2023-10-01"),
     *                     @OA\Property(property="gift", type="string", example="Gift Card"),
     *                     @OA\Property(property="description", type="string", example="Employee of the Month")
     *                 ),
     *                 @OA\Property(property="employees", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=101),
     *                         @OA\Property(property="name", type="string", example="John Doe")
     *                     )
     *                 ),
     *                 @OA\Property(property="awardTypes", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="name", type="string", example="Employee of the Month")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Award not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Award not found.")
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
            $award = Award::where('id',$id)->first();
            if (!$award) {
                return $this->errorResponse(__('Award not exists.'), 404);
            }
            if ($user->getAllPermissions()->where('guard_name','web')->pluck('name')->contains('Edit Award')
             && $award->created_by == Auth::user()->creatorId()) {
                $employees = Employee::where('created_by', Auth::user()->creatorId())->active()->pluck('name', 'id');
                $awardTypes = AwardType::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');

                return $this->successResponse([
                    'award' => $award,
                    'employees' => $employees,
                    'awardTypes' => $awardTypes,
                ]);
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified award in storage.
     *
     * @param AwardRequest $request
     * @param Award $award
     * @return JsonResponse
     */

    /**
     * @OA\Put(
     *     path="/api/hr/awards/{award}",
     *     tags={"HR Awards"},
     *     summary="Update an existing HR award",
     *     description="Updates the details of an existing HR award.",
     *     @OA\Parameter(
     *         name="award",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the award to update"
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
     *             @OA\Property(property="award_type", type="integer", example=5),
     *             @OA\Property(property="date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="gift", type="string", example="Gift Card"),
     *             @OA\Property(property="description", type="string", example="Employee of the Month")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Award successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Award successfully updated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Award not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Award not found.")
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
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function update(Request $request,$id): JsonResponse
    {
        try {
            $user = Auth::user();
            $award = Award::where('id',$id)->first();
            if (!$award) {
                return $this->errorResponse(__('Award not exists.'), 404);
            }
            if ($user->getAllPermissions()->where('guard_name','web')->pluck('name')->contains('Edit Award')
             && $award->created_by == Auth::user()->creatorId()) {
                $rules = [
                    'employee_id' => 'nullable|exists:employees,id', // Ensure employee exists
                    'award_type' => 'nullable|exists:award_types,id', // Ensure award type exists
                    'date' => 'nullable|date', // Ensure date is provided and is a valid date
                    'gift' => 'nullable|string|max:255', // Ensure gift is provided and is a string
                    'description' => 'nullable|string|max:500', // Description is optional
                ];

                // Create a validator instance
                $validator = Validator::make($request->all(), $rules);
                // Check if validation fails
                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }
                $validatedData = $validator->validated();

                $award->update($validatedData);

                return $this->successResponse(['award'=>$award],__('Award successfully updated.'));
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified award from storage.
     *
     * @param Award $award
     * @return JsonResponse
     */

     /**
     * @OA\Delete(
     *     path="/api/hr/awards/{award}",
     *     tags={"HR Awards"},
     *     summary="Delete an HR award",
     *     description="Deletes a specific HR award.",
     *     @OA\Parameter(
     *         name="award",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the award to delete"
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
     *         description="Award successfully deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Award not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Award not found.")
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
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = Auth::user();
            $award = Award::where('id',$id)->first();
            if (!$award) {
                return $this->errorResponse(__('Award not exists.'), 404);
            }
            if ($user->getAllPermissions()->where('guard_name','web')->pluck('name')->contains('Delete Award')
            && $award->created_by == Auth::user()->creatorId()) {
                $award->delete();

                return $this->successResponse(null,__('Award successfully deleted.'));
            }

            return $this->errorResponse(__('Permission denied.'), 403);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

}
