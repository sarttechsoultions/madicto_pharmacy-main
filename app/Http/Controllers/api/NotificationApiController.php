<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\notification as noty;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class NotificationApiController extends Controller
{
    protected FirebaseNotificationService $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * API: Register or update device FCM token
     * POST /api/fcm/token
     * POST /api/save-fcm-token
     */
    public function registerToken(Request $request)
    {
        // Normalize aliases
        if (!$request->has('fcm_token')) {
            $alias = $request->input('token') ?? $request->input('device_token');
            if ($alias) {
                $request->merge(['fcm_token' => $alias]);
            }
        }

        if (!$request->has('device_type')) {
            $type = $request->input('type') ?? $request->input('platform');
            if ($type) {
                $request->merge(['device_type' => $type]);
            }
        }

        $validator = Validator::make($request->all(), [
            'fcm_token'   => 'required|string|min:10',
            'device_type' => 'nullable|string|in:android,ios,web,other,Android,iOS,Web,Other',
            'device_id'   => 'nullable|string|max:191',
            'device_name' => 'nullable|string|max:191',
            'app_version' => 'nullable|string|max:50',
            'user_id'     => 'nullable|integer|exists:users,id',
        ], [
            'fcm_token.required' => 'FCM Device Token is required.',
            'fcm_token.min'      => 'Invalid FCM Device Token length.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Determine user ID (Sanctum token takes precedence, fallback to input user_id)
        $userId = auth('sanctum')->id()
            ?? auth()->id()
            ?? ($request->filled('user_id') ? (int)$request->input('user_id') : null);

        $deviceType = strtolower($request->input('device_type', 'android'));

        $tokenRecord = $this->firebaseService->registerDeviceToken(
            $request->input('fcm_token'),
            $userId,
            $deviceType,
            $request->input('device_id'),
            $request->input('device_name'),
            $request->input('app_version')
        );

        return response()->json([
            'success' => true,
            'status'  => true,
            'message' => 'FCM device token registered and synchronized successfully.',
            'data'    => [
                'token_id'       => $tokenRecord->id,
                'device_type'    => $tokenRecord->device_type,
                'user_id'        => $tokenRecord->user_id,
                'device_id'      => $tokenRecord->device_id,
                'is_active'      => $tokenRecord->is_active,
                'last_active_at' => $tokenRecord->last_active_at?->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * API: Invalidate/remove device FCM token (e.g. on logout)
     * POST /api/fcm/token/remove
     * DELETE /api/fcm/token
     */
    public function removeToken(Request $request)
    {
        $token = $request->input('fcm_token')
            ?? $request->input('token')
            ?? $request->input('device_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'fcm_token is required to deactivate device.',
            ], 422);
        }

        $this->firebaseService->invalidateToken($token);

        return response()->json([
            'success' => true,
            'status'  => true,
            'message' => 'FCM device token deactivated successfully.',
        ], 200);
    }

    /**
     * API: Send Push Notification
     * POST /api/fcm/send
     * POST /api/notifications/send-push
     */
    public function sendPush(Request $request)
    {
        // Aliases for message / body
        if (!$request->has('message') && $request->has('body')) {
            $request->merge(['message' => $request->input('body')]);
        }

        // Aliases for token
        if (!$request->has('fcm_token')) {
            $t = $request->input('token') ?? $request->input('device_token');
            if ($t) {
                $request->merge(['fcm_token' => $t]);
            }
        }

        // Aliases for tokens array
        if (!$request->has('tokens') && $request->has('device_tokens')) {
            $request->merge(['tokens' => $request->input('device_tokens')]);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'message'     => 'required|string',
            'fcm_token'   => 'nullable|string',
            'tokens'      => 'nullable|array',
            'tokens.*'    => 'string',
            'user_id'     => 'nullable|integer|exists:users,id',
            'topic'       => 'nullable|string|max:100',
            'send_to_all' => 'nullable|boolean',
            'type'        => 'nullable|string|max:50',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'image_url'   => 'nullable|string|url',
            'data'        => 'nullable|array',
            'channel_id'  => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Verify at least one destination target is specified
        $hasToken = $request->filled('fcm_token');
        $hasTokens = $request->filled('tokens') && is_array($request->tokens) && count($request->tokens) > 0;
        $hasUserId = $request->filled('user_id');
        $hasTopic = $request->filled('topic');
        $sendToAll = filter_var($request->input('send_to_all'), FILTER_VALIDATE_BOOLEAN);

        if (!$hasToken && !$hasTokens && !$hasUserId && !$hasTopic && !$sendToAll) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Please provide a destination target: fcm_token, tokens (array), user_id, topic, or set send_to_all=true.',
            ], 422);
        }

        // Handle image upload or image URL
        $imageFileName = null;
        $imageUrl = $request->input('image_url');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $destination = public_path('uploads/notification');
            if (!File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $imageFileName);
            $imageUrl = asset('uploads/notification/' . $imageFileName);
        }

        $title = $request->input('title');
        $message = $request->input('message');
        $type = $request->input('type', 'General');
        $customData = $request->input('data', []);
        $channelId = $request->input('channel_id', 'medicto_general');

        // Check if Firebase credentials configured
        if (!$this->firebaseService->isConfigured()) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Firebase credentials file missing or incomplete in storage/app/firebase/firebase-service-account.json. Please upload the Service Account JSON key from Firebase Console.',
                'diagnostic' => $this->firebaseService->getStatus(),
            ], 503);
        }

        // Determine send_to description and recipients
        $sendToDescription = 'Single Device';
        $totalRecipients = 1;
        $successCount = 0;
        $failedCount = 0;
        $invalidTokensRemoved = 0;
        $errors = [];

        if ($sendToAll) {
            $sendToDescription = 'All Users';
            $result = $this->firebaseService->sendToAll($title, $message, $customData, $imageUrl, $channelId);
            $totalRecipients = $result['total'];
            $successCount = $result['success_count'];
            $failedCount = $result['failed_count'];
            $invalidTokensRemoved = $result['invalid_tokens_removed'];
            $errors = $result['errors'];
        } elseif ($hasTopic) {
            $sendToDescription = 'Topic: ' . $request->topic;
            $result = $this->firebaseService->sendToTopic($request->topic, $title, $message, $customData, $imageUrl, $channelId);
            $totalRecipients = 1;
            $successCount = $result['success'] ? 1 : 0;
            $failedCount = $result['success'] ? 0 : 1;
            if (!$result['success']) {
                $errors[] = $result['error'];
            }
        } elseif ($hasUserId) {
            $sendToDescription = 'User ID: ' . $request->user_id;
            $result = $this->firebaseService->sendToUser($request->user_id, $title, $message, $customData, $imageUrl, $channelId);
            $totalRecipients = $result['total'];
            $successCount = $result['success_count'];
            $failedCount = $result['failed_count'];
            $invalidTokensRemoved = $result['invalid_tokens_removed'];
            $errors = $result['errors'];
        } elseif ($hasTokens) {
            $sendToDescription = 'Multiple Devices (' . count($request->tokens) . ')';
            $result = $this->firebaseService->sendToTokens($request->tokens, $title, $message, $customData, $imageUrl, $channelId);
            $totalRecipients = $result['total'];
            $successCount = $result['success_count'];
            $failedCount = $result['failed_count'];
            $invalidTokensRemoved = $result['invalid_tokens_removed'];
            $errors = $result['errors'];
        } else {
            // Single device token
            $sendToDescription = 'Single Token';
            $result = $this->firebaseService->sendToDevice($request->fcm_token, $title, $message, $customData, $imageUrl, $channelId);
            $totalRecipients = 1;
            $successCount = $result['success'] ? 1 : 0;
            $failedCount = $result['success'] ? 0 : 1;
            if ($result['token_invalid']) {
                $invalidTokensRemoved = 1;
            }
            if (!$result['success']) {
                $errors[] = $result['error'];
            }
        }

        // Log notification to noties table for history and admin panel visibility
        $notificationStatus = 'Sent';
        if ($failedCount > 0 && $successCount > 0) {
            $notificationStatus = 'Partial Success';
        } elseif ($failedCount > 0 && $successCount === 0) {
            $notificationStatus = 'Failed';
        }

        $notyRecord = noty::create([
            'user_id'       => $request->user_id ? (int)$request->user_id : null,
            'target_role'   => $request->target_role ?? ($hasUserId ? 'user' : 'all'),
            'title'         => $title,
            'message'       => $message,
            'type'          => $type,
            'screen'        => $request->screen ?? ($customData['screen'] ?? null),
            'reference_id'  => $request->reference_id ?? ($customData['reference_id'] ?? null),
            'data'          => $customData,
            'image'         => $imageFileName,
            'send_to'       => $sendToDescription,
            'status'        => $notificationStatus,
            'total_users'   => $totalRecipients,
            'success_count' => $successCount,
            'failed_count'  => $failedCount,
        ]);

        return response()->json([
            'success' => $successCount > 0,
            'status'  => $successCount > 0,
            'message' => $successCount > 0
                ? "Push notification sent successfully to {$successCount} device(s)."
                : "Failed to deliver push notification: " . ($errors[0] ?? 'Unknown error'),
            'data'    => [
                'notification_id'        => $notyRecord->id,
                'title'                  => $title,
                'target'                 => $sendToDescription,
                'total_recipients'       => $totalRecipients,
                'success_count'          => $successCount,
                'failed_count'           => $failedCount,
                'invalid_tokens_removed' => $invalidTokensRemoved,
                'image_url'              => $imageUrl,
                'errors'                 => $errors,
            ],
        ], $successCount > 0 ? 200 : 400);
    }

    /**
     * API: List notification history for mobile app (Customer Feed)
     * GET /api/notifications
     * GET /api/app/notifications
     */
    public function getNotifications(Request $request)
    {
        $userId = auth('sanctum')->id()
            ?? auth()->id()
            ?? ($request->filled('user_id') ? (int)$request->input('user_id') : null);

        $query = noty::forCustomerFeed($userId)->latest();

        if ($request->filled('type') && $request->input('type') !== 'all') {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('unread_only') && filter_var($request->input('unread_only'), FILTER_VALIDATE_BOOLEAN)) {
            $query->where('is_read', false);
        }

        $limit = min((int)$request->input('limit', 20), 100);
        $notifications = $query->paginate($limit);

        // Unread badge count
        $unreadCount = noty::forCustomerFeed($userId)
            ->where('is_read', false)
            ->whereIn('status', ['Sent', 'Partial Success', 'Pending'])
            ->count();

        // Format and enrich each item
        $notifications->getCollection()->transform(function ($item) {
            $parsedData = is_array($item->data) ? $item->data : (json_decode($item->data, true) ?? []);
            return [
                'id'           => $item->id,
                'title'        => $item->title,
                'message'      => $item->message,
                'type'         => $item->type ?? 'General',
                'screen'       => $item->screen ?? ($parsedData['screen'] ?? null),
                'reference_id' => $item->reference_id ?? ($parsedData['reference_id'] ?? null),
                'data'         => $parsedData,
                'image_url'    => $item->image ? (str_starts_with($item->image, 'http') ? $item->image : asset('uploads/notification/' . $item->image)) : null,
                'is_read'      => (bool)$item->is_read,
                'read_at'      => $item->read_at?->toIso8601String(),
                'created_at'   => $item->created_at?->toIso8601String(),
                'time_ago'     => $item->created_at ? $item->created_at->diffForHumans() : 'Just now',
            ];
        });

        return response()->json([
            'success'      => true,
            'status'       => true,
            'unread_count' => $unreadCount,
            'data'         => $notifications->items(),
            'meta'         => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'per_page'     => $notifications->perPage(),
                'total'        => $notifications->total(),
            ],
        ], 200);
    }

    /**
     * API: Mark single notification as read
     * POST /api/notifications/{id}/read
     * POST /api/app/notifications/{id}/read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = noty::find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'status'  => true,
            'message' => 'Notification marked as read successfully.',
            'data'    => [
                'id'      => $notification->id,
                'is_read' => true,
                'read_at' => $notification->read_at->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * API: Mark all notifications as read for user
     * POST /api/notifications/mark-all-read
     * POST /api/app/notifications/mark-all-read
     */
    public function markAllAsRead(Request $request)
    {
        $userId = auth('sanctum')->id()
            ?? auth()->id()
            ?? ($request->filled('user_id') ? (int)$request->input('user_id') : null);

        $query = noty::forCustomerFeed($userId)->where('is_read', false);
        $count = $query->count();
        $query->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success'      => true,
            'status'       => true,
            'message'      => "{$count} notifications marked as read.",
            'marked_count' => $count,
        ], 200);
    }

    /**
     * API: Diagnostic status of Firebase configuration
     * GET /api/fcm/status
     */
    public function getStatus()
    {
        $status = $this->firebaseService->getStatus();
        $status['total_active_devices'] = DeviceToken::where('is_active', true)->count();
        $status['devices_by_platform'] = [
            'android' => DeviceToken::where('is_active', true)->where('device_type', 'android')->count(),
            'ios'     => DeviceToken::where('is_active', true)->where('device_type', 'ios')->count(),
            'web'     => DeviceToken::where('is_active', true)->where('device_type', 'web')->count(),
        ];
        $status['users_with_token'] = User::whereNotNull('fcm_token')->count();

        return response()->json([
            'success' => true,
            'status'  => true,
            'data'    => $status,
        ], 200);
    }

    // ==========================================
    // ADMIN NOTIFICATION APIS
    // ==========================================

    /**
     * Admin API: List notifications with filters & search
     * GET /api/admin/notifications
     */
    public function adminNotifications(Request $request)
    {
        $query = noty::latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('send_to', 'like', "%{$search}%")
                  ->orWhere('reference_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('target_role') && $request->target_role !== 'all') {
            $query->where('target_role', $request->target_role);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            if ($request->date === 'today') {
                $query->whereDate('created_at', today());
            } elseif ($request->date === 'this_week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            }
        }

        $limit = min((int)$request->input('limit', 15), 50);
        $notifications = $query->paginate($limit);

        $notifications->getCollection()->transform(function ($item) {
            $parsedData = is_array($item->data) ? $item->data : (json_decode($item->data, true) ?? []);
            return [
                'id'            => $item->id,
                'user_id'       => $item->user_id,
                'target_role'   => $item->target_role,
                'title'         => $item->title,
                'message'       => $item->message,
                'type'          => $item->type,
                'screen'        => $item->screen ?? ($parsedData['screen'] ?? null),
                'reference_id'  => $item->reference_id ?? ($parsedData['reference_id'] ?? null),
                'data'          => $parsedData,
                'send_to'       => $item->send_to,
                'status'        => $item->status,
                'total_users'   => $item->total_users,
                'success_count' => $item->success_count,
                'failed_count'  => $item->failed_count,
                'image_url'     => $item->image ? (str_starts_with($item->image, 'http') ? $item->image : asset('uploads/notification/' . $item->image)) : null,
                'created_at'    => $item->created_at?->toIso8601String(),
                'time_ago'      => $item->created_at ? $item->created_at->diffForHumans() : 'Just now',
            ];
        });

        return response()->json([
            'success' => true,
            'status'  => true,
            'data'    => $notifications->items(),
            'meta'    => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'per_page'     => $notifications->perPage(),
                'total'        => $notifications->total(),
            ],
        ], 200);
    }

    /**
     * Admin API: Notification center summary statistics
     * GET /api/admin/notifications/stats
     */
    public function adminStats()
    {
        $totalNotifications = noty::count();
        $todayCount = noty::whereDate('created_at', today())->count();
        $failedCount = noty::where('status', 'Failed')->count();
        $partialSuccessCount = noty::where('status', 'Partial Success')->count();
        $sentCount = noty::where('status', 'Sent')->count();
        $totalUsers = User::count();
        $usersWithTokens = User::whereNotNull('fcm_token')->count();

        $activeDevices = DeviceToken::where('is_active', true)->count();
        $androidDevices = DeviceToken::where('is_active', true)->where('device_type', 'android')->count();
        $iosDevices = DeviceToken::where('is_active', true)->where('device_type', 'ios')->count();
        $webDevices = DeviceToken::where('is_active', true)->where('device_type', 'web')->count();

        $status = $this->firebaseService->getStatus();

        return response()->json([
            'success' => true,
            'status'  => true,
            'data'    => [
                'total_notifications'   => $totalNotifications,
                'sent_today'            => $todayCount,
                'sent_count'            => $sentCount,
                'partial_success_count' => $partialSuccessCount,
                'failed_count'          => $failedCount,
                'total_users'           => $totalUsers,
                'users_with_token'      => $usersWithTokens,
                'total_active_devices'  => $activeDevices,
                'devices_by_platform'   => [
                    'android' => $androidDevices,
                    'ios'     => $iosDevices,
                    'web'     => $webDevices,
                ],
                'firebase_configured'   => $status['configured'],
                'firebase_project_id'   => $status['project_id'],
            ],
        ], 200);
    }

    /**
     * Admin API: Get single notification detail
     * GET /api/admin/notifications/{id}
     */
    public function adminShow($id)
    {
        $notification = noty::find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->image_url = $notification->image
            ? asset('uploads/notification/' . $notification->image)
            : null;

        return response()->json([
            'success' => true,
            'status'  => true,
            'data'    => $notification,
        ], 200);
    }

    /**
     * Admin API: Delete notification
     * DELETE /api/admin/notifications/{id}
     */
    public function adminDestroy($id)
    {
        $notification = noty::find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        if ($notification->image && File::exists(public_path('uploads/notification/' . $notification->image))) {
            File::delete(public_path('uploads/notification/' . $notification->image));
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'status'  => true,
            'message' => 'Notification deleted successfully.',
        ], 200);
    }

    /**
     * Admin API: Resend an existing notification
     * POST /api/admin/notifications/{id}/resend
     */
    public function adminResend($id)
    {
        $notification = noty::find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        if (!$this->firebaseService->isConfigured()) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Firebase credentials file missing or incomplete in storage/app/firebase/firebase-service-account.json.',
            ], 503);
        }

        $imageUrl = $notification->image ? asset('uploads/notification/' . $notification->image) : null;
        $result = $this->firebaseService->sendToAll(
            $notification->title,
            $notification->message,
            ['type' => $notification->type ?? 'General'],
            $imageUrl
        );

        $notificationStatus = 'Sent';
        if ($result['failed_count'] > 0 && $result['success_count'] > 0) {
            $notificationStatus = 'Partial Success';
        } elseif ($result['failed_count'] > 0 && $result['success_count'] === 0) {
            $notificationStatus = 'Failed';
        }

        $notification->update([
            'total_users'   => $result['total'],
            'success_count' => $result['success_count'],
            'failed_count'  => $result['failed_count'],
            'status'        => $notificationStatus,
        ]);

        return response()->json([
            'success' => $result['success_count'] > 0,
            'status'  => $result['success_count'] > 0,
            'message' => "Notification resent: {$result['success_count']} delivered, {$result['failed_count']} failed.",
            'data'    => [
                'notification_id'        => $notification->id,
                'total_recipients'       => $result['total'],
                'success_count'          => $result['success_count'],
                'failed_count'           => $result['failed_count'],
                'invalid_tokens_removed' => $result['invalid_tokens_removed'],
                'status'                 => $notificationStatus,
            ],
        ], $result['success_count'] > 0 ? 200 : 400);
    }

    /**
     * Admin API: List registered devices
     * GET /api/admin/fcm/devices
     */
    public function adminDevices(Request $request)
    {
        $query = DeviceToken::with('user:id,name,email,role,status')->latest();

        if ($request->filled('device_type') && $request->device_type !== 'all') {
            $query->where('device_type', $request->device_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('device_name', 'like', "%{$search}%")
                  ->orWhere('device_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $limit = min((int)$request->input('limit', 20), 50);
        $devices = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'status'  => true,
            'data'    => $devices->items(),
            'meta'    => [
                'current_page' => $devices->currentPage(),
                'last_page'    => $devices->lastPage(),
                'total'        => $devices->total(),
            ],
        ], 200);
    }

    /**
     * Admin API: Send quick test push notification
     * POST /api/admin/fcm/test
     */
    public function adminTestPush(Request $request)
    {
        $request->merge([
            'title'   => $request->input('title', 'Medicto FCM Test Notification'),
            'message' => $request->input('message', 'This is a test notification from Medicto Admin Panel at ' . now()->toDateTimeString()),
            'type'    => 'Test',
        ]);

        return $this->sendPush($request);
    }

    // ==========================================
    // MOBILE APP NOTIFICATION APIS
    // ==========================================

    /**
     * App API: Get single notification details
     * GET /api/app/notifications/{id}
     */
    public function appNotificationDetail($id)
    {
        $notification = noty::find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->image_url = $notification->image
            ? asset('uploads/notification/' . $notification->image)
            : null;

        return response()->json([
            'success' => true,
            'status'  => true,
            'data'    => [
                'id'         => $notification->id,
                'title'      => $notification->title,
                'message'    => $notification->message,
                'type'       => $notification->type,
                'image_url'  => $notification->image_url,
                'created_at' => $notification->created_at?->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * App API: Unread / Recent notifications count badge
     * GET /api/app/notifications/unread-count
     */
    public function appUnreadCount(Request $request)
    {
        $days = (int)$request->input('days', 7);
        $recentCount = noty::where('created_at', '>=', now()->subDays($days))
            ->whereIn('status', ['Sent', 'Partial Success'])
            ->count();

        return response()->json([
            'success' => true,
            'status'  => true,
            'data'    => [
                'unread_count' => $recentCount,
                'period_days'  => $days,
            ],
        ], 200);
    }
}
