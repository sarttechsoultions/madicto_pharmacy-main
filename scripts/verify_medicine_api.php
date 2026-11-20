<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\medicineModel;
use App\Models\CategoryModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;

echo "=========================================================\n";
echo "    MEDICTO PHARMACY - MEDICINE API COMPREHENSIVE AUDIT\n";
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

function makeApiCall($method, $uri, $params = [], $files = []) {
    global $app;
    $content = null;
    $parameters = $params;

    if (!empty($params) && strtoupper($method) === 'GET') {
        $uri .= (strpos($uri, '?') === false ? '?' : '&') . http_build_query($params);
        $parameters = [];
    }

    $server = ['HTTP_ACCEPT' => 'application/json'];
    $request = Request::create($uri, strtoupper($method), $parameters, [], $files, $server);
    $app->instance('request', $request);

    $response = Route::dispatch($request);
    $statusCode = $response->getStatusCode();
    $data = json_decode($response->getContent(), true);

    return [$statusCode, $data];
}

// 1. Authenticate an admin user for sanctum endpoints
$user = User::first();
if (!$user) {
    $user = User::create([
        'name' => 'Audit Admin',
        'email' => 'admin_audit@medicto.com',
        'password' => bcrypt('password123'),
        'role' => 'admin',
    ]);
}
auth()->login($user);

// 2. Test GET /api/medicines (Listing)
list($status, $json) = makeApiCall('GET', '/api/medicines', ['per_page' => 10]);
if ($status !== 200 || !isset($json['data'])) {
    echo "Check 2 failed. Status: $status\n";
    echo "Response: " . print_r($json, true) . "\n";
}
assertCheck(
    $status === 200 &&
    ($json['success'] ?? false) === true &&
    isset($json['data']) &&
    is_array($json['data']) &&
    count($json['data']) > 0 &&
    isset($json['pagination']['total']),
    "API 1: GET /api/medicines returns 200 with paginated array of items (Total: " . ($json['pagination']['total'] ?? 0) . ")"
);

// Check that items contain image_url and category
$firstItem = $json['data'][0] ?? null;
assertCheck(
    $firstItem !== null &&
    array_key_exists('image_url', $firstItem) &&
    array_key_exists('final_price', $firstItem) &&
    array_key_exists('is_in_stock', $firstItem),
    "API 1 Schema: Medicines include dynamic accessors (image_url, final_price, is_in_stock)"
);

// 3. Test POST /api/medicines (Listing with body filter)
list($status, $json) = makeApiCall('POST', '/api/medicines', ['category_id' => 7, 'per_page' => 5]);
assertCheck(
    $status === 200 &&
    ($json['success'] ?? false) === true &&
    count($json['data']) === 5,
    "API 2: POST /api/medicines with category filter and per_page=5 returns exactly 5 items"
);

// 4. Test GET /api/medicines/{id} (Medicine details)
$sampleMed = medicineModel::where('category_id', 7)->first();
if ($sampleMed) {
    list($status, $json) = makeApiCall('GET', '/api/medicines/' . $sampleMed->id);
    assertCheck(
        $status === 200 &&
        ($json['success'] ?? false) === true &&
        ($json['data']['id'] ?? 0) === $sampleMed->id &&
        isset($json['data']['ratings_breakdown']),
        "API 3: GET /api/medicines/{id} returns full medicine details with ratings_breakdown for ID {$sampleMed->id}"
    );
}

// 5. Test POST /api/medicines with { "id": ... } (Alternative mobile details request)
if ($sampleMed) {
    list($status, $json) = makeApiCall('POST', '/api/medicines', ['id' => $sampleMed->id]);
    assertCheck(
        $status === 200 &&
        ($json['success'] ?? false) === true &&
        ($json['data']['id'] ?? 0) === $sampleMed->id,
        "API 4: POST /api/medicines with {'id': {$sampleMed->id}} returns single medicine details"
    );
}

// 6. Test GET /api/medicines/99999999 (Not Found)
list($status, $json) = makeApiCall('GET', '/api/medicines/99999999');
assertCheck(
    $status === 404 &&
    ($json['success'] ?? true) === false,
    "API 5: GET /api/medicines/99999999 correctly returns 404 Not Found"
);

// 7. Test Search by Manufacturer (Brand Name) across entire 17,275 database
list($status, $json) = makeApiCall('GET', '/api/medicines-search', ['search' => 'Cipla']);
$mfrCount = $json['pagination']['total'] ?? count($json['data'] ?? []);
assertCheck(
    $status === 200 &&
    ($json['success'] ?? false) === true &&
    $mfrCount > 200,
    "API 6: GET /api/medicines-search?search=Cipla searches entire database across brand name (Found: {$mfrCount} Cipla medicines)"
);

// 8. Test Search by Medicine Name
list($status, $json) = makeApiCall('GET', '/api/medicines-search', ['search' => 'Paracip']);
$paraCount = $json['pagination']['total'] ?? count($json['data'] ?? []);
assertCheck(
    $status === 200 &&
    ($json['success'] ?? false) === true &&
    $paraCount > 0,
    "API 7: GET /api/medicines-search?search=Paracip matches medicine name (Found: {$paraCount})"
);

// 9. Test Search Empty State (Graceful empty response without errors)
list($status, $json) = makeApiCall('GET', '/api/medicines-search', ['search' => 'xyzunlikelynonexistent999']);
assertCheck(
    $status === 200 &&
    ($json['success'] ?? false) === true &&
    count($json['data']) === 0 &&
    ($json['pagination']['total'] ?? 0) === 0,
    "API 8: GET /api/medicines-search empty state returns 200 OK with empty array [] and clean message"
);

