<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Exception;
use Illuminate\Support\Facades\Log;

class UIDAIService
{
    protected $authUrl;
    protected $otpUrl;
    protected $verifyUrl;
    protected $apiKey;
    protected $apiSecret;

    public function __construct()
    {
        $this->authUrl = "https://api.sandbox.co.in/authenticate"; // Authentication URL
        $this->otpUrl = "https://api.sandbox.co.in/kyc/aadhaar/okyc/otp";
        $this->verifyUrl = "https://api.sandbox.co.in/kyc/aadhaar/okyc/otp/verify";
        $this->apiKey = "key_live_jqvfnNHcVWPVGWzMiVYUMedEWaLJz25o"; // API Key
        $this->apiSecret = "secret_live_pHrjujibWahVkwlrPUVNkKdBczi7d8sE"; // API Secret
        // $this->apiKey = "key_test_3VYMbjHYaGzc3wtFAwAILkBCNdzcI0ut"; // API Key
        // $this->apiSecret = "secret_test_Sl6gcBzWM1nnlQxXQpGEEVtcER8JntFr"; // API Secret
    }

    /**
     * Authenticate Aadhaar using Sandbox API
     *
     * @param string $aadharNumber
     * @return array
     * @throws \Exception
     */
    public function authenticateAadhar($aadharNumber)
    {
        // Validate Aadhaar number format
        if (!preg_match('/^\d{12}$/', $aadharNumber)) {
            throw new \InvalidArgumentException('Invalid Aadhaar number format');
        }

        // Send POST request to the API
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'x-api-key' => $this->apiKey,
            'x-api-secret' => $this->apiSecret,
            'x-api-version' => '1',
        ])->post($this->authUrl, [
            'uid' => $aadharNumber, // Include the Aadhaar number in the request body if required
        ]);

        // Check for errors
        if ($response->failed()) {
            throw new \Exception('Failed to authenticate Aadhaar: ' . $response->body());
        }

        // Return the response as an array
        return $response->json();
    }

    public function sendOtp($aadharNumber)
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'x-api-key' => $this->apiKey,
            'x-api-secret' => $this->apiSecret,
            'x-api-version' => '1',
        ])->post($this->authUrl, [
            'uid' => $aadharNumber, // Include the Aadhaar number in the request body if required
        ]);

        Session::put('accessToken', $response->json()['access_token']);
        Session::put('aanb', $aadharNumber);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => Session::get('accessToken'),
            'x-api-key' => $this->apiKey,
            'x-api-secret' => $this->apiSecret,
            'x-api-version' => '2.0',
            'content-type' => 'application/json',
        ])->post($this->otpUrl, [
            '@entity' => 'in.co.sandbox.kyc.aadhaar.okyc.otp.request',
            'consent' => 'Y',
            'reason' => 'KYC',
            'aadhaar_number' => $aadharNumber,
        ]);

        if ($response->failed()) {
            throw new \Exception($response->json('message') ?? 'Failed to send OTP');
        }

        return $response->json();
    }

    public function verifyOtp($referenceId, $otp)
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => Session::get('accessToken'),
            'x-api-key' => $this->apiKey,
            'x-api-version' => '2.0',
            'content-type' => 'application/json',
        ])->post($this->verifyUrl, [
            '@entity' => 'in.co.sandbox.kyc.aadhaar.okyc.request',
            'reference_id' => (string) $referenceId,
            'otp' => (string) $otp,
        ]);

        if ($response->failed()) {
            throw new \Exception($response->json('message') ?? 'Failed to verify OTP');
        }

        return $response->json();
    }
}
