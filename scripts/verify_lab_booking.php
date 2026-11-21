<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Lab;
use App\Models\LabTest;
use App\Models\LabTestCategory;
use App\Models\LabTestBooking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

echo "=========================================================\n";
echo "  MEDICTO PHARMACY - LAB TEST MANAGEMENT REDESIGN AUDIT\n";
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
        $data = json_decode($response->getContent(), true);
    } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
        $statusCode = 404;
        $data = ['message' => 'The route ' . $request->path() . ' could not be found.'];
    } catch (\Exception $e) {
        $statusCode = 500;
        $data = ['message' => $e->getMessage()];
    }

    return [$statusCode, $data];
}

// ---------------------------------------------------------------------
// 1. ENSURE TEST FIXTURES
// ---------------------------------------------------------------------
$test = LabTest::with(['lab', 'category'])->where('status', 'active')->first();
if (!$test) {
    $lab = Lab::firstOrCreate(['name' => 'City Diagnostic Lab'], [
        'city' => 'Mumbai',
        'status' => 'active',
        'contact_number' => '9876543210'
    ]);
    $cat = LabTestCategory::firstOrCreate(['name' => 'Blood Studies'], ['status' => 'active']);
    $test = LabTest::firstOrCreate(['test_name' => 'Complete Blood Count (CBC)'], [
        'lab_id' => $lab->id,
        'category_id' => $cat->id,
        'price' => 350.00,
        'discount_price' => 299.00,
        'sample_type' => 'Blood',
        'report_delivery_time' => '24 Hours',
        'status' => 'active',
    ]);
}

$adminUser = User::first();
$customerUser = User::skip(1)->first() ?? $adminUser;

// ---------------------------------------------------------------------
// 2. MOBILE APP BOOKING CREATION
// ---------------------------------------------------------------------
echo "--- SECTION 1: MOBILE APP BOOKING INTEGRATION ---\n";
$futureDate = date('Y-m-d', strtotime('+2 days'));
list($status, $json) = makeApiCall('POST', '/api/lab-test-bookings', [
    'lab_test_id' => $test->id,
    'customer_name' => 'Pooja Hegde',
    'customer_phone' => '9822233344',
    'customer_email' => 'pooja@example.com',
    'address' => 'Flat 502, Palm Beach Residency, Juhu',
    'city' => 'Mumbai',
    'state' => 'Maharashtra',
    'pincode' => '400049',
    'booking_date' => $futureDate,
    'booking_time' => '08:00 AM - 09:00 AM',
    'additional_instructions' => 'Patient fasting since 10 PM',
], [], $customerUser);

$createdBookingId = $json['data']['booking_id'] ?? null;
$createdDbId = $json['data']['id'] ?? null;

assertCheck(
    $status === 201 && !empty($createdBookingId) && ($json['data']['status'] ?? '') === 'Pending',
    "Mobile API: POST /api/lab-test-bookings successfully placed booking #{$createdBookingId} with initial status Pending"
);

// ---------------------------------------------------------------------
// 3. ADMIN PANEL PAGES VERIFICATION
// ---------------------------------------------------------------------
echo "\n--- SECTION 2: ADMIN PANEL - NEW LAB TEST MANAGEMENT PAGES ---\n";

$adminPages = [
    '/lab/bookings/dashboard' => 'Lab Test Bookings Dashboard (Summary Cards)',
    '/lab/bookings'           => 'All Bookings Management Table',
    "/lab/bookings/{$createdDbId}" => 'Booking Details Page',
    '/lab/bookings/calendar'  => 'Booking Schedule Calendar',
    '/lab/bookings/reports'   => 'Booking Reports & Analytics',
    '/lab/orders'             => 'Orders Alias Route',
    '/lab/dashboard'          => 'Dashboard Alias Route',
];

foreach ($adminPages as $uri => $label) {
    $req = Request::create($uri, 'GET');
    $req->setUserResolver(function () use ($adminUser) {
        return $adminUser;
    });
    auth()->guard('web')->setUser($adminUser);
    $app->instance('request', $req);
    $res = Route::dispatch($req);
    assertCheck($res->getStatusCode() === 200, "Admin Page: {$label} ({$uri}) returns HTTP 200 OK");
}

// ---------------------------------------------------------------------
// 4. ADMIN BOOKING STATUS LIFECYCLE MANAGEMENT
// ---------------------------------------------------------------------
echo "\n--- SECTION 3: ADMIN BOOKING STATUS LIFECYCLE ---\n";

$booking = LabTestBooking::find($createdDbId);
$controller = app(\App\Http\Controllers\LabController::class);

