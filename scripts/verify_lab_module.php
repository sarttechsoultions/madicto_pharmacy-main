<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Lab;
use App\Models\LabTest;
use App\Models\LabTestCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

echo "=== VERIFYING LAB MODULE BACKEND & REST APIS ===\n\n";

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

// 1. Database table records check
$labsCount = Lab::count();
$activeLabsCount = Lab::where('status', 'active')->count();
echo "Total Labs in DB: {$labsCount} (Active: {$activeLabsCount})\n";
assertCheck($labsCount > 0, "Laboratories exist in database");

$categoriesCount = LabTestCategory::count();
$activeCategoriesCount = LabTestCategory::where('status', 'active')->count();
echo "Total Categories in DB: {$categoriesCount} (Active: {$activeCategoriesCount})\n";
assertCheck($categoriesCount > 0, "Lab test categories exist in database");

$testsCount = LabTest::count();
$activeTestsCount = LabTest::where('status', 'active')->count();
echo "Total Tests in DB: {$testsCount} (Active: {$activeTestsCount})\n\n";
assertCheck($testsCount > 0, "Lab tests exist in database");

// Helper to dispatch request through Laravel router
function makeApiCall($method, $uri, $params = []) {
    global $app;
    if (!empty($params) && strtoupper($method) === 'GET') {
        $uri .= (strpos($uri, '?') === false ? '?' : '&') . http_build_query($params);
        $params = [];
    }
    $request = Request::create($uri, $method, $params, [], [], ['HTTP_ACCEPT' => 'application/json']);
    $app->instance('request', $request);
    $response = Route::dispatch($request);
    $content = json_decode($response->getContent(), true);
    return [$response->getStatusCode(), $content];
}

// 2. API 1: GET /api/labs
list($status, $json) = makeApiCall('GET', '/api/labs');
assertCheck($status === 200 && ($json['success'] ?? false) === true && is_array($json['data']), "API 1: GET /api/labs returns 200 with active labs");

// 3. API 2: GET /api/lab-test-categories
list($status, $json) = makeApiCall('GET', '/api/lab-test-categories');
assertCheck($status === 200 && ($json['success'] ?? false) === true && is_array($json['data']), "API 2: GET /api/lab-test-categories returns 200 with categories");

// 4. API 3: GET /api/lab-tests
list($status, $json) = makeApiCall('GET', '/api/lab-tests');
assertCheck($status === 200 && ($json['success'] ?? false) === true && isset($json['pagination']), "API 3: GET /api/lab-tests returns 200 with paginated tests");

// 5. API 4: GET /api/labs/{id}/tests
$firstLab = Lab::where('status', 'active')->first();
if ($firstLab) {
    list($status, $json) = makeApiCall('GET', "/api/labs/{$firstLab->id}/tests");
    assertCheck($status === 200 && ($json['success'] ?? false) === true && is_array($json['data']), "API 4: GET /api/labs/{$firstLab->id}/tests returns tests for lab");
}

// 6. API 5: GET /api/lab-tests/{id}
$firstTest = LabTest::where('status', 'active')->first();
if ($firstTest) {
    list($status, $json) = makeApiCall('GET', "/api/lab-tests/{$firstTest->id}");
    assertCheck($status === 200 && ($json['success'] ?? false) === true && ($json['data']['id'] ?? 0) === $firstTest->id, "API 5: GET /api/lab-tests/{$firstTest->id} returns test details");
}

// 7. API 6: GET /api/lab-tests?search=cbc
list($status, $json) = makeApiCall('GET', '/api/lab-tests', ['search' => 'cbc']);
assertCheck($status === 200 && ($json['success'] ?? false) === true && count($json['data']) > 0, "API 6 (Search): GET /api/lab-tests?search=cbc returns CBC test");

// 8. API 7: GET /api/lab-tests?category_id=1
$firstCat = LabTestCategory::where('status', 'active')->first();
if ($firstCat) {
    list($status, $json) = makeApiCall('GET', '/api/lab-tests', ['category_id' => $firstCat->id]);
    assertCheck($status === 200 && ($json['success'] ?? false) === true, "API 7 (Category Filter): GET /api/lab-tests?category_id={$firstCat->id} succeeds");
}

