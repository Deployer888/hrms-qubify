<?php

namespace App\Http\Controllers;

use SimpleXMLElement;
use App\Models\{AadhaarDetail, Employee};
use Illuminate\Http\Request;
use App\Services\UIDAIService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Exception;

class AadhaarController extends Controller
{
    protected $uidaiService;
    
    private $compareUrl = 'https://api-us.faceplusplus.com/facepp/v3/compare';
    private $apiKey = 'MiuU8GJhmm5TaSaDxgJ_bbHewGUmx79k';
    private $apiSecret = 'zW48QNkIv_0H5M5-AdBp5JGQm9aSDdMd';

    public function __construct(UIDAIService $uidaiService)
    {
        $this->uidaiService = $uidaiService;
    }
    
    public function index()
    {
        $verified_emp = AadhaarDetail::pluck('employee_id')->toArray();
        $emp_list = Employee::whereNotIn('id',$verified_emp)->where('is_active', 1)->pluck('name','id');
        return view('aadhaar.index', compact('emp_list'));
    }

    public function send_otp(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'aadhar_number' => 'required|digits:12',
            'employee_id'   => 'required|exists:employees,id',
        ]);
       
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        try {
            $empAadharCheck = AadhaarDetail::where('aadhaar_number', $request->input('aadhar_number'))->first();
 
            if($empAadharCheck){
                $empAadharCheck->aadhar_number = $empAadharCheck->aadhaar_number;
                $data = [
                        'success' => true,
                        'user' => true,
                    ];
                return back()->with('success', 'Aadhar already Verified!');
            }
 
            $response = $this->uidaiService->sendOtp($request->input('aadhar_number'));
            Session::put('aanb', $request->input('aadhar_number'));
            Session::put('employee_id', $request->input('employee_id'));
            Session::put('uidaiServiceResID', $response['data']['reference_id']);
 
            return view('aadhaar.verify')->with('success', $response['data']['message']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    public function faceAuthenticate()
    {
         $verified_emp = AadhaarDetail::whereNull('photo_encoded_optimized')
                                ->pluck('employee_id')
                                ->toArray();

// echo "<pre>";
// print_r($verified_emp);
// die;

        $emp_list = Employee::whereIn('id', $verified_emp)->where('is_active', 1)->pluck('name','id');
        return view('aadhaar.authenticate', compact('emp_list'));
    }
    
    public function authenticate(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|string',
                'employee_id' => 'required|exists:employees,id'
            ]);
    
            // Process the captured image
            $capturedImage = $this->processBase64Image($request->image);
            
            // Save the captured image to storage/captured
            $imageName = 'captured_' . time() . '.jpg';
            $imagePath = 'public/captured/' . $imageName;
            
            // Create a temporary file to save the image
            $tempFile = tempnam(sys_get_temp_dir(), 'img');
            $capturedImage->save($tempFile, 'jpg');
            $imageData = file_get_contents($tempFile);
            
            // Store the image in storage
            Storage::put($imagePath, $imageData);
            
            // Clean up temp file
            @unlink($tempFile);
    
            // Generate the correct public URL - make sure this is the correct format
            $publicPath = '/storage/public/captured/' . $imageName;
            
            // Encode image for face comparison
            $capturedBase64 = base64_encode($imageData);
    
            // Get the specific employee by ID
            $employee = AadhaarDetail::where('employee_id', $request->employee_id)->first();
            
            if (!$employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found'
                ]);
            }
            
            try {
                // Get stored photo of the employee
                if (empty($employee->photo_encoded)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'No reference photo found for this employee'
                    ]);
                }
    
                // Handle different types of encoded data
                if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $employee->photo_encoded)) {
                    // It's already a base64 string, use it directly
                    $storedBase64 = $employee->photo_encoded;
                } else {
                    // It's binary data, encode it
                    $storedBase64 = base64_encode($employee->photo_encoded);
                }
    
                // Log image sizes for debugging
                \Log::debug("Captured image size: " . strlen($capturedBase64));
                \Log::debug("Stored image size: " . strlen($storedBase64));
    
                // Compare faces
                $result = $this->compareFaces($capturedBase64, $storedBase64);
    
                if ($result['success']) {
                    $confidence = $result['confidence'];
                    
                    // If match percentage is above 80%, save the encoded image
                    if ($confidence >= 85) {
                        // Save the optimized encoded image to aadhaar_detail record
                        $employee->photo_encoded_optimized = $capturedBase64;
                        $employee->save();
                        
                        // Get employee details
                        $employeeDetails = Employee::find($request->employee_id);
                        
                        // Get department info
                        $departmentName = 'N/A';
                        if ($employeeDetails && !empty($employeeDetails->department_id)) {
                            $department = \App\Models\Department::find($employeeDetails->department_id);
                            if ($department) {
                                $departmentName = $department->name;
                            }
                        }
                        
                        // Create a proper photo URL for display - save and generate URL for Aadhaar photo
                        $photoUrl = null;
                        try {
                            // Generate a data URL from the stored aadhaar photo
                            $photoData = base64_decode($storedBase64);
                            $aadhaarPhotoName = 'aadhaar_' . time() . '.jpg';
                            Storage::put('public/aadhaar/' . $aadhaarPhotoName, $photoData);
                            $photoUrl = '/storage/public/aadhaar/' . $aadhaarPhotoName;
                        } catch (\Exception $e) {
                            \Log::error('Error creating photo URL: ' . $e->getMessage());
                            $photoUrl = null;
                        }
                        
                        // Mask Aadhaar number
                        $maskedAadhaar = $this->maskAadhar($employee->aadhaar_number);
                        
                        // Return success with employee data
                        return response()->json([
                            'status' => true,
                            'data' => [
                                'original' => [
                                    'time' => now()->format('H:i:s'),
                                    'message' => 'Authentication successful',
                                    'data' => [
                                        'name' => $employee->name,
                                        'employeeId' => $employee->employee_id,
                                        'department' => $departmentName,
                                        'maskedAadhaar' => $maskedAadhaar,
                                        'photo' => $photoUrl,
                                        'capturedPhoto' => $publicPath,
                                        'confidenceScore' => $confidence
                                    ]
                                ]
                            ]
                        ]);
                    } else {
                        // Match percentage is below threshold
                        return response()->json([
                            'status' => false,
                            'message' => 'Face verification confidence too low: ' . $confidence . '%'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Face comparison failed: ' . ($result['error'] ?? 'Unknown error')
                    ]);
                }
            } catch (Exception $e) {
                \Log::error('Face comparison error for employee ' . $employee->id . ': ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Error processing employee photo: ' . $e->getMessage()
                ]);
            }
    
        } catch (Exception $e) {
            \Log::error('Face authentication error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Authentication error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function compareFaces($image1Base64, $image2Base64)
    {
        try {
            // Log the first few characters of each base64 string for debugging
            \Log::debug("Image1 base64 prefix: " . substr($image1Base64, 0, 30));
            \Log::debug("Image2 base64 prefix: " . substr($image2Base64, 0, 30));
            
            $response = Http::asForm()->post($this->compareUrl, [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
                'image_base64_1' => $image1Base64,
                'image_base64_2' => $image2Base64,
            ]);

            $result = $response->json();
            
            \Log::debug("Face++ API response: " . json_encode($result));

            if (isset($result['error_message'])) {
                throw new Exception($result['error_message']);
            }

            $confidence = $result['confidence'] ?? 0;
            
            return [
                'success' => true,
                'confidence' => $confidence
            ];

        } catch (Exception $e) {
            \Log::error("Face comparison error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'confidence' => 0
            ];
        }
    }
    
    private function processBase64Image($base64String)
    {
        try {
            // Remove data URL prefix if present
            if (strpos($base64String, 'data:image') === 0) {
                $base64String = preg_replace('/^data:image\/\w+;base64,/', '', $base64String);
            }
            
            // Create new manager instance with GD driver
            $manager = new ImageManager(new Driver());

            // Process the image
            $image = $manager->read(base64_decode($base64String));

            // Fit the image to 224x224
            $image->cover(224, 224);

            return $image;
        } catch (\Exception $e) {
            throw new \Exception('Failed to process image: ' . $e->getMessage());
        }
    }

    /**
     * Mask Aadhaar Number (Show last 4 digits, replace others with 'X')
     */
    private function maskAadhar($aadharNumber, $visibleDigits = 4)
    {
        $maskedLength = strlen($aadharNumber) - $visibleDigits;
        return str_repeat('X', $maskedLength) . substr($aadharNumber, -$visibleDigits);
    }

    public function verify_otp_post(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);
 
        $reference_id = Session::get('uidaiServiceResID');
 
        $response = $this->uidaiService->verifyOtp($reference_id, $request->input('otp'));
 
        if ($response['code'] == 200) {
            $userData = $response['data'];
 
            $data = [
                'employee_id' => Session::get('employee_id'),
                'name' => $userData['name'],
                'aadhaar_number' => Session::get('aanb'),
                'gender' => $userData['gender'],
                'date_of_birth' => date('Y-m-d', strtotime($userData['date_of_birth'])) ?? null,
                'year_of_birth' => $userData['year_of_birth'] ?? null,
                'mobile_hash' => $userData['mobile_hash'] ?? null,
                'email_hash' => $userData['email_hash'] ?? null,
                'care_of' => $userData['care_of'] ?? null,
                'full_address' => $userData['full_address'],

                // Address fields
                'house' => $userData['address']['house'] ?? null,
                'street' => $userData['address']['street'] ?? null,
                'landmark' => $userData['address']['landmark'] ?? null,
                'vtc' => $userData['address']['vtc'] ?? null,
                'subdistrict' => $userData['address']['subdistrict'] ?? null,
                'district' => $userData['address']['district'] ?? null,
                'state' => $userData['address']['state'],
                'country' => $userData['address']['country'],
                'pincode' => $userData['address']['pincode'],
                'photo_encoded' => $userData['photo'] ?? null,
                'share_code' => $userData['share_code'] ?? null,
            ];

            $aadhaar_detail = AadhaarDetail::create($data);

            return redirect()->route('aadhaar.index')
                ->with('success', 'Aadhaar verified successfully.');
        }
        else
        {
            return redirect()->back()->with('error', 'Invalid OTP');
        }
    }

    public function show(){}
}