// Action A: Confirm Booking
$req = Request::create("/lab/bookings/{$createdDbId}/confirm", 'POST');
$req->setUserResolver(fn() => $adminUser);
$res = $controller->confirmBooking($createdDbId);
$booking->refresh();
assertCheck($booking->status === 'Confirmed', "Admin Action: Confirm Booking sets status to 'Confirmed'");

// Action B: Mobile API Immediate Verification of Confirmed Status
list($status, $json) = makeApiCall('GET', "/api/lab-test-bookings/{$createdDbId}", [], [], $customerUser);
assertCheck(
    $status === 200 && ($json['data']['status'] ?? '') === 'Confirmed',
    "Mobile API Sync: GET /api/lab-test-bookings/{id} immediately reflects 'Confirmed' status"
);

// Action C: Update to Sample Collection Scheduled
$req = Request::create("/lab/bookings/{$createdDbId}/status", 'POST', ['status' => 'Sample Collection Scheduled']);
$req->headers->set('Accept', 'application/json');
$controller->updateBookingStatus($req, $createdDbId);
$booking->refresh();
assertCheck($booking->status === 'Sample Collection Scheduled', "Admin Status: Updated to 'Sample Collection Scheduled'");

// Action D: Update to Sample Collected
$req = Request::create("/lab/bookings/{$createdDbId}/status", 'POST', ['status' => 'Sample Collected']);
$req->headers->set('Accept', 'application/json');
$controller->updateBookingStatus($req, $createdDbId);
$booking->refresh();
assertCheck($booking->status === 'Sample Collected', "Admin Status: Updated to 'Sample Collected'");

// Action E: Update to Completed
$req = Request::create("/lab/bookings/{$createdDbId}/status", 'POST', ['status' => 'Completed', 'payment_status' => 'Paid']);
$req->headers->set('Accept', 'application/json');
$controller->updateBookingStatus($req, $createdDbId);
$booking->refresh();
assertCheck($booking->status === 'Completed' && $booking->payment_status === 'Paid', "Admin Status: Updated to 'Completed' & Payment 'Paid'");

// Action F: Mobile API Immediate Verification of Completed Status
list($status, $json) = makeApiCall('GET', "/api/lab-test-bookings/{$createdDbId}", [], [], $customerUser);
assertCheck(
    $status === 200 && ($json['data']['status'] ?? '') === 'Completed',
    "Mobile API Sync: GET /api/lab-test-bookings/{id} immediately reflects 'Completed' status"
);

// Action G: Cancel Action Test on a separate booking
$cancelTestBooking = LabTestBooking::create([
    'booking_id' => 'LB-TEST-CANCEL-' . uniqid(),
    'user_id' => $customerUser->id,
    'lab_test_id' => $test->id,
    'lab_id' => $test->lab_id,
    'customer_name' => 'Admin Cancel Test',
    'customer_phone' => '9988776655',
    'address' => 'Test Address',
    'city' => 'Mumbai',
    'state' => 'Maharashtra',
    'pincode' => '400001',
    'booking_date' => date('Y-m-d', strtotime('+4 days')),
    'booking_time' => '09:00 AM - 10:00 AM',
    'total_amount' => 299.00,
    'status' => 'Pending',
]);

$req = Request::create("/lab/bookings/{$cancelTestBooking->id}/cancel", 'POST', ['reason' => 'Patient requested rescheduling']);
$controller->quickCancelBooking($req, $cancelTestBooking->id);
$cancelTestBooking->refresh();
assertCheck(
    $cancelTestBooking->status === 'Cancelled' && $cancelTestBooking->cancellation_reason === 'Patient requested rescheduling',
    "Admin Action: Cancel Booking sets status to 'Cancelled' with reason"
);

// ---------------------------------------------------------------------
// 5. OLD APIS REMAIN 404
// ---------------------------------------------------------------------
echo "\n--- SECTION 4: OLD APIS PERSISTENT 404 VERIFICATION ---\n";
$oldApis = ['/api/labs', '/api/lab-test-categories', '/api/lab-tests'];
foreach ($oldApis as $endpoint) {
    list($status, $json) = makeApiCall('GET', $endpoint);
    assertCheck($status === 404, "Old API: {$endpoint} correctly returns HTTP 404");
}

echo "\n=========================================================\n";
echo "AUDIT SUMMARY: {$pass} checks PASSED, {$fail} checks FAILED.\n";
if ($fail === 0) {
    echo "SUCCESS: ADMIN PANEL LAB TEST REDESIGN & SYNC 100% OPERATIONAL!\n";
} else {
    echo "ATTENTION: SOME AUDIT CHECKS FAILED!\n";
}
echo "=========================================================\n";