// 9. API 8: GET /api/lab-tests?lab_id=1
if ($firstLab) {
    list($status, $json) = makeApiCall('GET', '/api/lab-tests', ['lab_id' => $firstLab->id]);
    assertCheck($status === 200 && ($json['success'] ?? false) === true, "API 8 (Lab Filter): GET /api/lab-tests?lab_id={$firstLab->id} succeeds");
}

// 10. API 9: GET /api/lab-tests/{id}/price
if ($firstTest) {
    list($status, $json) = makeApiCall('GET', "/api/lab-tests/{$firstTest->id}/price");
    assertCheck(
        $status === 200 &&
        ($json['success'] ?? false) === true &&
        isset($json['data']['effective_price']) &&
        isset($json['data']['currency']),
        "API 9: GET /api/lab-tests/{$firstTest->id}/price returns dynamic pricing"
    );
}

// 11. Inactive filtering test
$tempInactive = LabTest::create([
    'test_name' => 'Automated Inactive Filter Test ' . uniqid(),
    'lab_id' => $firstLab->id,
    'category_id' => $firstCat->id,
    'price' => 500,
    'status' => 'inactive'
]);
list($status, $json) = makeApiCall('GET', '/api/lab-tests', ['search' => $tempInactive->test_name]);
assertCheck($status === 200 && count($json['data']) === 0, "Public API correctly excludes inactive tests from search results");
list($status, $json) = makeApiCall('GET', "/api/lab-tests/{$tempInactive->id}");
assertCheck($status === 404, "Public API returns 404 when directly accessing inactive test");
$tempInactive->forceDelete();

// 12. Admin Route Names check
$routeList = [
    'admin.lab.dashboard',
    'admin.lab.setup',
    'admin.lab.store',
    'admin.lab.update',
    'admin.lab.destroy',
    'admin.lab.toggleStatus',
    'admin.lab.tests',
    'admin.lab.tests.store',
    'admin.lab.tests.update',
    'admin.lab.tests.destroy',
    'admin.lab.tests.toggleStatus',
    'admin.lab.tests.pricing',
    'admin.lab.categories',
    'admin.lab.categories.store',
    'admin.lab.categories.update',
    'admin.lab.categories.destroy',
    'admin.lab.categories.toggleStatus',
    'admin.lab.orders',
];

$allRoutesRegistered = true;
foreach ($routeList as $routeName) {
    if (!Route::has($routeName)) {
        echo "Missing route: {$routeName}\n";
        $allRoutesRegistered = false;
    }
}
assertCheck($allRoutesRegistered, "All 18 Admin Lab routes are registered in Laravel router");

// 13. Admin Blade Views Rendering Check (with authenticated mock user)
$adminUser = \App\Models\User::first();
if (!$adminUser) {
    $adminUser = new \App\Models\User(['name' => 'Admin User', 'email' => 'admin@medicto.com']);
}
auth()->login($adminUser);

$viewRoutes = [
    '/lab/dashboard' => 'Dashboard View',
    '/lab/setup'     => 'Lab Setup View',
    '/lab/tests'     => 'Manage Lab Tests View',
    '/lab/categories'=> 'Lab Categories View',
    '/lab/orders'    => 'Lab Orders View',
];

foreach ($viewRoutes as $uri => $label) {
    try {
        $req = Request::create($uri, 'GET');
        $app->instance('request', $req);
        $res = Route::dispatch($req);
        $statusCode = $res->getStatusCode();
        assertCheck($statusCode === 200, "Admin View: {$label} ({$uri}) renders successfully with status 200");
    } catch (\Throwable $e) {
        assertCheck(false, "Admin View: {$label} encountered error: " . $e->getMessage());
    }
}

echo "\n============================================\n";
echo "SUMMARY: {$pass} passed, {$fail} failed.\n";
if ($fail === 0) {
    echo "ALL TESTS PASSED SUCCESSFULLY!\n";
} else {
    echo "SOME CHECKS FAILED!\n";
}
