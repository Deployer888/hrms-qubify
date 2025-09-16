<?php

namespace App\Http\Controllers\Api\Ticket;

use App\Models\Employee;
use App\Mail\TicketSend;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Api\BaseController;

class TicketController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/ticket",
     *     tags={"Tickets"},
     *     summary="Get all tickets",
     *     description="Retrieve all tickets based on user permissions.",
     *     security={{"bearerAuth": {}}},
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
     *             @OA\Property(property="message", type="string", example="Ticket list."),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Network Issue"),
     *                     @OA\Property(property="priority", type="string", example="high"),
     *                     @OA\Property(property="status", type="string", example="open"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
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
     *     )
     * )
     */
    public function index()
    {
        try {
            $user = Auth::user();
            if ($user->getAllPermissions()->pluck('name')->contains('Manage Ticket')) {
                if ($user->type == 'employee') {
                    $tickets = Ticket::where('employee_id', '=', $user->id)
                        ->orWhere('ticket_created', $user->id)
                        ->get();
                } else {
                    $tickets = Ticket::select('tickets.*')
                        ->join('users', 'tickets.created_by', '=', 'users.id')
                        ->where('users.created_by', '=', $user->creatorId())
                        ->orWhere('tickets.created_by', $user->creatorId())
                        ->get();
                }

                return $this->successResponse(['tickets'=>$tickets],"Ticket list.");
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/ticket/create",
     *     tags={"Tickets"},
     *     summary="Get data for creating a new ticket",
     *     description="Retrieve the list of employees and priorities for creating a new ticket.",
     *     security={{"bearerAuth": {}}},
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
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="priorities", type="array",
     *                     @OA\Items(type="string", example="high")
     *                 ),
     *                 @OA\Property(property="employees", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
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
     *     )
     * )
     */
    public function create()
    {
        try {
            $user = Auth::user();
            if ($user->getAllPermissions()->pluck('name')->contains('Create Ticket')) {
                $employees = User::where('created_by', '=', \Auth::user()->creatorId())
                    ->where('type', '=', 'employee')
                    ->get()
                    ->pluck('name', 'id');
                $priorities = [
                    'low',
                    'medium',
                    'high',
                    'critical',
                ];
                if(Auth::user()->type == "employee")
                {
                    return $this->successResponse(['priorities'=>$priorities]);
                }
                return $this->successResponse(['priorities'=>$priorities,'employees'=>$employees]);
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ticket",
     *     tags={"Tickets"},
     *     summary="Create a new ticket",
     *     description="Create a new ticket with required details",
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
     *             @OA\Property(property="title", type="string", example="Network Issue"),
     *             @OA\Property(property="priority", type="string", enum={"low", "medium", "high", "critical"}, example="high"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-04-15"),
     *             @OA\Property(property="description", type="string", example="Internet connection is down."),
     *             @OA\Property(property="employee_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ticket successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ticket successfully created."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Network Issue"),
     *                 @OA\Property(property="priority", type="string", example="high"),
     *                 @OA\Property(property="status", type="string", example="open")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
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
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->getAllPermissions()->pluck('name')->contains('Create Ticket')) {
                $rules = [
                    'title' => 'required',
                    'priority' => 'required',
                    'end_date' => 'required',
                    'description' => 'required',
                ];
                
                if (Auth::user()->type != "employee") {
                    $rules['employee_id'] = 'required'; // Merge additional rule
                }
                
                $validator = \Validator::make($request->all(), $rules);
                
                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 400, $validator->errors()->toArray());
                }

                $rand = date('hms');
                $ticket = new Ticket();
                $ticket->title = $request->title;
                $ticket->employee_id = Auth::user()->type == "employee" ? Auth::user()->id : $request->employee_id;
                $ticket->priority = $request->priority;
                $ticket->end_date = $request->end_date;
                $ticket->ticket_code = $rand;
                $ticket->description = $request->description;
                $ticket->ticket_created = Auth::user()->id;
                $ticket->created_by = Auth::user()->creatorId();
                $ticket->status = 'open';
                $ticket->save();

                // Notification logic (slack, telegram, twilio) can be added here

                return $this->successResponse(['ticket'=>$ticket], __('Ticket successfully created.'));
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ticket/{ticket}",
     *     tags={"Tickets"},
     *     summary="Get a specific ticket",
     *     description="Retrieve a specific ticket by its ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="ticket",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the ticket"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ticket retrieved successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Network Issue"),
     *                 @OA\Property(property="priority", type="string", example="high"),
     *                 @OA\Property(property="status", type="string", example="open"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ticket not found.")
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
     *     )
     * )
     */
    public function show($ticket)
    {
        try {
            $ticket = Ticket::find($ticket);
            if (!$ticket) {
                return $this->errorResponse('Ticket not found.', 404);
            }
            $user = Auth::user();
            if ($user->getAllPermissions()->pluck('name')->contains('Manage Ticket'))
            {
                return $this->successResponse(['ticket' => $ticket]);
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/ticket/{ticket}/edit",
     *     tags={"Tickets"},
     *     summary="Edit a specific ticket",
     *     description="Retrieve data for editing a specific ticket.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="ticket",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the ticket"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ticket data retrieved successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="ticket", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Network Issue"),
     *                     @OA\Property(property="priority", type="string", example="high"),
     *                     @OA\Property(property="status", type="string", example="open")
     *                 ),
     *                 @OA\Property(property="priorities", type="array",
     *                     @OA\Items(type="string", example="high"),
     *                 ),
     *                 @OA\Property(property="employees", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="John Doe")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ticket not found.")
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
     *     )
     * )
     */
    public function edit($ticket)
    {
        try {
            $ticket = Ticket::find($ticket);
            if (!$ticket) {
                return $this->errorResponse('Ticket not found.', 404);
            }
            $user = Auth::user();
            if ($user->getAllPermissions()->pluck('name')->contains('Manage Ticket'))
            {
                $employees = User::where('created_by', '=', \Auth::user()->creatorId())
                    ->where('type', '=', 'employee')
                    ->get()
                    ->pluck('name', 'id');

                $priorities = [
                    'low',
                    'medium',
                    'high',
                    'critical',
                ];
                $status = [
                    'close',
                    'open',
                    'onhold',
                ];

                if(Auth::user()->type == "employee")
                {
                    return $this->successResponse(['priorities'=>$priorities,'ticket' => $ticket,'status'=>$status]);
                }
                return $this->successResponse(['priorities'=>$priorities,'ticket' => $ticket,'status'=>$status,'employees'=>$employees]);
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Put(
     *     path="/api/ticket/{ticket}",
     *     tags={"Tickets"},
     *     summary="Update a specific ticket",
     *     description="Update the details of a specific ticket.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="ticket",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the ticket"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Network Issue"),
     *             @OA\Property(property="priority", type="string", enum={"low", "medium", "high", "critical"}, example="high"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-04-15"),
     *             @OA\Property(property="description", type="string", example="Internet connection is down."),
     *             @OA\Property(property="employee_id", type="integer", example=2),
     *             @OA\Property(property="status", type="string", enum={"open", "close", "onhold"}, example="open")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ticket successfully updated."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Network Issue"),
     *                 @OA\Property(property="priority", type="string", example="high"),
     *                 @OA\Property(property="status", type="string", example="open")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ticket not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
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
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $ticket)
    {
        try {
            $ticket = Ticket::find($ticket);
            if (!$ticket) {
                return $this->errorResponse('Ticket not found.', 404);
            }
            $user = Auth::user();
            if ($user->getAllPermissions()->pluck('name')->contains('Edit Ticket'))
            {
                $rules = [
                    'title' => 'required',
                    'priority' => 'required',
                    'end_date' => 'required',
                    'description' => 'required',
                ];
                
                if (Auth::user()->type != "employee") {
                    $rules['employee_id'] = 'required'; // Merge additional rule
                }
                
                $validator = \Validator::make($request->all(), $rules);
                
                if ($validator->fails()) {
                    return $this->errorResponse('Validation error', 400, $validator->errors()->toArray());
                }

                $ticket->title = $request->title;
                $ticket->employee_id = Auth::user()->type == "employee" ? Auth::user()->id : $request->employee_id;
                $ticket->priority = $request->priority;
                $ticket->end_date = $request->end_date;
                $ticket->description = $request->description;
                $ticket->status = $request->status;
                $ticket->save();

                return $this->successResponse(['ticket' => $ticket], __('Ticket successfully updated.'));
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/ticket/{ticket}",
     *     tags={"Tickets"},
     *     summary="Delete a specific ticket",
     *     description="Delete a specific ticket by its ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="ticket",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the ticket"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Ticket successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ticket successfully deleted.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ticket not found.")
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
     *     )
     * )
     */
    public function destroy($ticket)
    {
        try {
            $ticket = Ticket::find($ticket);
            if (!$ticket) {
                return $this->errorResponse('Ticket not found.', 404);
            }
            $user = Auth::user();
            if ($user->getAllPermissions()->pluck('name')->contains('Delete Ticket'))
            {
                if ($ticket->created_by == \Auth::user()->creatorId()) {
                    $ticket->delete();
                    TicketReply::where('ticket_id', $ticket->id)->delete();

                    return $this->successResponse(null, __('Ticket successfully deleted.'));
                } 
            } else {
                return $this->errorResponse(__('Permission denied.'), 403);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/ticket/{ticket}/reply",
     *     tags={"Tickets"},
     *     summary="Get replies for a specific ticket",
     *     description="Retrieve all replies for a specific ticket.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="ticket",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the ticket"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Replies retrieved successfully."),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="description", type="string", example="This is a reply."),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ticket not found.")
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
     *     )
     * )
     */
    public function reply($ticket)
    {
        try {
            $ticketreply = TicketReply::where('ticket_id', '=', $ticket)->orderBy('id', 'DESC')->get();
            $ticket = Ticket::find($ticket);

            if (\Auth::user()->type == 'employee') {
                TicketReply::where('ticket_id', $ticket->id)->where('created_by', '!=', \Auth::user()->id)->update(['is_read' => '1']);
            } else {
                TicketReply::where('ticket_id', $ticket->id)->where('created_by', '!=', \Auth::user()->creatorId())->update(['is_read' => '1']);
            }

            return $this->successResponse(['ticket' => $ticket, 'replies' => $ticketreply]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/ticket/reply",
     *     tags={"Tickets"},
     *     summary="Reply to a specific ticket",
     *     description="Send a reply to a specific ticket.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string", example="This is a reply."),
     *             @OA\Property(property="ticket_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reply successfully sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ticket Reply successfully sent."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="description", type="string", example="This is a reply.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type=" boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ticket not found.")
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
     *     )
     * )
     */
    public function changereply(Request $request)
    {
        try {
            $validator = \Validator::make(
                $request->all(),
                [
                    'description' => 'required',
                    'ticket_id' => 'required',
                ]
            );

            if ($validator->fails()) {
                return $this->errorResponse('Validation error', 400, $validator->errors()->toArray());
            }

            $ticket = Ticket::find($request->ticket_id);
            if (!$ticket) {
                return $this->errorResponse('Ticket not found.', 404);
            }
            $ticket_reply = new TicketReply();
            $ticket_reply->ticket_id = $request->ticket_id;
            $ticket_reply->employee_id = $ticket->employee_id;
            $ticket_reply->description = $request->description;
            $ticket_reply->created_by = \Auth::user()->type == 'employee' ? Auth::user()->id : Auth::user()->creatorId();
            $ticket_reply->save();

            return $this->successResponse($ticket_reply, __('Ticket Reply successfully sent.'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
