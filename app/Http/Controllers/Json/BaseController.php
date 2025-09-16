<?php

namespace App\Http\Controllers\Json;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class BaseController extends Controller
{
    
    public function successResponse($data = null, $message = 'Success', $status = Response::HTTP_OK)
    {
        // Prepare the response structure
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];

        // Return the JSON response with the appropriate HTTP status code
        return response()->json($response, $status);
    }

    public function errorResponse($message = 'Error', $status = Response::HTTP_BAD_REQUEST, $data = null)
    {
        // Prepare the response structure
        $response = [
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ];

        // Return the JSON response with the appropriate HTTP status code
        return response()->json($response, $status);
    }
}
