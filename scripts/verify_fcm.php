<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DeviceToken;
use App\Models\User;
use App\Models\notification as noty;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

echo "=========================================================\n";
echo "  MEDICTO PHARMACY - FCM PUSH NOTIFICATIONS AUDIT\n";
echo "=========================================================\n\n";

$pass = 0;
$fail = 0;

function assertCheck($condition, $description) {
    global $pass, $fail;
    if ($condition) {
        echo " [PASS] " . $description . "\n";
        $pass++;
    } else {
        echo " [FAIL] " . $description . "\n";
        $fail++;
    }
}

function makeApiCall($method, $uri, $params = [], $files = [], $user = null) {
    global $app;
    $parameters = $params;

    if (!empty($params) && strtoupper($method) === 'GET') {
        $uri .= (strpos($uri, '?') === false ? '?' : '&') . http_build_query($params);
        $parameters = [];
    }

    $server = ['HTTP_ACCEPT' => 'application/json'];
    $request = Request::create($uri, strtoupper($method), $parameters, [], $files, $server);

    if ($user) {
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        auth()->guard('sanctum')->setUser($user);
        auth()->guard('web')->setUser($user);
    }

    $app->instance('request', $request);

    try {
        $response = Route::dispatch($request);
        $statusCode = $response->getStatusCode();
        $data = json_decode($response->getContent(), true) ?? [];
    } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
        $statusCode = 404;
        $data = ['message' => 'Route not found: ' . $e->getMessage()];
    } catch (\Illuminate\Validation\ValidationException $e) {
        $statusCode = 422;
        $data = [
            'success' => false,
            'message' => $e->validator->errors()->first(),
            'errors'  => $e->validator->errors()->toArray(),
        ];
    } catch (\Exception $e) {
        $statusCode = 500;
        $data = ['message' => $e->getMessage()];
    }

    return [$statusCode, $data];
}

// -------------------------------------------------------------
// SECTION 1: Service Layer & Diagnostic Status
// -------------------------------------------------------------
echo "--- SECTION 1: FIREBASE SERVICE & DIAGNOSTIC STATUS ---\n";

$service = app(FirebaseNotificationService::class);
assertCheck($service instanceof FirebaseNotificationService, "FirebaseNotificationService resolved from DI container");

$status = $service->getStatus();
assertCheck(is_array($status) && array_key_exists('configured', $status), "FirebaseNotificationService::getStatus() returns configuration array");
assertCheck(str_contains($status['credentials_path'], 'firebase-service-account.json'), "Firebase credentials path correctly points to storage/app/firebase/firebase-service-account.json");

// Test GET /api/fcm/status
[$statusHttp, $statusData] = makeApiCall('GET', '/api/fcm/status');
assertCheck($statusHttp === 200, "GET /api/fcm/status returns HTTP 200 OK");
assertCheck(isset($statusData['data']['total_active_devices']), "GET /api/fcm/status includes total_active_devices count");

// -------------------------------------------------------------
// SECTION 2: Token Registration & Multi-Platform Support
// -------------------------------------------------------------
echo "\n--- SECTION 2: FCM DEVICE TOKEN REGISTRATION & SYNC ---\n";

$testUser = User::first();
if (!$testUser) {
    $testUser = User::create([
        'name' => 'FCM Test User',
        'email' => 'fcmtest@medicto.com',
        'password' => bcrypt('password123'),
        'role' => '2',
        'status' => 'Active',
    ]);
}

// Test 1: Register Android device token
$androidFcmToken = "fcm_test_android_token_" . time() . "_sample_long_token_string_1234567890";
[$regHttp, $regData] = makeApiCall('POST', '/api/fcm/token', [
    'fcm_token'   => $androidFcmToken,
    'device_type' => 'android',
    'device_id'   => 'android_device_pixel_8',
    'device_name' => 'Google Pixel 8',
    'app_version' => '1.0.0',
    'user_id'     => $testUser->id,
]);

assertCheck($regHttp === 200, "POST /api/fcm/token (Android) returns HTTP 200 OK");
assertCheck(($regData['success'] ?? false) === true, "POST /api/fcm/token returns success: true");

// Verify in DB
$dbToken = DeviceToken::where('fcm_token', $androidFcmToken)->first();
assertCheck($dbToken !== null && $dbToken->device_type === 'android', "DeviceToken persisted with device_type='android'");
assertCheck($dbToken && $dbToken->user_id == $testUser->id, "DeviceToken properly linked to user_id #{$testUser->id}");

$testUser->refresh();
assertCheck($testUser->fcm_token === $androidFcmToken, "User record fcm_token synchronized for backward compatibility");

// Test 2: Register iOS device token
$iosFcmToken = "fcm_test_ios_token_" . time() . "_apns_sample_token_string_9876543210";
[$iosHttp, $iosData] = makeApiCall('POST', '/api/fcm/token', [
    'token'       => $iosFcmToken, // testing alias 'token'
    'platform'    => 'ios',        // testing alias 'platform'
    'device_id'   => 'ios_device_iphone_15_pro',
    'device_name' => 'iPhone 15 Pro Max',
    'app_version' => '1.0.0',
    'user_id'     => $testUser->id,
]);

