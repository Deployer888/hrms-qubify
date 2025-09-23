<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\AadhaarDetail;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Employee;
use App\Models\AttendanceEmployee;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\FcmService;
use Illuminate\Htt;

class KioskController extends BaseController
{
    private $compareUrl = 'https://api-us.faceplusplus.com/facepp/v3/compare';
    private $apiKey;
    private $apiSecret;
    private $confidenceThreshold = 85;
    private $imageSize = 224;
    private $cacheDuration = 720; // 24 hours in minutes
    private $fcmService;
    
    public function __construct(FcmService $fcmService)
    {
        $this->apiKey = env('FACEPP_API_KEY', '');
        $this->apiSecret = env('FACEPP_API_SECRET', '');
        $this->fcmService = $fcmService;
    }
    
    
    public function markAttendanceFromKiosk(Request $request)
    {
        try {
            // Validate required parameters
            $validationResult = $this->validateAttendanceRequest($request);
            if ($validationResult !== true) {
                return $validationResult;
            }
            
            // Get employee_id and time from request
            $employeeId = $request->input('employee_id');
            $employeeDetails = $this->getEmployeeDetails($employeeId);
            $timestamp = $request->input('time');
            
            $timestampArr = explode(' ', $timestamp);
            $requestDate = $timestampArr[0];
            $requestTime = $timestampArr[1];
            
            // Check if time is between 1:45pm and 2:30pm
            $timeObj = \DateTime::createFromFormat('H:i:s', $requestTime);
            $restrictedStartTime = \DateTime::createFromFormat('H:i:s', '13:45:00'); // 1:45 PM
            $restrictedEndTime = \DateTime::createFromFormat('H:i:s', '14:30:00');   // 2:30 PM
            
            // If time is in restricted period and employee is not exempted (3 or 6)
            if ($timeObj >= $restrictedStartTime && $timeObj <= $restrictedEndTime && !in_array($employeeId, [3, 6])) {
                $fcmTokens = User::Where('id', $employeeDetails->user_id)
                                    ->pluck('fcm_token','name')
                                    ->toArray();

                foreach ($fcmTokens as $key => $fcmToken) {
                    $notificationData = [
                        'title' => "Attendance Notification",
                        'body' => "Attendance marking is restricted between 1:45 PM and 2:30 PM",
                        'fcm_token' => $fcmToken,
                    ];
                    try {
                        Helper::sendNotification($notificationData); // Call the helper function
                    } catch (\Exception $e) {
                        \Log::error("Notification Error: " . $e->getMessage());
                    }
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance marking is restricted between 1:45 PM and 2:30 PM',
                    'data' => ['restricted_time' => true]
                ], 200);
            }
            
            // Record attendance with provided parameters
            $attendanceResult = $this->recordAttendance($employeeId, $requestDate, $requestTime);

            if($attendanceResult == false){
                $fcmTokens = User::Where('id', $employeeDetails->user_id)
                                    ->pluck('fcm_token','name')
                                    ->toArray();

                foreach ($fcmTokens as $key => $fcmToken) {
                    $notificationData = [
                        'title' => "Attendance Notification",
                        'body' => "Please wait for a moment and try again.",
                        'fcm_token' => $fcmToken,
                    ];
                    try {
                        Helper::sendNotification($notificationData); // Call the helper function
                    } catch (\Exception $e) {
                        // dd($e->getMessage());
                        \Log::error("Notification Error: " . $e->getMessage());
                    }
                }
                return response()->json([
                    'status' => false,
                    'message' => 'Please wait for a moment and try again.'
                ], 200);
            }
            
            return response()->json(['success' => true], 200);
            
        } catch (\Exception $e) {
            Log::error('Attendance marking error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Attendance marking error',
                'data' => ['message' => $e->getMessage()]
            ], 200);
        }
    }
    
    private function validateAttendanceRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employees,id',
            'time' => 'required|string'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => ['errors' => $validator->errors()]
            ], 200);
        }
    
        return true;
    }
    
    private function recordAttendance($employeeId, $requestDate = null, $requestTime = null)
    {
        // Get employee details
        $employee = $this->getEmployeeDetails($employeeId);
        if (!$employee) {
            throw new \Exception("Employee with ID {$employeeId} not found");
        }
        
        // Parse the provided time or use current time
        /*if ($employee && $requestDate && $requestTime) {
            try {
                // Handle different time formats
                if (strlen($requestTime) > 8) {
                    // Full datetime format: 'Y-m-d H:i:s'
                    $dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $requestTime);
                    $date = $dateTime->format('Y-m-d');
                    $time = $dateTime->format('H:i:s');
                } else {
                    // Just time format: 'H:i:s'
                    $time = Carbon::createFromFormat('H:i:s', $requestTime)->format('H:i:s');
                    $date = Carbon::now()->format('Y-m-d');
                }
            } catch (\Exception $e) {
                throw new \Exception("Invalid time format. Expected 'H:i:s' or 'Y-m-d H:i:s'");
            }
        } else {
            // Use current date and time if not provided
            $date = Carbon::now()->format('Y-m-d');
            $time = Carbon::now()->format('H:i:s');
        }*/
        
        // Check if employee already clocked in for this date
        $existingAttendance = $this->getExistingAttendance($employeeId, $requestDate);

        if ($existingAttendance && isset($existingAttendance['clock_in'])) {
            $existingTime = strtotime($existingAttendance['clock_in']);
            $requestedTime = strtotime($requestTime);
            if (abs($requestedTime - $existingTime) < 30) {
                return false;
            }
        }
        
        

// echo "<pre>";
// print_r($existingAttendance);
        
        
        // Start transaction
        DB::beginTransaction();
        try {
            if ($existingAttendance) {
                // Clock out - only if not already clocked out
                if ($existingAttendance->clock_out != '00:00:00') {
                    // Employee already clocked out, create new clock in
                    $attendance = new AttendanceEmployee();
                    $attendance->employee_name = $employee->name;
                    $attendance->employee_id = $employee->id;
                    $attendance->date = $requestDate;
                    $attendance->status = "Present";
                    $attendance->clock_in = $requestTime;
                    $attendance->save();
                    
                    $action = 'Clock in';
                    $attendanceId = $attendance->id;
                    $message = 'New clock in created (previous session was already completed)';
                } else {
                    // Clock out
                    $existingAttendance->clock_out = $requestTime;
                    $existingAttendance->save();
                    
                    $fcmTokens = User::Where('id', $employee->user_id)
                                ->pluck('fcm_token','name')
                                ->toArray();

                    foreach ($fcmTokens as $key => $fcmToken) {
                        $notificationData = [
                            'title' => "Attendance Notification",
                            'body' => "Clock out successful",
                            'fcm_token' => $fcmToken,
                        ];
                        try {
                            Helper::sendNotification($notificationData); // Call the helper function
                        } catch (\Exception $e) {
                            \Log::error("Notification Error: " . $e->getMessage());
                        }
                    }
                    
                    $action = 'Clock out';
                    $attendanceId = $existingAttendance->id;
                    $message = 'Clock out successful';
                }
            } else {
                // Clock in - first time for this date
                $attendance = new AttendanceEmployee();
                $attendance->employee_name = $employee->name;
                $attendance->employee_id = $employee->id;
                $attendance->date = $requestDate;
                $attendance->status = "Present";
                $attendance->clock_in = $requestTime;
                $attendance->save();
                
                // Update recent employees cache
                $this->addToRecentEmployees($employee->id);
                
                $fcmTokens = User::Where('id', $employee->user_id)
                                ->pluck('fcm_token','name')
                                ->toArray();

                foreach ($fcmTokens as $key => $fcmToken) {
                    $notificationData = [
                        'title' => "Attendance Notification",
                        'body' => "Clock in successful",
                        'fcm_token' => $fcmToken,
                    ];
                    try {
                        Helper::sendNotification($notificationData); // Call the helper function
                    } catch (\Exception $e) {
                        \Log::error("Notification Error: " . $e->getMessage());
                    }
                }
                
                $action = 'Clock in';
                $attendanceId = $attendance->id;
                $message = 'Clock in successful';
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => $action . ' successful',
                'data' => [
                    'status' => true,
                    'message' => $message,
                    'action' => $action,
                    'date' => $requestDate,
                    'time' => date("h:i:s A", strtotime($requestTime)),
                    'attendance_id' => $attendanceId,
                    'employee_name' => $employee->name,
                    'employee_id' => $employee->id
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    /**
     * Mark attendance using facial recognition
     */
    /*public function markAttendanceFromKiosk(Request $request)
    {
        // Start timing for performance measurement
        $startTime = microtime(true);
        
        try {
            // Validate request - moved validation to separate method
            
            $validationResult = $this->validateRequest($request);
            if ($validationResult !== true) {
                return $validationResult;
            }
    
            // Process the captured image - moved to separate method
            try {
                $capturedBase64Jpeg = $this->processKioskImage($request->image);
            } catch (\Exception $e) {
                // Use base controller's error response method with correct parameter order
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image format',
                    'data' => ['message' => $e->getMessage()]
                ], 200);
            }
    
            // Initialize variables for face matching
            $matchResult = $this->findBestFaceMatch($capturedBase64Jpeg, $request->input('department_id'));
            
            // Handle no match case
            if (!$matchResult['success']) {
                $totalTime = $this->getElapsedTime($startTime);
                // Use base controller's error response method with correct parameter order
                return response()->json([
                    'success' => false,
                    'message' => 'No matching employee found',
                    'data' => [
                        'highest_confidence' => $matchResult['confidence'],
                        'threshold' => $this->confidenceThreshold,
                        'message' => 'Face verification failed!',
                        'processing_time_ms' => $totalTime
                    ]
                ], 200);
            }
            
            // Record attendance
            $attendanceResult = $this->recordAttendance(
                $matchResult['employee_id']
                // $matchResult['confidence']
            );
            
            // Add timing data
            $attendanceResult['data']['processing_time_ms'] = $this->getElapsedTime($startTime);
            
            Log::info("Total attendance processing time: {$attendanceResult['data']['processing_time_ms']}ms");
            return response()->json($attendanceResult, 200);
            
        } catch (\Exception $e) {
            Log::error('Face authentication error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            // Use base controller's error response method with correct parameter order
            return response()->json([
                'success' => false,
                'message' => 'Authentication error',
                'data' => ['message' => $e->getMessage()]
            ], 200);
        }
    }*/
    
    /**
     * Validate the incoming request
     */
    /*private function validateRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string'
        ]);

        if ($validator->fails()) {
            // Use base controller's error response method with correct parameter order
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'data' => ['message' => $e->getMessage()]
            ], 200);
        }
        
        return true;
    }*/
    
    /**
     * Record attendance for matched employee
     */
    // private function recordAttendance($employeeId, $confidenceScore)
    /*private function recordAttendance($employeeId)
    {
        // Get employee details - use cached data if available
        $employee = $this->getEmployeeDetails($employeeId);
        if (!$employee) {
            throw new \Exception("Employee with ID {$employeeId} not found");
        }
        
        // Get current date and time
        $date = Carbon::now()->format('Y-m-d');
        $time = Carbon::now()->format('H:i:s');
        
        // Check if employee already clocked in
        $existingAttendance = $this->getExistingAttendance($employeeId, $date);
        
        // Start transaction
        DB::beginTransaction();
        try {
            if ($existingAttendance) {
                // Clock out
                $existingAttendance->clock_out = $time;
                $existingAttendance->save();
                
                $action = 'Clock out';
                $attendanceId = $existingAttendance->id;
            } else {
                // Clock in
                $attendance = new AttendanceEmployee();
                $attendance->employee_name = $employee->name;
                $attendance->employee_id = $employee->id;
                $attendance->date = $date;
                $attendance->status = "Present";
                $attendance->clock_in = $time;
                $attendance->save();
                
                // Update recent employees cache
                $this->addToRecentEmployees($employee->id);
                
                $action = 'Clock in';
                $attendanceId = $attendance->id;
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => $action . ' successful',
                'data' => [
                    'status' => true,
                    'message' => $action . ' successfully',
                    'time' => date("h:i:s A", strtotime($time)),
                    'attendance_id' => $attendanceId,
                    // 'confidence' => $confidenceScore,
                    'employee_name' => $employee->name,
                    'employee_id' => $employee->id
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }*/
    
    /**
     * Find the best matching face among employees
     */
    private function findBestFaceMatch($capturedBase64Jpeg, $departmentId = null)
    {
        // Initialize variables to track best match
        $highestConfidence = 0;
        $bestMatchEmployeeId = null;
        
        // First try to match with recent employees (cached)
        $matchResult = $this->matchWithRecentEmployees($capturedBase64Jpeg);
        if ($matchResult['success']) {
            return $matchResult;
        }
        
        // If no match found, try with all other employees
        return $this->matchWithAllEmployees($capturedBase64Jpeg, $departmentId, $matchResult['confidence']);
    }
    
    /**
     * Try to match with employees who recently clocked in/out
     */
    private function matchWithRecentEmployees($capturedBase64Jpeg)
    {
        $todayDate = Carbon::now()->format('Y-m-d');
        $recentEmployeeIds = $this->getRecentEmployeeIds($todayDate);
        
        if (empty($recentEmployeeIds)) {
            return ['success' => false, 'confidence' => 0];
        }
        
        $highestConfidence = 0;
        $bestMatchEmployeeId = null;
        
        // Get batch of recent employees
        $recentEmployees = AadhaarDetail::select('id', 'employee_id', 'photo_encoded_optimized', 'photo_encoded')
            ->whereIn('employee_id', $recentEmployeeIds)
            ->whereNotNull('photo_encoded')
            ->where('is_active', 1)
            ->get();
            
        foreach ($recentEmployees as $aadhaar) {
            try {
                // Compare faces
                $result = $this->compareEmployeeFace($aadhaar, $capturedBase64Jpeg);
                
                // If high confidence match found, return immediately
                if ($result['success'] && $result['confidence'] >= $this->confidenceThreshold) {
                    Log::info("Found high confidence match in recent employees: {$result['confidence']}% for ID {$aadhaar->employee_id}");
                    return [
                        'success' => true, 
                        'confidence' => $result['confidence'],
                        'employee_id' => $aadhaar->employee_id
                    ];
                }
                
                // Track highest confidence match
                if ($result['success'] && $result['confidence'] > $highestConfidence) {
                    $highestConfidence = $result['confidence'];
                    $bestMatchEmployeeId = $aadhaar->employee_id;
                }
            } catch (\Exception $e) {
                Log::warning("Error comparing recent employee {$aadhaar->employee_id}: {$e->getMessage()}");
                continue;
            }
        }
        
        // If we found a match above threshold
        if ($bestMatchEmployeeId && $highestConfidence >= $this->confidenceThreshold) {
            return [
                'success' => true, 
                'confidence' => $highestConfidence,
                'employee_id' => $bestMatchEmployeeId
            ];
        }
        
        // No match found in recent employees
        return [
            'success' => false, 
            'confidence' => $highestConfidence,
            'employee_id' => $bestMatchEmployeeId
        ];
    }
    
    /**
     * Match with all employees if recent employee matching failed
     */
    private function matchWithAllEmployees($capturedBase64Jpeg, $departmentId, $currentHighestConfidence)
    {
        $highestConfidence = $currentHighestConfidence;
        $bestMatchEmployeeId = null;
        $todayDate = Carbon::now()->format('Y-m-d');
        $recentEmployeeIds = $this->getRecentEmployeeIds($todayDate);
        
        // Build query for remaining employees
        $query = AadhaarDetail::select('id', 'employee_id', 'photo_encoded_optimized', 'photo_encoded')
            ->whereNotNull('photo_encoded')
            ->where('is_active', 1);
            
        // Skip already checked recent employees
        if (!empty($recentEmployeeIds)) {
            $query->whereNotIn('employee_id', $recentEmployeeIds);
        }
        
        // Add department filter if provided
        if ($departmentId) {
            $query->whereHas('employee', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }
        
        // Total employees for logging
        $totalEmployees = $query->count();
        Log::info("Searching among {$totalEmployees} additional employees");
        
        // Use a reference variable to allow updating from closure
        $bestMatch = [
            'found' => false,
            'confidence' => $highestConfidence,
            'employee_id' => null
        ];
        
        // Process in smaller chunks to avoid memory issues
        $query->orderBy('id')
            ->chunk(10, function($aadhaarDetails) use ($capturedBase64Jpeg, &$bestMatch) {
                // If we already found a match in a previous chunk, skip processing
                if ($bestMatch['found']) {
                    return false;
                }
                
                foreach ($aadhaarDetails as $aadhaar) {
                    try {
                        // Compare faces
                        $result = $this->compareEmployeeFace($aadhaar, $capturedBase64Jpeg);
                        
                        // If high confidence match, return immediately
                        if ($result['success'] && $result['confidence'] >= $this->confidenceThreshold) {
                            $bestMatch = [
                                'found' => true,
                                'confidence' => $result['confidence'],
                                'employee_id' => $aadhaar->employee_id
                            ];
                            Log::info("Found match above threshold: {$result['confidence']}% for ID {$aadhaar->employee_id}");
                            return false; // Break the chunk loop
                        }
                        
                        // Update best match if better than current
                        if ($result['success'] && $result['confidence'] > $bestMatch['confidence']) {
                            $bestMatch['confidence'] = $result['confidence'];
                            $bestMatch['employee_id'] = $aadhaar->employee_id;
                        }
                    } catch (\Exception $e) {
                        Log::warning("Error comparing employee {$aadhaar->employee_id}: {$e->getMessage()}");
                        continue;
                    }
                }
            });
        
        // Check if we found a match with sufficient confidence
        if ($bestMatch['found'] || ($bestMatch['employee_id'] && $bestMatch['confidence'] >= $this->confidenceThreshold)) {
            return [
                'success' => true,
                'confidence' => $bestMatch['confidence'],
                'employee_id' => $bestMatch['employee_id']
            ];
        }
        
        // No match found
        return [
            'success' => false,
            'confidence' => $bestMatch['confidence'],
            'employee_id' => $bestMatch['employee_id']
        ];
    }
    
    
    /**
     * Process the image from kiosk
     */
    private function processKioskImage($imageData)
    {
        $base64Image = $this->getBase64($imageData);
        $capturedImage = $this->processBase64Image($base64Image);
        return base64_encode($capturedImage->toJpeg()->toString());
    }
    
    /**
     * Compare employee face with captured image
     */
    private function compareEmployeeFace($aadhaar, $capturedBase64Jpeg)
    {
        // Process employee photo - use optimized version if available
        $employeePhotoEncoded = $aadhaar->photo_encoded_optimized ?? $aadhaar->photo_encoded;
        
        // Get processed employee photo - from cache if available
        $employeePhotoBase64Jpeg = $this->getProcessedEmployeePhoto($aadhaar->employee_id, $employeePhotoEncoded);
        
        // Compare faces using Face++ API
        return $this->compareFaces($capturedBase64Jpeg, $employeePhotoBase64Jpeg);
    }
    
    /**
     * Get processed employee photo (from cache if possible)
     */
    private function getProcessedEmployeePhoto($employeeId, $photoEncoded)
    {
        $cacheKey = 'employee_face_' . $employeeId;
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($photoEncoded) {
            $processedImage = $this->processBase64Image($photoEncoded);
            return base64_encode($processedImage->toJpeg()->toString());
        });
    }
    
    /**
     * Get list of employees who clocked in today
     */
    private function getRecentEmployeeIds($date)
    {
        $cacheKey = 'recent_employees_' . $date;
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($date) {
            return AttendanceEmployee::where('date', $date)
                ->pluck('employee_id')
                ->toArray();
        });
    }
    
    /**
     * Add employee to today's recent list
     */
    private function addToRecentEmployees($employeeId)
    {
        $todayDate = Carbon::now()->format('Y-m-d');
        $cacheKey = 'recent_employees_' . $todayDate;
        
        $recentEmployeeIds = Cache::get($cacheKey, []);
        if (!in_array($employeeId, $recentEmployeeIds)) {
            $recentEmployeeIds[] = $employeeId;
            Cache::put($cacheKey, $recentEmployeeIds, $this->cacheDuration);
        }
    }
    
    /**
     * Get employee details (cached)
     */
    private function getEmployeeDetails($employeeId)
    {
        $cacheKey = 'employee_details_' . $employeeId;
        
        return Cache::remember($cacheKey, $this->cacheDuration, function() use ($employeeId) {
            return Employee::select('id', 'user_id', 'name', 'department_id')
                ->where('id', $employeeId)
                ->first();
        });
    }
    
    /**
     * Get existing attendance record for today
     */
    private function getExistingAttendance($employeeId, $date)
    {
        return AttendanceEmployee::select('id', 'clock_in', 'clock_out')
            ->where('employee_id', $employeeId)
            ->where('date', $date)
            ->whereNotNull('clock_in')
            ->where(function ($query) {
                $query->whereNull('clock_out')
                    ->orWhere('clock_out', '00:00:00');
            })
            ->first();
    }
    
    /**
     * Calculate elapsed time in milliseconds
     */
    private function getElapsedTime($startTime)
    {
        return round((microtime(true) - $startTime) * 1000);
    }
    
    /**
     * Process base64 image string to get clean base64 data
     */
    private function getBase64($image_code)
    {
        // Check if the image already has the base64 prefix
        if (strpos($image_code, ';base64,') !== false) {
            // If it has the prefix, split and return the actual base64 content
            $image_parts = explode(";base64,", $image_code);
            if (count($image_parts) !== 2) {
                throw new \Exception('Invalid Base64 string format.');
            }
            return $image_parts[1];
        } else {
            // If it's already a raw base64 string without prefix, validate and return it
            if (base64_encode(base64_decode($image_code, true)) === $image_code) {
                return $image_code;
            } else {
                // If it's neither format, add proper format and try again
                $formattedImage = 'data:image/jpeg;base64,' . $image_code;
                return $this->getBase64($formattedImage);
            }
        }
    }
    
    /**
     * Process base64 image to standardized format
     */
    private function processBase64Image($base64String)
    {
        try {
            // Create new manager instance with GD driver
            $manager = new ImageManager(new Driver());
    
            // Process the image
            $image = $manager->read($base64String);
    
            // Fit the image to standard size
            $image->cover($this->imageSize, $this->imageSize);
    
            return $image;
        } catch (\Exception $e) {
            throw new \Exception('Failed to process image: ' . $e->getMessage());
        }
    }
    
    /**
     * Compare two face images using Face++ API
     */
    private function compareFaces($image1Base64, $image2Base64)
    {
        try {
            // Try to get from cache first to reduce API calls
            $cacheKey = 'face_compare_' . md5($image1Base64 . '_' . $image2Base64);
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            
            $response = Http::timeout(5)->asForm()->post($this->compareUrl, [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
                'image_base64_1' => $image1Base64,
                'image_base64_2' => $image2Base64,
            ]);
    
            $result = $response->json();
    
            if (isset($result['error_message'])) {
                throw new \Exception($result['error_message']);
            }
    
            $confidence = $result['confidence'] ?? 0;
            
            $resultData = [
                'success' => true,
                'confidence' => $confidence
            ];
            
            // Cache the result for 1 hour to reduce API calls
            Cache::put($cacheKey, $resultData, 60);
            
            return $resultData;
    
        } catch (\Exception $e) {
            Log::warning("Face comparison failed: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Face comparison failed: ' . $e->getMessage(),
                'confidence' => 0
            ];
        }
    }
}