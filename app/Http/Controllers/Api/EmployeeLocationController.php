<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLocation;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EmployeeLocationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/employee-location",
     *     summary="Get All Employee Locations",
     *     description="Retrieves a list of all employee locations with optional filters",
     *     operationId="getAllEmployeeLocations",
     *     tags={"Employee Locations"},
     *     @OA\Parameter(
     *         name="employee_id",
     *         in="query",
     *         @OA\Schema(type="integer"),
     *         description="Filter by specific employee ID"
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         @OA\Schema(type="string", format="date"),
     *         description="Start date filter (Y-m-d)"
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         @OA\Schema(type="string", format="date"),
     *         description="End date filter (Y-m-d)"
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         @OA\Schema(type="integer", default=100),
     *         description="Number of records to return"
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         @OA\Schema(type="string", enum={"time", "employee_id", "created_at"}, default="time"),
     *         description="Sort field"
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc"),
     *         description="Sort order"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Employee locations retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Employee locations retrieved successfully"),
     *             @OA\Property(property="data", type="array", 
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="employee_id", type="integer", example=1),
     *                     @OA\Property(property="latitude", type="number", format="float", example=28.6139),
     *                     @OA\Property(property="longitude", type="number", format="float", example=77.2090),
     *                     @OA\Property(property="location_name", type="string", example="Office Building A"),
     *                     @OA\Property(property="time", type="string", format="datetime", example="2025-06-10 14:30:00"),
     *                     @OA\Property(property="created_at", type="string", format="datetime"),
     *                     @OA\Property(property="updated_at", type="string", format="datetime"),
     *                     @OA\Property(property="employee", type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function index(Request $request)
    {
        try {
            // Validation for query parameters
            $rules = [
                'employee_id' => 'nullable|integer|exists:employees,id',
                'start_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
                'limit' => 'nullable|integer|min:1|max:1000',
                'sort_by' => 'nullable|in:time,employee_id,created_at',
                'sort_order' => 'nullable|in:asc,desc'
            ];
    
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            // Build query
            $query = EmployeeLocation::with('employee');
    
            // Apply filters
            if ($request->has('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }
    
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('time', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            } elseif ($request->has('start_date')) {
                $query->where('time', '>=', $request->start_date . ' 00:00:00');
            } elseif ($request->has('end_date')) {
                $query->where('time', '<=', $request->end_date . ' 23:59:59');
            }
    
            // Apply sorting
            $sortBy = $request->get('sort_by', 'time');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
    
            // Apply limit
            $limit = $request->get('limit', 100);
            $locations = $query->limit($limit)->get();
    
            // Format response data to ensure location_name is included
            $formattedLocations = $locations->map(function ($location) {
                return [
                    'id' => $location->id,
                    'employee_id' => $location->employee_id,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'location_name' => $location->location_name ?? null,
                    'time' => $location->time,
                    'created_at' => $location->created_at,
                    'updated_at' => $location->updated_at,
                    'employee' => $location->employee ? [
                        'id' => $location->employee->id,
                        'name' => $location->employee->name,
                        'email' => $location->employee->email,
                        'phone' => $location->employee->phone ?? null,
                    ] : null
                ];
            });
    
            return response()->json([
                'success' => true,
                'message' => 'Employee locations retrieved successfully',
                'data' => $formattedLocations
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * @OA\Post(
     *     path="/api/employee-location",
     *     summary="Create Employee Location Record",
     *     description="Creates a new location record for an employee with GPS coordinates, timestamp and location name",
     *     operationId="createEmployeeLocation",
     *     tags={"Employee Locations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"employee_id", "latitude", "longitude", "time"},
     *             @OA\Property(property="employee_id", type="integer", example=1, description="Employee ID"),
     *             @OA\Property(property="latitude", type="number", format="float", example=28.6139, description="Latitude coordinate"),
     *             @OA\Property(property="longitude", type="number", format="float", example=77.2090, description="Longitude coordinate"),
     *             @OA\Property(property="location_name", type="string", example="Office Building A", description="Name/description of the location (optional)"),
     *             @OA\Property(property="time", type="string", format="datetime", example="2025-06-10 14:30:00", description="Location timestamp in yyyy-MM-dd HH:mm:ss format")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Location record created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Location recorded successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="employee_id", type="integer", example=1),
     *                 @OA\Property(property="latitude", type="number", format="float", example=28.6139),
     *                 @OA\Property(property="longitude", type="number", format="float", example=77.2090),
     *                 @OA\Property(property="location_name", type="string", example="Office Building A"),
     *                 @OA\Property(property="time", type="string", format="datetime", example="2025-06-10 14:30:00"),
     *                 @OA\Property(property="created_at", type="string", format="datetime"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime"),
     *                 @OA\Property(property="employee", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Employee not found")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'employee_id' => [
                    'required',
                    'integer',
                    Rule::exists('employees', 'id')
                ],
                'latitude' => [
                    'required',
                    'numeric',
                    'between:-90,90'
                ],
                'longitude' => [
                    'required',
                    'numeric',
                    'between:-180,180'
                ],
                'location_name' => [
                    'nullable',
                    'string',
                    'max:255'
                ],
                'time' => [
                    'required',
                    'string'
                ]
            ];
    
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            // Check if employee exists
            $employee = Employee::find($request->employee_id);
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }
    
            // Parse and validate the time format from Kotlin app
            try {
                // Handle Kotlin time format: yyyy-MM-dd HH:mm:ss
                $timeString = $request->time;
                
                // Define the timezone that the Kotlin app is using (India timezone)
                $appTimezone = 'Asia/Kolkata'; // Change this to your app's timezone
                
                // Try multiple date formats to handle different Kotlin date formats
                $possibleFormats = [
                    'Y-m-d H:i:s',     // 2025-06-10 14:30:00
                    'Y-m-d\TH:i:s',    // 2025-06-10T14:30:00 (ISO format)
                    'Y-m-d\TH:i:s\Z',  // 2025-06-10T14:30:00Z (UTC format)
                    'Y-m-d\TH:i:s.u',  // 2025-06-10T14:30:00.123456 (with microseconds)
                    'Y-m-d\TH:i:s.u\Z' // 2025-06-10T14:30:00.123456Z (UTC with microseconds)
                ];
                
                $parsedTime = null;
                foreach ($possibleFormats as $format) {
                    try {
                        // Create Carbon instance in the app's timezone
                        $parsedTime = Carbon::createFromFormat($format, $timeString, $appTimezone);
                        if ($parsedTime !== false) {
                            break;
                        }
                    } catch (\Exception $e) {
                        continue; // Try next format
                    }
                }
                
                // If none of the formats work, try Carbon's general parsing with timezone
                if (!$parsedTime) {
                    $parsedTime = Carbon::parse($timeString, $appTimezone);
                }
                
                // Store the exact time as received (no timezone conversion)
                // This preserves the original time from the Kotlin app
                $formattedTime = $parsedTime->format('Y-m-d H:i:s');
                
                // Log for debugging (remove in production)
                \Log::info('Time Conversion Debug', [
                    'original_time' => $timeString,
                    'parsed_time' => $parsedTime->toString(),
                    'formatted_time' => $formattedTime,
                    'timezone' => $appTimezone
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid time format. Please use yyyy-MM-dd HH:mm:ss format.',
                    'error_details' => $e->getMessage()
                ], 422);
            }
    
            // Create location record
            $locationData = [
                'employee_id' => $request->employee_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'time' => $formattedTime,
            ];
    
            // Add location_name if provided
            if ($request->has('location_name') && !empty($request->location_name)) {
                $locationData['location_name'] = $request->location_name;
            }
    
            $location = EmployeeLocation::create($locationData);
    
            // Load the employee relationship
            $location->load('employee');
    
            // Prepare response data
            $responseData = [
                'id' => $location->id,
                'employee_id' => $location->employee_id,
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'location_name' => $location->location_name,
                'time' => $location->time,
                'formatted_time' => Carbon::parse($location->time)->format('d M Y, h:i A'), // User-friendly format
                'created_at' => $location->created_at,
                'updated_at' => $location->updated_at,
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'phone' => $employee->phone ?? null,
                ]
            ];
    
            return response()->json([
                'success' => true,
                'message' => 'Location recorded successfully',
                'data' => $responseData
            ], 201);
    
        } catch (\Exception $e) {
            \Log::error('Employee Location Store Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while recording location',
                'error_details' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/employee-locations/{employeeId}",
     *     summary="Get Employee Location History",
     *     description="Retrieves location history for a specific employee",
     *     operationId="getEmployeeLocations",
     *     tags={"Employee Locations"},
     *     @OA\Parameter(
     *         name="employeeId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Employee ID"
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         @OA\Schema(type="string", format="date"),
     *         description="Start date filter (Y-m-d)"
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         @OA\Schema(type="string", format="date"),
     *         description="End date filter (Y-m-d)"
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         @OA\Schema(type="integer", default=50),
     *         description="Number of records to return"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location history retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Location history retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function show($employeeId, Request $request)
    {
        try {
            $query = EmployeeLocation::with('employee')->where('employee_id', $employeeId);

            // Apply date filters if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('time', [
                    $request->start_date . ' 00:00:00', 
                    $request->end_date . ' 23:59:59'
                ]);
            }

            // Apply limit
            $limit = $request->get('limit', 50);
            $locations = $query->orderBy('time', 'desc')->limit($limit)->get();

            return response()->json([
                'success' => true,
                'message' => 'Location history retrieved successfully',
                'data' => $locations
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/employee-locations/{employeeId}/latest",
     *     summary="Get Latest Employee Location",
     *     description="Retrieves the most recent location for a specific employee",
     *     operationId="getLatestEmployeeLocation",
     *     tags={"Employee Locations"},
     *     @OA\Parameter(
     *         name="employeeId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Employee ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Latest location retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Latest location retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No location records found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No location records found for this employee")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function latest($employeeId)
    {
        try {
            $location = EmployeeLocation::with('employee')
                ->where('employee_id', $employeeId)
                ->orderBy('id', 'desc')
                ->first();

            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'No location records found for this employee'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Latest location retrieved successfully',
                'data' => $location
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}