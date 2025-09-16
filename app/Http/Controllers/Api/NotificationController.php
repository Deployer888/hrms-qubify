<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Models\MobileNotification;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Htt;
use Auth;

class NotificationController extends BaseController
{
    private $fcmService;
    
    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Send notification to a single device
     */
    public function sendToDevice(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:500',
        ]);
        
        $result = $this->fcmService->sendToDevice(
            $request->device_token,
            $request->title,
            $request->body
        );
        $notification = MobileNotification::create([
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'body' => $request->body,
            'is_read' => false,
        ]);

        // dd($result);
        return response()->json($result);
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices(Request $request)
    {
        $request->validate([
            'device_tokens' => 'required|array',
            'device_tokens.*' => 'string',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:500',
        ]);

        $results = $this->fcmService->sendToMultipleDevices(
            $request->device_tokens,
            $request->title,
            $request->body
        );

        return response()->json([
            'success' => true,
            'results' => $results,
            'total_sent' => count($results),
            // 'successful' => count(array_filter($results, fn($r) => $r['success']))
        ]);
    }

    /**
     * Send notification to a topic
     */
    public function sendToTopic(Request $request)
    {
        $request->validate([
            'topic' => 'required|string',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:500',
        ]);

        $result = $this->fcmService->sendToTopic(
            $request->topic,
            $request->title,
            $request->body
        );

        return response()->json($result);
    }

    public function index(Request $request)
    {
        $notifications = auth()->user()->mobileNotifications()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => auth()->user()->unreadNotificationsCount()
        ]);
    }
    
    public function unread()
    {
        $notifications = auth()->user()->mobileNotifications()
            ->unread()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'count' => $notifications->count()
        ]);
    }
    
    public function markAsRead(MobileNotification $notification)
    {
        // Ensure user owns this notification
        if ($notification->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
    
    public function markAllAsRead()
    {
        auth()->user()->mobileNotifications()
            ->unread()
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy(MobileNotification $notification)
    {
        // Ensure user owns this notification
        if ($notification->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }
}