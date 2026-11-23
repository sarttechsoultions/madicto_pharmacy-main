<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
use Kreait\Firebase\Exception\FirebaseException;

class FirebaseNotificationService
{
    /**
     * Singleton instance of Kreait Messaging
     */
    protected ?Messaging $messaging = null;

    /**
     * Cached path to credentials
     */
    protected ?string $credentialsPath = null;

    /**
     * Resolve the credentials file path
     */
    public function getCredentialsPath(): string
    {
        if ($this->credentialsPath !== null) {
            return $this->credentialsPath;
        }

        $configured = config('firebase.projects.app.credentials')
            ?? env('FIREBASE_CREDENTIALS')
            ?? env('GOOGLE_APPLICATION_CREDENTIALS')
            ?? 'storage/app/firebase/firebase-service-account.json';

        // Resolve absolute vs relative paths
        $isAbsolute = str_starts_with($configured, '/')
            || str_starts_with($configured, '\\')
            || (strlen($configured) > 2 && ctype_alpha($configured[0]) && $configured[1] === ':');

        if (!$isAbsolute) {
            $configured = base_path($configured);
        }

        $this->credentialsPath = $configured;
        return $this->credentialsPath;
    }

    /**
     * Check if Firebase credentials file exists and is valid JSON
     */
    public function isConfigured(): bool
    {
        $path = $this->getCredentialsPath();

        if (!File::exists($path)) {
            return false;
        }

        $content = @file_get_contents($path);
        if (!$content) {
            return false;
        }

        $json = @json_decode($content, true);
        if (!is_array($json)) {
            return false;
        }

        // Must have project_id and private_key
        return !empty($json['project_id']) && !empty($json['private_key']);
    }

    /**
     * Get diagnostic status of Firebase setup
     */
    public function getStatus(): array
    {
        $path = $this->getCredentialsPath();
        $exists = File::exists($path);
        $projectId = null;
        $clientEmail = null;
        $validJson = false;

        if ($exists) {
            $content = @file_get_contents($path);
            $json = @json_decode($content, true);
            if (is_array($json)) {
                $validJson = true;
                $projectId = $json['project_id'] ?? null;
                $clientEmail = $json['client_email'] ?? null;
            }
        }

        return [
            'configured'       => $this->isConfigured(),
            'credentials_path' => $path,
            'file_exists'      => $exists,
            'valid_json'       => $validJson,
            'project_id'       => $projectId,
            'client_email'     => $clientEmail,
        ];
    }

    /**
     * Get or create Kreait Messaging instance
     */
    public function getMessaging(): ?Messaging
    {
        if ($this->messaging !== null) {
            return $this->messaging;
        }

        if (!$this->isConfigured()) {
            return null;
        }

        $factory = (new Factory)->withServiceAccount($this->getCredentialsPath());
        $this->messaging = $factory->createMessaging();

        return $this->messaging;
    }

