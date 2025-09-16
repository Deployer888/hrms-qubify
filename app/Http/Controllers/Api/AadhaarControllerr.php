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


    public function send_otp(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'aadhar_number' => 'required|digits:12',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        try {
            $aadhar_detail = AadhaarDetail::where('aadhar_number', $request->input('aadhar_number'))->first();

            if($aadhar_detail){
                $aadhar_detail->aadhar_number = $this->maskAadhar($aadhar_detail->aadhar_number);
            }

            // $response = $this->uidaiService->sendOtp($request->input('aadhar_number'));
            // Session::put('uidaiService', $response);

            return redirect()->route('aadhaar.verify.otp');
            return view('aadhaar.verify');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function maskAadhar($aadharNumber, $visibleDigits = 4)
    {
        $maskedLength = strlen($aadharNumber) - $visibleDigits;
        return str_repeat('X', $maskedLength) . substr($aadharNumber, -$visibleDigits);
    }

    public function verify_otp()
    {
        return view('aadhaar.verify');
    }

    public function verify_otp_post(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $aadhhaarAccess = Session::get('uidaiService');
        $response = $this->uidaiService->verifyOtp($aadhhaarAccess['res']['data']['reference_id'],$request->input('otp'),$aadhhaarAccess['access_token']);
        dd($response);


    }

    /**
     * @OA\Post(
     *     path="/api/aadhaar-detail",
     *     summary="Add or update Aadhaar details for an employee",
     *     operationId="addAadhaarDetail",
     *     tags={"Aadhaar"},
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
     *             required={"employee_id","name","aadhar_number","gender","date_of_birth","year_of_birth","mobile_hash","email_hash","care_of","full_address","house","street","landmark","vtc","subdistrict","district","state","country","pincode","photo_encoded"},
     *             @OA\Property(property="employee_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="aadhar_number", type="string", example="123456789012"),
     *             @OA\Property(property="gender", type="string", example="Male"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="year_of_birth", type="integer", example=1990),
     *             @OA\Property(property="mobile_hash", type="string", example="abc123hash"),
     *             @OA\Property(property="email_hash", type="string", example="emailhash123"),
     *             @OA\Property(property="care_of", type="string", example="Father Name"),
     *             @OA\Property(property="full_address", type="string", example="123 Full Street, City"),
     *             @OA\Property(property="house", type="string", example="H-123"),
     *             @OA\Property(property="street", type="string", example="Main Street"),
     *             @OA\Property(property="landmark", type="string", example="Near Park"),
     *             @OA\Property(property="vtc", type="string", example="Village Name"),
     *             @OA\Property(property="subdistrict", type="string", example="Subdistrict Name"),
     *             @OA\Property(property="district", type="string", example="District Name"),
     *             @OA\Property(property="state", type="string", example="State Name"),
     *             @OA\Property(property="country", type="string", example="India"),
     *             @OA\Property(property="pincode", type="string", example="123456"),
     *             @OA\Property(property="photo_encoded", type="string", example="base64imagestring==")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Vistor stored.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */


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
