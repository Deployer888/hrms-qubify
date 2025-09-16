<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{
     /**
     * Send a success response.
     *
     * @param mixed $data The data to be returned in the response.
     * @param string $message A message describing the success.
     * @param int $statusCode The HTTP status code for the response (default: 200).
     * @return JsonResponse
     */
    protected function successResponse($data, string $message = '', int $statusCode = 200): JsonResponse
    {
        $response = [
            'status' => true,
            'message' => $message,
        ];
        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Send an error response.
     *
     * @param string $message A message describing the error.
     * @param int $statusCode The HTTP status code for the response (default: 400).
     * @param array $errors Additional error details (optional).
     * @return JsonResponse
     */
    protected function errorResponse(string $message = '', int $statusCode = 200, array $errors = []): JsonResponse
    {
        $response = [
            'status' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
    /**
     * Send an error response.
     *
     * @param string $message A message describing the error.
     * @param int $statusCode The HTTP status code for the response (default: 400).
     * @param array $errors Additional error details (optional).
     * @return JsonResponse
     */
    protected function errorDataResponse($data, string $message = '', int $statusCode = 200): JsonResponse
    {
        $response = [
            'status' => false,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }


        return response()->json($response, $statusCode);
    }
    
    /**
     * success response method.
     *
     * @param array $result
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
    
        return response()->json($response, 200);
    }
    
    /**
     * return error response.
     *
     * @param string $error
     * @param array $errorMessages
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
    
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
    
        return response()->json($response, $code);
    }
}
