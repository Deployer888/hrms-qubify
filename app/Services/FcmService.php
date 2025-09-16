<?php

namespace App\Services;

use Google\Auth\OAuth2;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class FcmService
{
    private $projectId;
    private $clientEmail;
    private $privateKey;

    public function __construct()
    {
        // Load FCM configuration from environment or config
        $this->projectId = env('FCM_PROJECT_ID');
        $this->clientEmail = env('FCM_CLIENT_EMAIL');
        
        // Load service account JSON from storage
        $this->loadServiceAccountCredentials();
    }

    /**
     * Load service account credentials from JSON file
     */
    private function loadServiceAccountCredentials()
    {
        try {
            $jsonPath = storage_path('app/hrms-88b49-firebase-adminsdk-fbsvc-b046e8ca76.json');
            
            // Check if file exists
            if (!file_exists($jsonPath)) {
                throw new Exception('FCM service account JSON file not found at: ' . $jsonPath);
            }
            
            // Read and decode JSON file
            $jsonContent = file_get_contents($jsonPath);
            $jsonKey = json_decode($jsonContent, true);
            
            if (!$jsonKey) {
                throw new Exception('Failed to parse FCM service account JSON');
            }
            // dd($jsonKey);
            $this->privateKey = $jsonKey['private_key'];
            
            // Override with values from JSON if not set in env
            if (!$this->projectId) {
                $this->projectId = $jsonKey['project_id'];
            }
            
            if (!$this->clientEmail) {
                $this->clientEmail = $jsonKey['client_email'];
            }
            
        } catch (Exception $e) {
            Log::error('FCM Service Account Error: ' . $e->getMessage());
            throw new Exception('FCM service account configuration error: ' . $e->getMessage());
        }
    }

    /**
     * Get OAuth2 access token for FCM V1 API
     */
    private function getAccessToken()
    {
        try {
            // Validate required credentials
            if (empty($this->clientEmail)) {
                throw new Exception('Client email is missing');
            }
            
            if (empty($this->privateKey)) {
                throw new Exception('Private key is missing');
            }
            
            // Create OAuth2 configuration
            $oauth = new OAuth2([
                'audience' => 'https://oauth2.googleapis.com/token',
                'issuer' => $this->clientEmail,
                'signingAlgorithm' => 'RS256',
                'signingKey' => $this->privateKey,
                'tokenCredentialUri' => 'https://oauth2.googleapis.com/token',
                'scope' => ['https://www.googleapis.com/auth/firebase.messaging'],
                'sub' => null, // Important: don't set subject for service accounts
            ]);
    
            Log::info('Attempting to fetch auth token', [
                'issuer' => $this->clientEmail,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging'
            ]);
    
            $authToken = $oauth->fetchAuthToken();
            // dd($authToken);
            if (!isset($authToken['access_token'])) {
                Log::error('No access token in response', ['response' => $authToken]);
                throw new Exception('Failed to get access token from response');
            }
            
            Log::info('Access token obtained successfully');
            return $authToken['access_token'];
            
        } catch (Exception $e) {
            Log::error('FCM OAuth Error', [
                'message' => $e->getMessage(),
                'client_email' => $this->clientEmail,
                'private_key_start' => substr($this->privateKey, 0, 50) . '...'
            ]);
            throw new Exception('Failed to get FCM access token: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to a single device
     */
    public function sendToDevice($deviceToken, $title, $body, $data = [])
    {
        try {
            $accessToken = $this->getAccessToken();
            $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
            // dd($accessToken);
            $payload = [
                'message' => [
                    'token' => $deviceToken,
                    'data' => [
                        'title' => $title,
                        'body' => $body,
                    ]
                ]
            ];
            // dd($accessToken);
            $response = Http::withToken($accessToken)
                ->post($fcmUrl, $payload);
                // dd($response);

            if ($response->successful()) {
                Log::info('FCM notification sent successfully', [
                    'device_token' => substr($deviceToken, 0, 20) . '...',
                    'title' => $title,
                    'response' => $response->json()
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Notification sent successfully',
                    'response' => $response->json()
                ];
            } else {
                Log::error('FCM notification failed', [
                    'device_token' => substr($deviceToken, 0, 20) . '...',
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to send notification',
                    'error' => $response->json()
                ];
            }

        } catch (Exception $e) {
            Log::error('FCM Send Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices($deviceTokens, $title, $body, $data = [])
    {
        $results = [];
        
        foreach ($deviceTokens as $token) {
            $results[] = $this->sendToDevice($token, $title, $body, $data);
            
            // Add small delay to avoid rate limiting
            usleep(100000); // 0.1 second delay
        }
        
        return $results;
    }

    /**
     * Send notification to a topic
     */
    public function sendToTopic($topic, $title, $body, $data = [])
    {
        try {
            $accessToken = $this->getAccessToken();
            $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            $payload = [
                'message' => [
                    'topic' => $topic,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'android' => [
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => 'high_importance_channel',
                        ],
                        'priority' => 'high',
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
                    ],
                    'data' => array_merge([
                        'title' => $title,
                        'body' => $body,
                    ], $data)
                ]
            ];

            $response = Http::withToken($accessToken)
                ->post($fcmUrl, $payload);

            if ($response->successful()) {
                Log::info('FCM topic notification sent successfully', [
                    'topic' => $topic,
                    'title' => $title
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Topic notification sent successfully',
                    'response' => $response->json()
                ];
            } else {
                Log::error('FCM topic notification failed', [
                    'topic' => $topic,
                    'response' => $response->json()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to send topic notification',
                    'error' => $response->json()
                ];
            }

        } catch (Exception $e) {
            Log::error('FCM Topic Send Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send data-only notification (silent notification)
     */
    public function sendDataOnly($deviceToken, $data = [])
    {
        try {
            $accessToken = $this->getAccessToken();
            $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            $payload = [
                'message' => [
                    'token' => $deviceToken,
                    'android' => [
                        'priority' => 'high',
                    ],
                    'apns' => [
                        'headers' => [
                            'apns-push-type' => 'background',
                            'apns-priority' => '5',
                        ],
                        'payload' => [
                            'aps' => [
                                'content-available' => 1,
                            ],
                        ],
                    ],
                    'data' => $data
                ]
            ];

            $response = Http::withToken($accessToken)
                ->post($fcmUrl, $payload);

            return $response->successful() ? 
                ['success' => true, 'response' => $response->json()] : 
                ['success' => false, 'error' => $response->json()];

        } catch (Exception $e) {
            Log::error('FCM Data-only Send Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage()
            ];
        }
    }
}