    /**
     * Normalize custom data payload (all values must be strings for FCM)
     */
    protected function sanitizeData(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $sanitized[(string)$key] = $value ? '1' : '0';
            } elseif (is_null($value)) {
                $sanitized[(string)$key] = '';
            } elseif (is_array($value) || is_object($value)) {
                $sanitized[(string)$key] = json_encode($value);
            } else {
                $sanitized[(string)$key] = (string)$value;
            }
        }
        return $sanitized;
    }

    /**
     * Build standard cross-platform CloudMessage object
     */
    public function buildMessage(
        string $targetType,
        string $targetValue,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null,
        string $channelId = 'medicto_general'
    ): CloudMessage {
        $notification = Notification::create($title, $body, $imageUrl);

        // Android configuration
        $androidNotification = [
            'channel_id'   => $channelId,
            'sound'        => 'default',
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ];
        if ($imageUrl) {
            $androidNotification['image'] = $imageUrl;
        }

        $androidConfig = AndroidConfig::fromArray([
            'priority'     => 'high',
            'notification' => $androidNotification,
        ]);

        // iOS APNs configuration
        $aps = [
            'alert' => [
                'title' => $title,
                'body'  => $body,
            ],
            'sound'             => 'default',
            'badge'             => 1,
            'content-available' => 1,
            'mutable-content'   => 1,
        ];

        $apnsOptions = [];
        if ($imageUrl) {
            $apnsOptions['fcm_options'] = ['image' => $imageUrl];
        }

        $apnsConfig = ApnsConfig::fromArray(array_merge([
            'headers' => ['apns-priority' => '10'],
            'payload' => ['aps' => $aps],
        ], $apnsOptions));

        // WebPush configuration
        $webPushConfig = WebPushConfig::fromArray([
            'notification' => array_filter([
                'title' => $title,
                'body'  => $body,
                'icon'  => $imageUrl ?? '/favicon.ico',
            ]),
        ]);

        $message = CloudMessage::withTarget($targetType, $targetValue)
            ->withNotification($notification)
            ->withAndroidConfig($androidConfig)
            ->withApnsConfig($apnsConfig)
            ->withWebPushConfig($webPushConfig);

        // Auto-enrich data payload with all essential navigation & display fields for mobile apps
        $enrichedData = array_merge([
            'title'        => $title,
            'body'         => $body,
            'message'      => $body,
            'image'        => $imageUrl ?? '',
            'image_url'    => $imageUrl ?? '',
            'channel_id'   => $channelId,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'sound'        => 'default',
            'timestamp'    => (string)now()->timestamp,
        ], $data);

        $sanitizedData = $this->sanitizeData($enrichedData);
        $message = $message->withData($sanitizedData);

        return $message;
    }

    /**
     * Send push notification to a single device token
     *
     * @return array ['success' => bool, 'message_id' => ?string, 'error' => ?string, 'token_invalid' => bool]
     */
    public function sendToDevice(
        string $token,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null,
        string $channelId = 'medicto_general'
    ): array {
        if (!$this->isConfigured()) {
            return [
                'success'       => false,
                'message_id'    => null,
                'error'         => 'Firebase credentials not configured. Please place firebase-service-account.json in storage/app/firebase/',
                'token_invalid' => false,
            ];
        }

        try {
            $messaging = $this->getMessaging();
            $message = $this->buildMessage('token', $token, $title, $body, $data, $imageUrl, $channelId);
            $result = $messaging->send($message);

            return [
                'success'       => true,
                'message_id'    => is_array($result) ? ($result['name'] ?? null) : (string)$result,
                'error'         => null,
                'token_invalid' => false,
            ];
        } catch (NotFound $e) {
            // Token is no longer registered or has been refreshed on client
            $this->invalidateToken($token);
            Log::warning("FCM token unregistered / not found: {$token}");

            return [
                'success'       => false,
                'message_id'    => null,
                'error'         => 'Device token is no longer registered or has expired.',
                'token_invalid' => true,
            ];
        } catch (InvalidMessage $e) {
            $this->invalidateToken($token);
            Log::warning("FCM token invalid format: {$token}. Error: " . $e->getMessage());

            return [
                'success'       => false,
                'message_id'    => null,
                'error'         => 'Device token is invalid: ' . $e->getMessage(),
                'token_invalid' => true,
            ];
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            $isInvalid = str_contains($errorMessage, 'UNREGISTERED')
                || str_contains($errorMessage, 'INVALID_ARGUMENT')
                || str_contains($errorMessage, 'registration-token-not-registered');

            if ($isInvalid) {
                $this->invalidateToken($token);
            }

            Log::error("FCM Send error for token [{$token}]: {$errorMessage}");

            return [
                'success'       => false,
                'message_id'    => null,
                'error'         => $errorMessage,
                'token_invalid' => $isInvalid,
            ];
        }
    }

    /**
     * Send push notification to multiple device tokens
     */
    public function sendToTokens(
        array $tokens,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null,
        string $channelId = 'medicto_general'
    ): array {
        $tokens = array_unique(array_filter($tokens));
        $total = count($tokens);

        if ($total === 0) {
            return [
                'total'                  => 0,
                'success_count'          => 0,
                'failed_count'           => 0,
                'invalid_tokens_removed' => 0,
                'errors'                 => ['No valid device tokens provided'],
            ];
        }

        if (!$this->isConfigured()) {
            return [
                'total'                  => $total,
                'success_count'          => 0,
                'failed_count'           => $total,
                'invalid_tokens_removed' => 0,
                'errors'                 => ['Firebase credentials file missing or invalid in storage/app/firebase/'],
            ];
        }

        $successCount = 0;
        $failedCount = 0;
        $invalidTokensCount = 0;
        $errors = [];

        foreach ($tokens as $token) {
            $res = $this->sendToDevice($token, $title, $body, $data, $imageUrl, $channelId);

            if ($res['success']) {
                $successCount++;
            } else {
                $failedCount++;
                if (!empty($res['error']) && count($errors) < 10) {
                    $errors[] = $res['error'];
                }
                if (!empty($res['token_invalid'])) {
                    $invalidTokensCount++;
                }
            }
        }

        return [
            'total'                  => $total,
            'success_count'          => $successCount,
            'failed_count'           => $failedCount,
            'invalid_tokens_removed' => $invalidTokensCount,
            'errors'                 => array_values(array_unique($errors)),
        ];
    }

    /**
     * Send push notification to all active devices of a specific user
     */
    public function sendToUser(
        $user,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null,
        string $channelId = 'medicto_general'
    ): array {
        $userId = $user instanceof User ? $user->id : (int)$user;

        // Collect all tokens from device_tokens table and users table
        $tokens = DeviceToken::where('user_id', $userId)
            ->where('is_active', true)
            ->pluck('fcm_token')
            ->toArray();

        $userRecord = User::find($userId);
        if ($userRecord && !empty($userRecord->fcm_token)) {
            $tokens[] = $userRecord->fcm_token;
        }

        $tokens = array_unique(array_filter($tokens));

        if (empty($tokens)) {
            return [
                'total'                  => 0,
                'success_count'          => 0,
                'failed_count'           => 0,
                'invalid_tokens_removed' => 0,
                'errors'                 => ["User #{$userId} has no active device tokens registered."],
            ];
        }

        return $this->sendToTokens($tokens, $title, $body, $data, $imageUrl, $channelId);
    }

    /**
     * Send push notification to all users in the system
     */
    public function sendToAll(
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null,
        string $channelId = 'medicto_general'
    ): array {
        $deviceTokens = DeviceToken::where('is_active', true)
            ->pluck('fcm_token')
            ->toArray();

        $userTokens = User::whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        $allTokens = array_unique(array_filter(array_merge($deviceTokens, $userTokens)));

        return $this->sendToTokens($allTokens, $title, $body, $data, $imageUrl, $channelId);
    }

    /**
     * Send notification to a specific topic
     */
    public function sendToTopic(
        string $topic,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null,
        string $channelId = 'medicto_general'
    ): array {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error'   => 'Firebase credentials not configured',
            ];
        }

        try {
            $messaging = $this->getMessaging();
            $cleanTopic = preg_replace('/[^a-zA-Z0-9-_.~%]/', '', $topic);
            $message = $this->buildMessage('topic', $cleanTopic, $title, $body, $data, $imageUrl, $channelId);
            $result = $messaging->send($message);

            return [
                'success'    => true,
                'message_id' => is_array($result) ? ($result['name'] ?? null) : (string)$result,
                'error'      => null,
            ];
        } catch (\Throwable $e) {
            Log::error("FCM Topic send error for [{$topic}]: " . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Invalidate and purge stale or expired FCM tokens
     */
    public function invalidateToken(string $token): void
    {
        $hash = hash('sha256', $token);

        // Deactivate in device_tokens table
        DeviceToken::where('token_hash', $hash)
            ->orWhere('fcm_token', $token)
            ->update(['is_active' => false]);

        // Clear from user record
        User::where('fcm_token', $token)->update(['fcm_token' => null]);
    }

    /**
     * Register or update an FCM token for a user or guest device
     */
    public function registerDeviceToken(
        string $token,
        ?int $userId = null,
        string $deviceType = 'android',
        ?string $deviceId = null,
        ?string $deviceName = null,
        ?string $appVersion = null
    ): DeviceToken {
        $hash = hash('sha256', $token);
        $deviceType = in_array(strtolower($deviceType), ['android', 'ios', 'web', 'other'])
            ? strtolower($deviceType)
            : 'android';

        $record = DeviceToken::updateOrCreate(
            ['token_hash' => $hash],
            [
                'user_id'        => $userId,
                'fcm_token'      => $token,
                'device_type'    => $deviceType,
                'device_id'      => $deviceId,
                'device_name'    => $deviceName,
                'app_version'    => $appVersion,
                'is_active'      => true,
                'last_active_at' => now(),
            ]
        );

        // If user is provided, also update user's primary fcm_token
        if ($userId) {
            User::where('id', $userId)->update(['fcm_token' => $token]);
        }

        return $record;
    }

    /**
     * Send push notification to all Administrators
     */
    public function sendToAdmins(
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null,
        string $channelId = 'medicto_admin'
    ): array {
        $adminUserIds = User::where('role', '1')->orWhere('role', 1)->pluck('id')->toArray();

        $tokens = DeviceToken::whereIn('user_id', $adminUserIds)
            ->where('is_active', true)
            ->pluck('fcm_token')
            ->toArray();

        $directTokens = User::whereIn('id', $adminUserIds)
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        $allTokens = array_unique(array_filter(array_merge($tokens, $directTokens)));

        if (empty($allTokens)) {
            return [
                'total'                  => 0,
                'success_count'          => 0,
                'failed_count'           => 0,
                'invalid_tokens_removed' => 0,
                'errors'                 => ['No active admin device tokens found'],
            ];
        }

        return $this->sendToTokens($allTokens, $title, $body, $data, $imageUrl, $channelId);
    }

    /**
     * Notify Admin of an event (stores record in noties with target_role='admin' & dispatches push)
     */
    public function notifyAdmins(
        string $title,
        string $body,
        string $type,
        ?string $screen = null,
        ?string $referenceId = null,
        array $extraData = []
    ): ?\App\Models\notification {
        $dataPayload = array_merge([
            'type'         => $type,
            'screen'       => $screen ?? 'AdminNotifications',
            'reference_id' => $referenceId ?? '',
            'target_role'  => 'admin',
        ], $extraData);

        $notyRecord = \App\Models\notification::create([
            'title'        => $title,
            'message'      => $body,
            'type'         => $type,
            'target_role'  => 'admin',
            'screen'       => $screen,
            'reference_id' => $referenceId,
            'data'         => $dataPayload,
            'send_to'      => 'Admins',
            'status'       => 'Pending',
            'total_users'  => User::where('role', '1')->orWhere('role', 1)->count(),
        ]);

        try {
            $result = $this->sendToAdmins($title, $body, $dataPayload, null, 'medicto_admin');
            $status = $result['failed_count'] > 0
                ? ($result['success_count'] > 0 ? 'Partial Success' : 'Failed')
                : ($result['success_count'] > 0 ? 'Sent' : 'Pending');

            $notyRecord->update([
                'status'        => $status,
                'success_count' => $result['success_count'],
                'failed_count'  => $result['failed_count'],
            ]);
        } catch (\Throwable $e) {
            Log::warning("notifyAdmins push dispatch warning: " . $e->getMessage());
        }

        return $notyRecord;
    }

    /**
     * Notify Customer of an event (stores record in noties with user_id & dispatches push)
     */
    public function notifyCustomer(
        int $userId,
        string $title,
        string $body,
        string $type,
        ?string $screen = null,
        ?string $referenceId = null,
        array $extraData = [],
        ?string $imageUrl = null
    ): ?\App\Models\notification {
        $dataPayload = array_merge([
            'type'         => $type,
            'screen'       => $screen ?? 'NotificationScreen',
            'reference_id' => $referenceId ?? '',
            'user_id'      => (string)$userId,
            'target_role'  => 'user',
        ], $extraData);

        $notyRecord = \App\Models\notification::create([
            'user_id'      => $userId,
            'target_role'  => 'user',
            'title'        => $title,
            'message'      => $body,
            'type'         => $type,
            'screen'       => $screen,
            'reference_id' => $referenceId,
            'data'         => $dataPayload,
            'image'        => $imageUrl,
            'send_to'      => "User #{$userId}",
            'status'       => 'Pending',
            'total_users'  => 1,
        ]);

        try {
            $result = $this->sendToUser($userId, $title, $body, $dataPayload, $imageUrl);
            $status = $result['failed_count'] > 0
                ? ($result['success_count'] > 0 ? 'Partial Success' : 'Failed')
                : ($result['success_count'] > 0 ? 'Sent' : 'Pending');

            $notyRecord->update([
                'status'        => $status,
                'success_count' => $result['success_count'],
                'failed_count'  => $result['failed_count'],
            ]);
        } catch (\Throwable $e) {
            Log::warning("notifyCustomer push dispatch warning: " . $e->getMessage());
        }

        return $notyRecord;
    }

    /**
     * Automatic Event Trigger: New User Registered
     */
    public function notifyNewUserRegistration(User $user): void
    {
        $userName = $user->name ?: 'Customer';
        $email = $user->email ?: 'N/A';
        $phone = $user->number ?: $user->phone ?: 'N/A';

        // 1. Notify Admins
        $this->notifyAdmins(
            "New User Registered 👤",
            "New customer {$userName} ({$email}) has joined Medicto Pharmacy.",
            "NEW_USER",
            "UserProfileScreen",
            (string)$user->id,
            [
                'user_id' => (string)$user->id,
                'name'    => $userName,
                'email'   => $email,
                'phone'   => $phone,
            ]
        );

        // 2. Welcome notification for the new user
        $this->notifyCustomer(
            $user->id,
            "Welcome to Medicto Pharmacy! 🎉",
            "Hello {$userName}! Thank you for joining Medicto. Find genuine medicines and book lab tests easily.",
            "WELCOME",
            "HomeScreen",
            (string)$user->id,
            ['user_id' => (string)$user->id]
        );
    }

    /**
     * Automatic Event Trigger: New Order Placed
     */
    public function notifyNewOrderPlaced($order, User $user): void
    {
        $orderCode = $order->order_id ?? ('ORD-' . $order->id);
        $total = number_format((float)($order->total_amount ?? 0), 2);
        $userName = $user->name ?: 'Customer';

        // 1. Notify Admins
        $this->notifyAdmins(
            "New Order Received! 🛒 #{$orderCode}",
            "Order #{$orderCode} placed by {$userName} for ₹{$total}.",
            "NEW_ORDER",
            "AdminOrderDetails",
            $orderCode,
            [
                'order_id'    => $orderCode,
                'order_db_id' => (string)$order->id,
                'user_id'     => (string)$user->id,
                'amount'      => $total,
            ]
        );

        // 2. Notify Customer
        $this->notifyCustomer(
            $user->id,
            "Order Placed Successfully! 📦 #{$orderCode}",
            "Your order for ₹{$total} has been confirmed and is being processed.",
            "ORDER_PLACED",
            "OrderTrackingScreen",
            $orderCode,
            [
                'order_id'    => $orderCode,
                'order_db_id' => (string)$order->id,
                'amount'      => $total,
            ]
        );
    }

    /**
     * Automatic Event Trigger: Order Status Changed
     */
    public function notifyOrderStatusUpdated($order, string $status): void
    {
        $orderCode = $order->order_id ?? ('ORD-' . $order->id);
        $userId = $order->coustmer_id ?? $order->user_id;

        if (!$userId) {
            return;
        }

        $emoji = match (strtolower($status)) {
            'confirmed'        => '✅',
            'processing'       => '⏳',
            'shipped', 'out for delivery' => '🚚',
            'delivered'        => '🎉',
            'cancelled'        => '❌',
            default            => '📋',
        };

        // Notify Customer
        $this->notifyCustomer(
            (int)$userId,
            "Order {$status} {$emoji} #{$orderCode}",
            "Your order #{$orderCode} is currently: {$status}.",
            "ORDER_STATUS_UPDATE",
            "OrderTrackingScreen",
            $orderCode,
            [
                'order_id'    => $orderCode,
                'order_db_id' => (string)$order->id,
                'status'      => $status,
            ]
        );

        // If cancelled, also alert admins
        if (strtolower($status) === 'cancelled') {
            $this->notifyAdmins(
                "Order Cancelled ⚠️ #{$orderCode}",
                "Order #{$orderCode} has been cancelled.",
                "ORDER_CANCELLED",
                "AdminOrderDetails",
                $orderCode,
                ['order_id' => $orderCode, 'status' => 'Cancelled']
            );
        }
    }

    /**
     * Automatic Event Trigger: New Lab Test Booking
     */
    public function notifyNewLabBooking($booking): void
    {
        $bookingId = $booking->booking_id ?? ('LB-' . $booking->id);
        $testName = $booking->test_name ?? 'Diagnostic Lab Test';
        $customerName = $booking->customer_name ?? 'Customer';
        $amount = number_format((float)($booking->total_amount ?? 0), 2);
        $date = $booking->booking_date ?? date('Y-m-d');

        // 1. Notify Admins
        $this->notifyAdmins(
            "New Lab Test Booking! 🧪 #{$bookingId}",
            "New booking for '{$testName}' by {$customerName} (₹{$amount}) on {$date}.",
            "NEW_LAB_BOOKING",
            "AdminLabBookingDetails",
            $bookingId,
            [
                'booking_id'    => $bookingId,
                'booking_db_id' => (string)$booking->id,
                'test_name'     => $testName,
                'customer_name' => $customerName,
                'booking_date'  => (string)$date,
            ]
        );

        // 2. Notify Customer (if linked to registered user)
        if (!empty($booking->user_id)) {
            $this->notifyCustomer(
                (int)$booking->user_id,
                "Lab Test Booking Confirmed 🩺 #{$bookingId}",
                "Your test '{$testName}' is scheduled for {$date} ({$booking->booking_time}).",
                "LAB_BOOKING_CONFIRMED",
                "LabBookingDetailScreen",
                $bookingId,
                [
                    'booking_id'    => $bookingId,
                    'booking_db_id' => (string)$booking->id,
                    'test_name'     => $testName,
                    'booking_date'  => (string)$date,
                ]
            );
        }
    }

    /**
     * Automatic Event Trigger: Lab Test Booking Status Updated
     */
    public function notifyLabBookingStatusUpdated($booking, string $status): void
    {
        $bookingId = $booking->booking_id ?? ('LB-' . $booking->id);

        if (!empty($booking->user_id)) {
            $this->notifyCustomer(
                (int)$booking->user_id,
                "Lab Test Update: {$status} 🔬 #{$bookingId}",
                "Your lab test booking #{$bookingId} status is now {$status}.",
                "LAB_BOOKING_UPDATE",
                "LabBookingDetailScreen",
                $bookingId,
                ['booking_id' => $bookingId, 'status' => $status]
            );
        }

        if (strtolower($status) === 'cancelled') {
            $this->notifyAdmins(
                "Lab Booking Cancelled ⚠️ #{$bookingId}",
                "Booking #{$bookingId} for {$booking->customer_name} was cancelled.",
                "LAB_BOOKING_CANCELLED",
                "AdminLabBookingDetails",
                $bookingId,
                ['booking_id' => $bookingId, 'status' => 'Cancelled']
            );
        }
    }
}
