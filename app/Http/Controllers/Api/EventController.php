<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Event;
use App\Models\EventEmployee;
use Illuminate\Support\Facades\Auth;
use App\Models\Utility;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Mail\EventNotification;
use Illuminate\Support\Facades\Response;

class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/events",
     *     summary="Get all events",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Annual Meeting"),
     *                 @OA\Property(property="description", type="string", example="Detailed description of the event."),
     *                 @OA\Property(property="start", type="string", format="date-time", example="2024-09-01 09:00:00"),
     *                 @OA\Property(property="end", type="string", format="date-time", example="2024-09-01 17:00:00"),
     *                 @OA\Property(property="backgroundColor", type="string", example="#FF5733"),
     *                 @OA\Property(property="borderColor", type="string", example="#fff"),
     *                 @OA\Property(property="textColor", type="string", example="white"),
     *                 @OA\Property(property="url", type="string", format="uri", example="https://example.com/events/1/edit")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index()
    {
        if (Auth::user()->getAllPermissions()->pluck('name')->contains('Manage Event')) {
            $events = Event::where('created_by', Auth::user()->creatorId())->get();

            $arrEvents = [];
            foreach ($events as $event) {
                $arr['id']    = $event['id'];
                $arr['title'] = $event['title'];
                $arr['description'] = $event['description'];
                $arr['start'] = $event['start_date'];
                $arr['end']   = $event['end_date'];
                $arr['backgroundColor'] = $event['color'];
                $arr['borderColor']     = "#fff";
                $arr['textColor']       = "white";
                $arr['url']             = route('event.edit', $event['id']);

                $arrEvents[] = $arr;
            }

            return response()->json($arrEvents, 200);
        } else {
            return response()->json(['error' => __('Permission denied.')], 403);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/events",
     *     summary="Create a new event",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EventRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Event successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="string", example="Event successfully created.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The title field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized access.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     )
     * )
     */
    
    /**
     * @OA\Schema(
     *     schema="EventRequest",
     *     type="object",
     *     required={"branch_id", "department_id", "employee_id", "title", "start_date", "end_date", "color"},
     *     @OA\Property(property="branch_id", type="integer", example=1),
     *     @OA\Property(property="department_id", type="array", @OA\Items(type="integer", example=1)),
     *     @OA\Property(property="employee_id", type="array", @OA\Items(type="integer", example=1)),
     *     @OA\Property(property="title", type="string", example="Annual Meeting"),
     *     @OA\Property(property="start_date", type="string", format="date", example="2024-09-01"),
     *     @OA\Property(property="end_date", type="string", format="date", example="2024-09-02"),
     *     @OA\Property(property="color", type="string", example="#FF5733"),
     *     @OA\Property(property="description", type="string", example="This is a detailed description of the event.")
     * )
     */
    public function store(Request $request)
    {
        if (Auth::user()->getAllPermissions()->pluck('name')->contains('Create Event')) {
            $validator = Validator::make($request->all(), [
                'branch_id' => 'required',
                'department_id' => 'required',
                'employee_id' => 'required',
                'title' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'color' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 400);
            }
    
            $event = new Event();
            $event->branch_id     = $request->branch_id;
            $event->department_id = json_encode($request->department_id);
            $event->employee_id   = json_encode($request->employee_id);
            $event->title         = $request->title;
            $event->start_date    = $request->start_date;
            $event->end_date      = $request->end_date;
            $event->color         = $request->color;
            $event->description   = $request->description;
            $event->created_by    = Auth::user()->creatorId();
            $event->save();

            $employees = json_decode($event->employee_id);
            foreach ($employees as $employee) {
                $uArr = [
                    'event_id' => $event->id,
                    'employee_id' => $employee,
                ];
    
                EventEmployee::create($uArr);
            }
    
            return response()->json(['success' => __('Event successfully created.')], 201);
        } else {
            return response()->json(['error' => __('Permission denied.')], 403);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/events/{id}",
     *     summary="Get event details",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the event to retrieve",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="Annual Meeting"),
     *             @OA\Property(property="description", type="string", example="Details of the event."),
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-09-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-09-02"),
     *             @OA\Property(property="color", type="string", example="#FF5733"),
     *             @OA\Property(property="created_by", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized access.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="No data found.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $event = Event::find($id);

        if ($event && $event->created_by == Auth::user()->creatorId()) {
            return response()->json($event, 200);
        } else {
            return response()->json(['error' => __('No data found.')], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/events/{id}",
     *     summary="Update event details",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the event to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EventRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="string", example="Event successfully updated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation failed for one or more fields.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized access.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Event not found.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if ($event && Auth::user()->getAllPermissions()->pluck('name')->contains('Edit Event') && $event->created_by == Auth::user()->creatorId()) {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'color' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 400);
            }

            $event->title       = $request->title;
            $event->start_date  = $request->start_date;
            $event->end_date    = $request->end_date;
            $event->color       = $request->color;
            $event->description = $request->description;
            $event->save();

            return response()->json(['success' => __('Event successfully updated.')], 200);
        } else {
            return response()->json(['error' => __('Permission denied.')], 403);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/events/{id}",
     *     summary="Delete event",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the event to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="string", example="Event successfully deleted.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized access.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Permission denied.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Event not found.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $event = Event::find($id);
        if ($event && Auth::user()->getAllPermissions()->pluck('name')->contains('Manage Event') && $event->created_by == Auth::user()->creatorId()) {
            $event->delete();

            return response()->json(['success' => __('Event successfully deleted.')], 200);
        } else {
            return response()->json(['error' => __('Permission denied.')], 403);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/events/import",
     *     summary="Import events from a file",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="file", type="file", description="File to import")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="File successfully imported"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function importEvent(Request $request)
    {
        if (Auth::user()->getAllPermissions()->pluck('name')->contains('Import Event')) {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 400);
            }

            $events = (new EventImport())->toArray(request()->file('file'))[0];

            foreach ($events as $eventData) {
                $event = new Event();
                $event->branch_id     = $eventData['branch_id'];
                $event->department_id = json_encode(explode(',', $eventData['department_id']));
                $event->employee_id   = json_encode(explode(',', $eventData['employee_id']));
                $event->title         = $eventData['title'];
                $event->start_date    = $eventData['start_date'];
                $event->end_date      = $eventData['end_date'];
                $event->color         = $eventData['color'];
                $event->description   = $eventData['description'];
                $event->created_by    = Auth::user()->creatorId();
                $event->save();
            }

            return response()->json(['success' => __('File successfully imported.')], 200);
        } else {
            return response()->json(['error' => __('Permission denied.')], 403);
        }
    }

}