// 10. Test Related Medicines API
if ($sampleMed) {
    list($status, $json) = makeApiCall('GET', "/api/medicines/{$sampleMed->id}/related");
    assertCheck(
        $status === 200 &&
        ($json['success'] ?? false) === true &&
        is_array($json['data']),
        "API 9: GET /api/medicines/{id}/related returns related medicines"
    );
}

// 11. Test Trending Medicines API
list($status, $json) = makeApiCall('GET', '/api/trending-medicine');
assertCheck(
    $status === 200 &&
    ($json['success'] ?? false) === true &&
    isset($json['data']),
    "API 10: GET /api/trending-medicine returns trending products list"
);

// 12. Test Deals of the Day API
list($status, $json) = makeApiCall('GET', '/api/deals-of-day');
assertCheck(
    $status === 200 &&
    ($json['success'] ?? false) === true &&
    isset($json['data']),
    "API 11: GET /api/deals-of-day returns deals list"
);

// 13. Test Create Medicine API (POST /api/medicines/create)
$testUniqueName = 'API Audit Test Med ' . uniqid();
list($status, $json) = makeApiCall('POST', '/api/medicines/create', [
    'name' => $testUniqueName,
    'price' => 199.50,
    'category_id' => 7,
    'manufacturer' => 'Medicto Labs Ltd',
    'quantity' => 50,
    'stock' => 50,
    'unit_type' => 'Tablets',
    'pack_size' => '10 Tabs',
    'description' => 'Tested via automated audit',
    'status' => 'In Stock',
]);
$createdId = $json['data']['id'] ?? null;
assertCheck(
    $status === 201 &&
    ($json['success'] ?? false) === true &&
    $createdId !== null,
    "API 12: POST /api/medicines/create successfully creates new medicine record (ID: {$createdId})"
);

// 14. Test Update Medicine API (PUT /api/medicines/{id})
if ($createdId) {
    list($status, $json) = makeApiCall('PUT', "/api/medicines/{$createdId}", [
        'name' => $testUniqueName . ' (Updated)',
        'price' => 249.00,
        'quantity' => 45,
        'category_id' => 7,
        'description' => 'Updated via automated audit',
    ]);
    assertCheck(
        $status === 200 &&
        ($json['success'] ?? false) === true &&
        (float)$json['data']['price'] === 249.0,
        "API 13: PUT /api/medicines/{id} successfully updates medicine (New price: 249.00)"
    );
}

// 15. Test Toggle Active/Inactive Status API and Filtering
if ($createdId) {
    // Toggle to Inactive
    list($status, $json) = makeApiCall('POST', "/api/medicines/{$createdId}/toggle-status");
    assertCheck(
        $status === 200 &&
        ($json['data']['status'] ?? '') === 'Inactive',
        "API 14: POST /api/medicines/{id}/toggle-status sets status to Inactive"
    );

    // Verify inactive medicine is excluded from public search
    list($status, $json) = makeApiCall('GET', '/api/medicines-search', ['search' => $testUniqueName]);
    assertCheck(
        $status === 200 &&
        count($json['data']) === 0,
        "API 14 Filter: Inactive medicine is automatically excluded from public search results"
    );

    // Toggle back to Active
    list($status, $json) = makeApiCall('POST', "/api/medicines/{$createdId}/toggle-status");
    assertCheck(
        $status === 200 &&
        ($json['data']['status'] ?? '') !== 'Inactive',
        "API 14 Restore: POST /api/medicines/{id}/toggle-status reactivates medicine"
    );
}

// 16. Test Image Upload API (POST /api/medicines/{id}/image)
if ($createdId) {
    $tempImagePath = sys_get_temp_dir() . '/test_med_' . uniqid() . '.png';
    $img = imagecreatetruecolor(100, 100);
    imagepng($img, $tempImagePath);
    imagedestroy($img);

    $uploadedFile = new UploadedFile($tempImagePath, 'test_med.png', 'image/png', null, true);

    list($status, $json) = makeApiCall('POST', "/api/medicines/{$createdId}/image", [], ['image' => $uploadedFile]);
    assertCheck(
        $status === 200 &&
        ($json['success'] ?? false) === true &&
        !empty($json['image_url']),
        "API 15: POST /api/medicines/{id}/image successfully uploads main image and returns URL"
    );

    if (file_exists($tempImagePath)) {
        @unlink($tempImagePath);
    }
}

// 17. Test Delete Medicine API (DELETE /api/medicines/{id})
if ($createdId) {
    list($status, $json) = makeApiCall('DELETE', "/api/medicines/{$createdId}");
    assertCheck(
        $status === 200 &&
        ($json['success'] ?? false) === true,
        "API 16: DELETE /api/medicines/{id} successfully deletes medicine record"
    );

    // Verify it is gone
    $checkDeleted = medicineModel::find($createdId);
    assertCheck($checkDeleted === null, "API 16 Verification: Medicine record no longer exists in database");
}

// 18. Test Admin Web Panel Routes (Ensure no web regression)
$webRoutes = [
    '/medicine'              => 'Admin Medicine Inventory List',
    '/medicine/3/details'    => 'Admin Medicine Details View',
];
foreach ($webRoutes as $uri => $label) {
    $req = Request::create($uri, 'GET');
    $app->instance('request', $req);
    $res = Route::dispatch($req);
    assertCheck(
        $res->getStatusCode() === 200,
        "Admin Web Route: {$label} ({$uri}) returns status 200 OK"
    );
}

echo "\n=========================================================\n";
echo "AUDIT SUMMARY: {$pass} checks PASSED, {$fail} checks FAILED.\n";
if ($fail === 0) {
    echo "SUCCESS: ALL MEDICINE APIS AND WORKFLOWS FUNCTIONING PERFECTLY!\n";
} else {
    echo "ATTENTION: SOME ISSUES FOUND!\n";
}
echo "=========================================================\n";