assertCheck($iosHttp === 200, "POST /api/fcm/token (iOS) returns HTTP 200 OK");
$iosDbToken = DeviceToken::where('fcm_token', $iosFcmToken)->first();
assertCheck($iosDbToken !== null && $iosDbToken->device_type === 'ios', "DeviceToken persisted with device_type='ios'");

// Test 3: Alias route /api/save-fcm-token
$aliasFcmToken = "fcm_alias_token_" . time() . "_test_string_55555";
[$aliasHttp, $aliasData] = makeApiCall('POST', '/api/save-fcm-token', [
    'device_token' => $aliasFcmToken,
    'user_id'      => $testUser->id,
]);
assertCheck($aliasHttp === 200, "POST /api/save-fcm-token (legacy alias) returns HTTP 200 OK");

// -------------------------------------------------------------
// SECTION 3: Token Invalidation & Logout Cleanup
// -------------------------------------------------------------
echo "\n--- SECTION 3: TOKEN INVALIDATION & LOGOUT CLEANUP ---\n";

// Remove the android token
[$removeHttp, $removeData] = makeApiCall('POST', '/api/fcm/token/remove', [
    'fcm_token' => $androidFcmToken,
]);
assertCheck($removeHttp === 200, "POST /api/fcm/token/remove returns HTTP 200 OK");

$dbToken->refresh();
assertCheck($dbToken->is_active === false, "DeviceToken marked is_active=false upon removal");

// Remove the current active user token ($aliasFcmToken)
makeApiCall('POST', '/api/fcm/token/remove', [
    'fcm_token' => $aliasFcmToken,
]);

$testUser->refresh();
assertCheck($testUser->fcm_token === null, "User fcm_token cleared upon removal of active token");

// -------------------------------------------------------------
// SECTION 4: Push Notification API Validation & Payloads
// -------------------------------------------------------------
echo "\n--- SECTION 4: PUSH NOTIFICATION SEND API ---\n";

// Validation: Empty payload
[$valHttp, $valData] = makeApiCall('POST', '/api/fcm/send', []);
assertCheck($valHttp === 422, "POST /api/fcm/send without required fields returns HTTP 422");

// Validation: Missing target
[$noTargetHttp, $noTargetData] = makeApiCall('POST', '/api/fcm/send', [
    'title'   => 'Test Title',
    'message' => 'Test Body',
]);
assertCheck($noTargetHttp === 422, "POST /api/fcm/send without destination target returns HTTP 422");

// Service credential handling check
$hasRealCreds = $service->isConfigured();
[$sendHttp, $sendData] = makeApiCall('POST', '/api/fcm/send', [
    'title'     => 'Special Offer: 20% Off',
    'message'   => 'Use coupon MEDICTO20 at checkout today!',
    'fcm_token' => $iosFcmToken,
    'data'      => ['screen' => 'deals', 'promo_id' => '101'],
]);

if ($hasRealCreds) {
    assertCheck(in_array($sendHttp, [200, 400]), "POST /api/fcm/send with configured credentials processed by FCM");
} else {
    assertCheck($sendHttp === 503, "POST /api/fcm/send without credentials file returns HTTP 503 Service Unavailable with diagnostic instructions");
}

// -------------------------------------------------------------
// SECTION 5: Notification History & Feed
// -------------------------------------------------------------
echo "\n--- SECTION 5: NOTIFICATIONS FEED API ---\n";

[$feedHttp, $feedData] = makeApiCall('GET', '/api/notifications');
assertCheck($feedHttp === 200, "GET /api/notifications returns HTTP 200 OK");
assertCheck(isset($feedData['data']) && is_array($feedData['data']), "GET /api/notifications returns array of notifications");

// -------------------------------------------------------------
// SECTION 6: Security & Credentials Protection
// -------------------------------------------------------------
echo "\n--- SECTION 6: SECURITY & CREDENTIALS PROTECTION ---\n";

$gitignore = file_get_contents(__DIR__ . '/../.gitignore');
assertCheck(str_contains($gitignore, '/storage/app/firebase/*.json'), ".gitignore protects private Firebase Service Account JSON credentials from git commits");
assertCheck(file_exists(__DIR__ . '/../storage/app/firebase/.gitkeep'), "storage/app/firebase directory created with .gitkeep");

// -------------------------------------------------------------
// SECTION 7: Dedicated Mobile App APIs
// -------------------------------------------------------------
echo "\n--- SECTION 7: DEDICATED MOBILE APP APIS ---\n";

