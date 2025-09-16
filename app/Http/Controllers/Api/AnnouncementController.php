<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementEmployee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Validator;


class AnnouncementController extends BaseController
{

    /**
     * @OA\Get(
     *     path="/api/announcement",
     *     summary="Get a list of announcements",
     *     description="Retrieve a list of announcements based on user type and permissions. Returns announcements either for the current employee or all announcements created by the user.",
     *     tags={"Announcement"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Company Meeting"),
     *                     @OA\Property(property="start_date", type="string", format="date", example="2024-08-30"),
     *                     @OA\Property(property="end_date", type="string", format="date", example="2024-09-01"),
     *                     @OA\Property(property="branch_id", type="integer", example=2),
     *                     @OA\Property(property="department_id", type="string", example="[1,2,3]"),
     *                     @OA\Property(property="employee_id", type="string", example="[1,2,3]"),
     *                     @OA\Property(property="description", type="string", example="Important company meeting"),
     *                     @OA\Property(property="created_by", type="integer", example=1)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="current_employee",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=101),
     *                 @OA\Property(property="name", type="string", example="John Doe")
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
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string", format="bearer-token"),
     *         description="Bearer token for authentication"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            // Check if the user has permission to manage announcements
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Manage Announcement')) {
                $current_employee = Employee::where('user_id', Auth::id())->first();

                if (Auth::user()->type == 'employee') {
                    // Fetch announcements for the current employee
                    $announcements = Announcement::orderBy('id', 'desc')
                        ->leftJoin('announcement_employees', 'announcements.id', '=', 'announcement_employees.announcement_id')
                        ->where('announcement_employees.employee_id', $current_employee->id)
                        ->orWhere(function ($query) {
                            $query->where('announcements.department_id', '["0"]')
                                  ->where('announcements.employee_id', '["0"]');
                        })
                        ->get();
                } else {
                    // Fetch all announcements created by the user
                    $announcements = Announcement::where('created_by', Auth::user()->creatorId())->get();
                }

                return $this->successResponse([
                    'announcements' => $announcements,
                    'current_employee' => $current_employee
                ]);
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/announcement-create",
     *     tags={"Announcement"},
     *     summary="Create Announcement",
     *     description="Fetches and returns employees, branches, and departments for announcement creation. Requires 'Create Announcement' permission.",
     *     operationId="createAnnouncement",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with data",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="employees",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="John Doe")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="branches",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Main Branch")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="departments",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Sales")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Permission denied."
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="bearer",
     *             example="Bearer YOUR_ACCESS_TOKEN"
     *         )
     *     )
     * )
     */
    public function create()
    {
        try {
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Create Announcement')) {
                $employees = Employee::where('created_by', Auth::user()->creatorId())->active()->get()->pluck('name', 'id');
                $employees->prepend('All', 0);
    
                $branches = Branch::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
                $branches->prepend('All', 0);
    
                $departments = Department::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
                $departments->prepend('All', 0);
    
                return $this->successResponse([
                    'employees' => $employees,
                    'branches' => $branches,
                    'departments' => $departments
                ]);
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/announcement-store",
     *     tags={"Announcement"},
     *     summary="Store a new Announcement",
     *     description="Creates a new announcement and sends notifications if configured. Requires 'Create Announcement' permission.",
     *     operationId="storeAnnouncement",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "start_date", "end_date", "branch_id", "department_id", "employee_id", "description"},
     *             @OA\Property(
     *                 property="title",
     *                 type="string",
     *                 description="Title of the announcement",
     *                 example="New Policy Update"
     *             ),
     *             @OA\Property(
     *                 property="start_date",
     *                 type="string",
     *                 format="date",
     *                 description="Start date of the announcement",
     *                 example="2024-09-01"
     *             ),
     *             @OA\Property(
     *                 property="end_date",
     *                 type="string",
     *                 format="date",
     *                 description="End date of the announcement",
     *                 example="2024-09-30"
     *             ),
     *             @OA\Property(
     *                 property="branch_id",
     *                 type="integer",
     *                 description="ID of the branch",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="department_id",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 description="Array of department IDs",
     *             ),
     *             @OA\Property(
     *                 property="employee_id",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 description="Array of employee IDs",
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Description of the announcement",
     *                 example="Please review the updated policy document attached."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Announcement successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Announcement successfully created."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The title field is required."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Permission denied."
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="bearer",
     *             example="Bearer YOUR_ACCESS_TOKEN"
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Create Announcement')) {
                $validator = \Validator::make($request->all(), [
                    'title' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'branch_id' => 'required',
                    'department_id' => 'required|array',
                    'department_id.*' => 'required',
                    'employee_id' => 'required|array',
                    'employee_id.*' => 'required',
                    'description' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                }
                $announcement = new Announcement();
                $announcement->title = $request->title;
                $announcement->start_date = $request->start_date;
                $announcement->end_date = $request->end_date;
                $announcement->branch_id = $request->branch_id;
                $announcement->department_id = implode(",", $request->department_id);
                $announcement->employee_id = implode(",", $request->employee_id);
                $announcement->description = $request->description;
                $announcement->created_by = Auth::user()->creatorId();
                $announcement->save();

                $setting = Utility::settings(Auth::user()->creatorId());
                $branch = Branch::find($request->branch_id);

                if (isset($setting['Announcement_notification']) && $setting['Announcement_notification'] == 1) {
                    $msg = $request->title . ' ' . __("announcement created for branch") . ' ' . $branch->name . ' ' . __("from") . ' ' . $request->start_date . ' ' . __("to") . ' ' . $request->end_date . '.';
                    Utility::send_slack_msg($msg);
                }

                if (isset($setting['telegram_Announcement_notification']) && $setting['telegram_Announcement_notification'] == 1) {
                    $msg = $request->title . ' ' . __("announcement created for branch") . ' ' . $branch->name . ' ' . __("from") . ' ' . $request->start_date . ' ' . __("to") . ' ' . $request->end_date . '.';
                    Utility::send_telegram_msg($msg);
                }

                if (isset($setting['twilio_announcement_notification']) && $setting['twilio_announcement_notification'] == 1) {
                    $employees = Employee::whereIn('id', $request->employee_id)->get();
                    foreach ($employees as $employee) {
                        $msg = $request->title . ' ' . __("announcement created for branch") . ' ' . $branch->name . ' ' . __("from") . ' ' . $request->start_date . ' ' . __("to") . ' ' . $request->end_date . '.';
                        Utility::send_twilio_msg($employee->phone, $msg);
                    }
                }

                $departmentEmployee = in_array('0', $request->employee_id)
                    ? Employee::whereIn('department_id', $request->department_id)->pluck('id')
                    : $request->employee_id;

                foreach ($departmentEmployee as $employee) {
                    $announcementEmployee = new AnnouncementEmployee();
                    $announcementEmployee->announcement_id = $announcement->id;
                    $announcementEmployee->employee_id = $employee;
                    $announcementEmployee->created_by = Auth::user()->creatorId();
                    $announcementEmployee->save();
                }

                return $this->successResponse(['announcement'=>$announcement],__('Announcement successfully created.'), 201);
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/announcement-detail/{id}",
     *     tags={"Announcement"},
     *     summary="Get Announcement Detail",
     *     description="Fetches the details of a specific announcement. Requires 'Create Announcement' permission.",
     *     operationId="getAnnouncementDetail",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         ),
     *         description="The ID of the announcement to retrieve."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Announcement details successfully retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="announcement",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     example="New Policy Update"
     *                 ),
     *                 @OA\Property(
     *                     property="start_date",
     *                     type="string",
     *                     format="date",
     *                     example="2024-09-01"
     *                 ),
     *                 @OA\Property(
     *                     property="end_date",
     *                     type="string",
     *                     format="date",
     *                     example="2024-09-30"
     *                 ),
     *                 @OA\Property(
     *                     property="branch_id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="department_id",
     *                     type="string",
     *                     example="1,2,3"
     *                 ),
     *                 @OA\Property(
     *                     property="employee_id",
     *                     type="string",
     *                     example="1,2,3"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Please review the updated policy document attached."
     *                 ),
     *                 @OA\Property(
     *                     property="created_by",
     *                     type="integer",
     *                     example=1
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied or announcement not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Permission denied or announcement not found."
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="bearer",
     *             example="Bearer YOUR_ACCESS_TOKEN"
     *         )
     *     )
     * )
     */
    public function announcementDetail($announcementId)
    {
        try {
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Create Announcement')) {
                $announcement = Announcement::find($announcementId);
                if (is_null($announcement)) {
                    return $this->errorResponse(__('Announcement not found.'), 404);
                }
                if ($announcement && $announcement->created_by == Auth::user()->creatorId()) {
                    return $this->successResponse([
                        'announcement' => $announcement,
                    ]);
                } else {
                    return $this->errorResponse(__('Permission denied .'), 403);
                }
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/announcement-edit/{id}",
     *     tags={"Announcement"},
     *     summary="Get Announcement for Editing",
     *     description="Fetches details of a specific announcement for editing. Requires 'Edit Announcement' permission.",
     *     operationId="getAnnouncementForEditing",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         ),
     *         description="The ID of the announcement to retrieve for editing."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Announcement details successfully retrieved for editing",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="announcement",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     example="New Policy Update"
     *                 ),
     *                 @OA\Property(
     *                     property="start_date",
     *                     type="string",
     *                     format="date",
     *                     example="2024-09-01"
     *                 ),
     *                 @OA\Property(
     *                     property="end_date",
     *                     type="string",
     *                     format="date",
     *                     example="2024-09-30"
     *                 ),
     *                 @OA\Property(
     *                     property="branch_id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="department_id",
     *                     type="string",
     *                     example="1,2,3"
     *                 ),
     *                 @OA\Property(
     *                     property="employee_id",
     *                     type="string",
     *                     example="1,2,3"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Please review the updated policy document attached."
     *                 ),
     *                 @OA\Property(
     *                     property="created_by",
     *                     type="integer",
     *                     example=1
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="branches",
     *                 type="object",
     *                 @OA\AdditionalProperties(
     *                     type="string"
     *                 ),
     *                 example={
     *                     "1": "Headquarters",
     *                     "2": "Branch Office"
     *                 }
     *             ),
     *             @OA\Property(
     *                 property="departments",
     *                 type="object",
     *                 @OA\AdditionalProperties(
     *                     type="string"
     *                 ),
     *                 example={
     *                     "1": "HR",
     *                     "2": "Finance"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied or announcement not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Permission denied or announcement not found."
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="bearer",
     *             example="Bearer YOUR_ACCESS_TOKEN"
     *         )
     *     )
     * )
     */
    public function edit($announcementId)
    {
        try {
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Edit Announcement')) {
                $announcement = Announcement::find($announcementId);
                if (is_null($announcement)) {
                    return $this->errorResponse(__('Announcement not found.'), 404);
                }
                if ($announcement && $announcement->created_by == Auth::user()->creatorId()) {
                    $employees = Employee::where('created_by', Auth::user()->creatorId())->active()->get()->pluck('name', 'id');
                    $employees->prepend('All', 0);
        
                    $branches = Branch::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
                    $branches->prepend('All', 0);
        
                    $departments = Department::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
                    $departments->prepend('All', 0);
    
                    return $this->successResponse([
                        'announcement' => $announcement,
                        'branches' => $branches,
                        'departments' => $departments,
                        'employees' => $employees,
                    ]);
                } else {
                    return $this->errorResponse(__('Permission denied .'), 403);
                }
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/announcement-update/{id}",
     *     summary="Update an announcement",
     *     description="Update an announcement if the user has the 'Edit Announcement' permission and is the creator of the announcement.",
     *     tags={"Announcement"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the announcement to be updated",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="New Announcement Title"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-08-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-08-31"),
     *             @OA\Property(property="branch_id", type="integer", example=1),
     *             @OA\Property(property="department_id", type="array", @OA\Items(type="integer")),
     *             @OA\Property(property="description", type="string", example="Details about the announcement.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Announcement successfully updated.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Announcement successfully updated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The title field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied or announcement not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied or announcement not found.")
     *         )
     *     ),
     *     @OA\SecurityScheme(
     *         securityScheme="bearerAuth",
     *         type="http",
     *         scheme="bearer"
     *     )
     * )
     */
    public function update(Request $request, $announcementId)
    {
        try {
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Edit Announcement')) {
                $announcement = Announcement::find($announcementId);
                if (is_null($announcement)) {
                    return $this->errorResponse(__('Announcement not found.'), 404);
                }
    
                if ($announcement && $announcement->created_by == Auth::user()->creatorId()) {
                    $validator = \Validator::make(
                        $request->all(), [
                            'title' => 'required',
                            'start_date' => 'required',
                            'end_date' => 'required',
                            'branch_id' => 'required',
                            'department_id' => 'required|array',
                            'department_id.*' => 'required',
                            'employee_id' => 'required|array',
                            'employee_id.*' => 'required',
                        ]
                    );
    
                    
                    if ($validator->fails()) {
                        return $this->errorResponse('Validation error', 422, $validator->errors()->toArray());
                    }
    
                    $announcement->title = $request->title;
                    $announcement->start_date = $request->start_date;
                    $announcement->end_date = $request->end_date;
                    $announcement->branch_id = $request->branch_id;
                    $announcement->department_id = implode(",", $request->department_id);
                    $announcement->employee_id = implode(",", $request->employee_id);
                    $announcement->description = $request->description;
                    $announcement->save();
    
                    return $this->successResponse(['announcement' => $announcement],__('Announcement successfully updated.'));
                } else {
                    return $this->errorResponse(__('Permission denied.'), 403);
                }
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            // Handle any unexpected exceptions
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/announcement-delete/{id}",
     *     summary="Delete an announcement",
     *     description="Delete an announcement if the user has the 'Delete Announcement' permission and is the creator of the announcement.",
     *     tags={"Announcement"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the announcement to be deleted",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Announcement successfully deleted.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Announcement successfully deleted.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied or announcement not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied or announcement not found.")
     *         )
     *     ),
     *     @OA\SecurityScheme(
     *         securityScheme="bearerAuth",
     *         type="http",
     *         scheme="bearer"
     *     )
     * )
     */
    public function destroy($announcementId)
    {
        try {
            if (\Auth::user()->getAllPermissions()->pluck('name')->contains('Delete Announcement')) {
                $announcement = Announcement::find($announcementId);
                if (is_null($announcement)) {
                    return $this->errorResponse(__('Announcement not found.'), 404);
                }
                if ($announcement && $announcement->created_by == Auth::user()->creatorId()) {
                    $announcement->delete();

                    return $this->successResponse(null,__('Announcement successfully deleted.'));
                } else {
                    return $this->errorResponse(__('Permission denied'), 403);
                }
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse(__('An error occurred: ') . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/announcement/getdepartment/{id}",
     *     summary="Get departments for an announcement",
     *     description="Retrieve the departments associated with a specific announcement.",
     *     tags={"Announcement"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the announcement to retrieve departments for",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Departments successfully retrieved.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="departments", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Permission denied.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Announcement not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Announcement not found.")
     *         )
     *     ),
     *     @OA\SecurityScheme(
     *         securityScheme="bearerAuth",
     *         type="http",
     *         scheme="bearer"
     *     )
     * )
     */
    public function getDepartments(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|integer',
        ]);

        $departments = Department::where('created_by', Auth::user()->creatorId())
            ->when($validated['branch_id'] != 0, function ($query) use ($validated) {
                return $query->where('branch_id', $validated['branch_id']);
            })
            ->get()
            ->pluck('name', 'id')
            ->toArray();
        dd($departments);
        return response()->json($departments, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/announcement/getemployee/{id}",
     *     summary="Get employees by department",
     *     description="Retrieve a list of employees filtered by department(s). If '0' is included in the department IDs, all employees will be retrieved regardless of department.",
     *     tags={"Announcement"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="department_id",
     *                 type="array",
     *                 description="Array of department IDs to filter employees by. Include '0' to retrieve all employees.",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of employees successfully retrieved.",
     *         @OA\JsonContent(
     *             type="object",
     *             example={"1": "John Doe", "2": "Jane Smith"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The department_id field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized.")
     *         )
     *     ),
     *     @OA\SecurityScheme(
     *         securityScheme="bearerAuth",
     *         type="http",
     *         scheme="bearer"
     *     )
     * )
     */
    public function getEmployees(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|array',
            'department_id.*' => 'integer'
        ]);

        $employees = Employee::where('created_by', Auth::user()->creatorId())
            ->when(in_array(0, $validated['department_id']), function ($query) {
                return $query;
            })
            ->when(!in_array(0, $validated['department_id']), function ($query) use ($validated) {
                return $query->whereIn('department_id', $validated['department_id']);
            })
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        return response()->json($employees, 200);
    }

    public function getHomeAnnouncementData(Request $request) {
        return $announcements = Announcement::orderBy('announcements.id', 'desc')->take(5)->leftjoin('announcement_employees', 'announcements.id', '=', 'announcement_employees.announcement_id')->where('announcement_employees.employee_id', '=', $request->empId)->orWhere(
                    function ($q){
                        $q->where('announcements.department_id', '["0"]')->where('announcements.employee_id', '["0"]');
                    }
                )->get();
    }

}
