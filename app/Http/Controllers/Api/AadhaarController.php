<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\AadhaarDetail;
use App\Services\UIDAIService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class AadhaarController extends BaseController
{
    protected $uidaiService;

    public function __construct(UIDAIService $uidaiService)
    {
        $this->uidaiService = $uidaiService;
    }


    /**
     * @OA\Post(
     *     path="/api/send-otp",
     *     summary="Send OTP for Aadhar Verification",
     *     description="Sends OTP to verify Aadhar number for employee verification",
     *     operationId="sendOtpForAadhar",
     *     tags={"Aadhar Verification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"aadhar_number", "employee_id"},
     *             @OA\Property(property="aadhar_number", type="string", example="123456789012", description="12-digit Aadhar number"),
     *             @OA\Property(property="employee_id", type="integer", example=1, description="Employee ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully or Aadhar already verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP sent successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="is_already_verified", type="boolean", example=false),
     *                 @OA\Property(property="reference_id", type="string", example="ref123456"),
     *                 @OA\Property(property="aadhar_number", type="string", example="123456789012"),
     *                 @OA\Property(property="employee_id", type="integer", example=1)
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
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to send OTP")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function send_otp(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'aadhar_number' => 'required|digits:12',
                'employee_id'   => 'required|exists:employees,id',
            ]);
           
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            // Check if Aadhar is already verified
            $empAadharCheck = AadhaarDetail::where('aadhaar_number', $request->input('aadhar_number'))->first();
    
            if ($empAadharCheck) {
                return response()->json([
                    'success' => true,
                    'message' => 'Aadhar already verified!',
                    'data' => [
                        'is_already_verified' => true,
                        'aadhar_number' => $empAadharCheck->aadhaar_number,
                        'employee_id' => $request->input('employee_id')
                    ]
                ], 200);
            }
    
            // Send OTP using UIDAI service
            $response = $this->uidaiService->sendOtp($request->input('aadhar_number'));
    
            // Return session data in response for frontend to store
            return response()->json([
                'success' => true,
                'message' => $response['data']['message'] ?? 'OTP sent successfully',
                'data' => [
                    'is_already_verified' => false,
                    'reference_id' => $response['data']['reference_id'],
                    'aadhar_number' => $request->input('aadhar_number'),
                    'employee_id' => $request->input('employee_id')
                ]
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
     *     path="/api/verify-otp",
     *     summary="Verify OTP for Aadhar Authentication",
     *     description="Verifies the OTP and creates Aadhar detail record with complete user information",
     *     operationId="verifyOtpPost",
     *     tags={"Aadhar Verification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"otp", "reference_id", "aadhar_number", "employee_id"},
     *             @OA\Property(property="otp", type="string", example="123456", description="6-digit OTP"),
     *             @OA\Property(property="reference_id", type="string", example="ref123456", description="Reference ID from send OTP response"),
     *             @OA\Property(property="aadhar_number", type="string", example="123456789012", description="12-digit Aadhar number"),
     *             @OA\Property(property="employee_id", type="integer", example=1, description="Employee ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully and Aadhar details saved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Aadhar verified successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="aadhar_detail_id", type="integer", example=1),
     *                 @OA\Property(property="employee_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="aadhaar_number", type="string", example="123456789012"),
     *                 @OA\Property(property="gender", type="string", example="M"),
     *                 @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
     *                 @OA\Property(property="mobile_hash", type="string"),
     *                 @OA\Property(property="email_hash", type="string"),
     *                 @OA\Property(property="full_address", type="string"),
     *                 @OA\Property(property="address", type="object",
     *                     @OA\Property(property="house", type="string"),
     *                     @OA\Property(property="street", type="string"),
     *                     @OA\Property(property="district", type="string"),
     *                     @OA\Property(property="state", type="string"),
     *                     @OA\Property(property="pincode", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error or Invalid OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid OTP"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to verify OTP")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function verify_otp_post(Request $request)
    {
        try {
            // Validate request data
            $validator = \Validator::make($request->all(), [
                'otp' => 'required|digits:6',
                'reference_id' => 'required|string',
                'aadhar_number' => 'required|digits:12',
                'employee_id' => 'required|exists:employees,id',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            // Verify OTP using UIDAI service
            $response = $this->uidaiService->verifyOtp($request->input('reference_id'), $request->input('otp'));
    
            if ($response['code'] == 200) {
                $userData = $response['data'];
    
                // Prepare data for Aadhar detail creation
                $data = [
                    'employee_id' => $request->input('employee_id'),
                    'name' => $userData['name'],
                    'aadhaar_number' => $request->input('aadhar_number'),
                    'gender' => $userData['gender'],
                    'date_of_birth' => isset($userData['date_of_birth']) ? date('Y-m-d', strtotime($userData['date_of_birth'])) : null,
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
                    'verified_at' => now(),
                ];
    
                // Create Aadhar detail record
                $aadhaar_detail = AadhaarDetail::create($data);
    
                // Prepare response data
                $responseData = [
                    'aadhar_detail_id' => $aadhaar_detail->id,
                    'employee_id' => $aadhaar_detail->employee_id,
                    'name' => $aadhaar_detail->name,
                    'aadhaar_number' => $aadhaar_detail->aadhaar_number,
                    'gender' => $aadhaar_detail->gender,
                    'date_of_birth' => $aadhaar_detail->date_of_birth,
                    'year_of_birth' => $aadhaar_detail->year_of_birth,
                    'mobile_hash' => $aadhaar_detail->mobile_hash,
                    'email_hash' => $aadhaar_detail->email_hash,
                    'care_of' => $aadhaar_detail->care_of,
                    'full_address' => $aadhaar_detail->full_address,
                    'address' => [
                        'house' => $aadhaar_detail->house,
                        'street' => $aadhaar_detail->street,
                        'landmark' => $aadhaar_detail->landmark,
                        'vtc' => $aadhaar_detail->vtc,
                        'subdistrict' => $aadhaar_detail->subdistrict,
                        'district' => $aadhaar_detail->district,
                        'state' => $aadhaar_detail->state,
                        'country' => $aadhaar_detail->country,
                        'pincode' => $aadhaar_detail->pincode,
                    ],
                    'share_code' => $aadhaar_detail->share_code,
                    'verified_at' => $aadhaar_detail->verified_at,
                    'has_photo' => !empty($aadhaar_detail->photo_encoded)
                ];
    
                return response()->json([
                    'success' => true,
                    'message' => 'Aadhar verified successfully',
                    'data' => $responseData
                ], 200);
    
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP'
                ], 422);
            }
    
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
     public function addhaar_detail(Request $request)
    {
        $rules = [
            'employee_id' => 'required|integer|exists:employees,id',
            'name' => 'required|string|max:255',
            'aadhaar_number' => 'required|digits:12',
            'gender' => 'required|string|max:10',
            'date_of_birth' => 'required|date',
            'year_of_birth' => 'required|integer',
            'mobile_hash' => 'required|string|max:255',
            'email_hash' => 'required|string|max:255',
            'care_of' => 'required|string|max:255',
            'full_address' => 'required|string',
            'house' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'landmark' => 'required|string|max:255',
            'vtc' => 'required|string|max:255',
            'subdistrict' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'pincode' => 'required|string|max:20',
            'photo_encoded' => 'required|string', // Base64 image string
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error.', 200, $validator->errors()->toArray());
        }
        DB::beginTransaction();
        try {
            $aadhaar_detail = AadhaarDetail::where('aadhaar_number', $request->input('aadhaar_number'))->first();

            $data = [
                'employee_id' => $request->input('employee_id'),
                'name' => $request->input('name'),
                'aadhaar_number' => $request->input('aadhaar_number'),
                'gender' => $request->input('gender'),
                'date_of_birth' => $request->input('date_of_birth') ? date('Y-m-d', strtotime($request->input('date_of_birth'))) : null,
                'year_of_birth' => $request->input('year_of_birth'),
                'mobile_hash' => $request->input('mobile_hash'),
                'email_hash' => $request->input('email_hash'),
                'care_of' => $request->input('care_of'),
                'full_address' => $request->input('full_address'),
                'house' => $request->input('house'),
                'street' => $request->input('street'),
                'landmark' => $request->input('landmark'),
                'vtc' => $request->input('vtc'),
                'subdistrict' => $request->input('subdistrict'),
                'district' => $request->input('district'),
                'state' => $request->input('state'),
                'country' => $request->input('country'),
                'pincode' => $request->input('pincode'),
                'photo_encoded' => $request->input('photo_encoded'),
            ];

            if (!$aadhaar_detail) {
                $aadhaar_detail = AadhaarDetail::create($data);
            } else {
                $aadhaar_detail->update($data);
            }


            DB::commit();
            return $this->successResponse(['data' => $aadhaar_detail, 'message' => 'Vistor stored.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }

    }
}