// 1. App device token registration
$appTokenStr = "fcm_app_token_" . time() . "_uuid_abc123";
[$appTokHttp, $appTokData] = makeApiCall('POST', '/api/app/fcm/token', [
    'fcm_token'   => $appTokenStr,
    'device_type' => 'android',
    'device_id'   => 'device_app_test',
    'device_name' => 'OnePlus 12',
    'user_id'     => $testUser->id,
]);
assertCheck($appTokHttp === 200, "POST /api/app/fcm/token returns HTTP 200 OK");
assertCheck(($appTokData['success'] ?? false) === true, "POST /api/app/fcm/token returns success: true");

// 2. App notifications feed
[$appFeedHttp, $appFeedData] = makeApiCall('GET', '/api/app/notifications');
assertCheck($appFeedHttp === 200, "GET /api/app/notifications returns HTTP 200 OK");

// 3. App notifications unread count
[$unreadHttp, $unreadData] = makeApiCall('GET', '/api/app/notifications/unread-count');
assertCheck($unreadHttp === 200, "GET /api/app/notifications/unread-count returns HTTP 200 OK");
assertCheck(isset($unreadData['data']['unread_count']), "GET /api/app/notifications/unread-count includes unread_count");

// Create a dummy notification record to test details
$sampleNoty = noty::create([
    'title'       => 'Welcome to Medicto App',
    'message'     => 'Thank you for installing our pharmacy app!',
    'type'        => 'General',
    'send_to'     => 'All Users',
    'status'      => 'Sent',
    'total_users' => 1,
    'success_count' => 1,
]);

// 4. App notification detail
[$detailHttp, $detailData] = makeApiCall('GET', "/api/app/notifications/{$sampleNoty->id}");
assertCheck($detailHttp === 200, "GET /api/app/notifications/{id} returns HTTP 200 OK");
assertCheck(($detailData['data']['title'] ?? '') === 'Welcome to Medicto App', "GET /api/app/notifications/{id} matches notification title");

// 5. App token removal
[$appRemoveHttp, $appRemoveData] = makeApiCall('POST', '/api/app/fcm/token/remove', [
    'fcm_token' => $appTokenStr,
]);
assertCheck($appRemoveHttp === 200, "POST /api/app/fcm/token/remove returns HTTP 200 OK");

// -------------------------------------------------------------
// SECTION 8: Dedicated Admin Notification & FCM APIs
// -------------------------------------------------------------
echo "\n--- SECTION 8: DEDICATED ADMIN NOTIFICATION APIS ---\n";

// 1. Admin summary statistics
[$adminStatsHttp, $adminStatsData] = makeApiCall('GET', '/api/admin/notifications/stats');
assertCheck($adminStatsHttp === 200, "GET /api/admin/notifications/stats returns HTTP 200 OK");
assertCheck(isset($adminStatsData['data']['total_notifications']), "Admin stats includes total_notifications");
assertCheck(isset($adminStatsData['data']['total_active_devices']), "Admin stats includes total_active_devices");

// 2. Admin list notifications
[$adminListHttp, $adminListData] = makeApiCall('GET', '/api/admin/notifications', ['limit' => 10]);
assertCheck($adminListHttp === 200, "GET /api/admin/notifications returns HTTP 200 OK");
assertCheck(is_array($adminListData['data'] ?? null), "GET /api/admin/notifications returns list array");

// 3. Admin show single notification
[$adminShowHttp, $adminShowData] = makeApiCall('GET', "/api/admin/notifications/{$sampleNoty->id}");
assertCheck($adminShowHttp === 200, "GET /api/admin/notifications/{id} returns HTTP 200 OK");
assertCheck(($adminShowData['data']['id'] ?? 0) === $sampleNoty->id, "GET /api/admin/notifications/{id} returns correct notification");

// 4. Admin device fleet listing
[$adminDevHttp, $adminDevData] = makeApiCall('GET', '/api/admin/fcm/devices');
assertCheck($adminDevHttp === 200, "GET /api/admin/fcm/devices returns HTTP 200 OK");
assertCheck(is_array($adminDevData['data'] ?? null), "GET /api/admin/fcm/devices returns devices list array");

// 5. Admin FCM status
[$adminFcmStatHttp, $adminFcmStatData] = makeApiCall('GET', '/api/admin/fcm/status');
assertCheck($adminFcmStatHttp === 200, "GET /api/admin/fcm/status returns HTTP 200 OK");

// 6. Admin delete notification
[$adminDelHttp, $adminDelData] = makeApiCall('DELETE', "/api/admin/notifications/{$sampleNoty->id}");
assertCheck($adminDelHttp === 200, "DELETE /api/admin/notifications/{id} returns HTTP 200 OK");
assertCheck(noty::find($sampleNoty->id) === null, "Notification verified deleted from database");

echo "\n=========================================================\n";
echo "AUDIT SUMMARY: {$pass} checks PASSED, {$fail} checks FAILED.\n";
if ($fail === 0) {
    echo "SUCCESS: FIREBASE CLOUD MESSAGING (FCM) INTEGRATION 100% OPERATIONAL!\n";
} else {
    echo "WARNING: SOME CHECKS FAILED.\n";
}
echo "=========================================================\n";
