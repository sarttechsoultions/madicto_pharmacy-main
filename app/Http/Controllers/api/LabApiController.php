<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use App\Models\LabTest;
use App\Models\LabTestCategory;
use Illuminate\Http\Request;

class LabApiController extends Controller
{
    /**
     * Format a single LabTest model into the exact structure requested for mobile apps.
     */
    protected function formatLabTest(LabTest $test): array
    {
        return [
            'id'                       => (int)$test->id,
            'test_name'                => (string)$test->test_name,
            'test_code'                => (string)($test->test_code ?? ''),
            'lab_id'                   => (int)$test->lab_id,
            'lab_name'                 => (string)($test->lab->name ?? ''),
            'category_id'              => (int)$test->category_id,
            'category_name'            => (string)($test->category->name ?? ''),
            'description'              => (string)($test->description ?? ''),
            'price'                    => (float)$test->price,
            'discount_price'           => $test->discount_price !== null ? (float)$test->discount_price : null,
            'sample_type'              => (string)($test->sample_type ?? ''),
            'report_delivery_time'     => (string)($test->report_delivery_time ?? ''),
            'preparation_instructions' => (string)($test->preparation_instructions ?? ''),
            'image'                    => $test->image ? asset($test->image) : null,
            'status'                   => (string)$test->status,
        ];
    }

    /**
     * API 1: Get All Active Labs
     * GET /api/labs
     */
    public function getLabs(Request $request)
    {
        $query = Lab::where('status', 'active')
            ->withCount(['tests' => function ($q) {
                $q->where('status', 'active');
            }]);

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('city', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        $labs = $query->orderBy('name')->get()->map(function ($lab) {
            return [
                'id'             => (int)$lab->id,
                'name'           => (string)$lab->name,
                'logo'           => $lab->logo ? asset($lab->logo) : null,
                'description'    => (string)($lab->description ?? ''),
                'contact_number' => (string)($lab->contact_number ?? ''),
                'email'          => (string)($lab->email ?? ''),
                'address'        => (string)($lab->address ?? ''),
                'city'           => (string)($lab->city ?? ''),
                'state'          => (string)($lab->state ?? ''),
                'pincode'        => (string)($lab->pincode ?? ''),
                'status'         => (string)$lab->status,
                'active_tests_count' => (int)$lab->tests_count,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Active laboratories fetched successfully',
            'data'    => $labs,
        ], 200);
    }

    /**
     * API 2: Get All Active Lab Test Categories
     * GET /api/lab-test-categories
     */
    public function getCategories(Request $request)
    {
        $categories = LabTestCategory::where('status', 'active')
            ->withCount(['tests' => function ($q) {
                $q->where('status', 'active');
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($cat) {
                return [
                    'id'                 => (int)$cat->id,
                    'name'               => (string)$cat->name,
                    'icon'               => $cat->icon ? asset($cat->icon) : null,
                    'description'        => (string)($cat->description ?? ''),
                    'status'             => (string)$cat->status,
                    'active_tests_count' => (int)$cat->tests_count,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Lab test categories fetched successfully',
            'data'    => $categories,
        ], 200);
    }

    /**
     * API 3, 6, 7, 8: Get All Active Lab Tests (Supports search, category_id, lab_id, pagination)
     * GET /api/lab-tests
     */
    public function getTests(Request $request)
    {
        // Require active test, active lab, and active category
        $query = LabTest::where('status', 'active')
            ->whereHas('lab', function ($q) {
                $q->where('status', 'active');
            })
            ->whereHas('category', function ($q) {
                $q->where('status', 'active');
            })
            ->with(['lab', 'category']);

        // Search: ?search=cbc
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('test_name', 'LIKE', "%{$search}%")
                    ->orWhere('test_code', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('sample_type', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', function ($cq) use ($search) {
                        $cq->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('lab', function ($lq) use ($search) {
                        $lq->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filter by Category: ?category_id=1
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by Lab: ?lab_id=1
        if ($request->filled('lab_id')) {
            $query->where('lab_id', $request->lab_id);
        }

        // Filter by Sample Type: ?sample_type=Blood
        if ($request->filled('sample_type')) {
            $query->where('sample_type', $request->sample_type);
        }

        // Pagination or All
        $perPage = $request->input('per_page', 20);
        $paginated = $query->latest('id')->paginate((int)$perPage);

        $formattedData = collect($paginated->items())->map(function ($test) {
            return $this->formatLabTest($test);
        });

        return response()->json([
            'success'      => true,
            'message'      => 'Lab tests fetched successfully',
            'data'         => $formattedData,
            'pagination'   => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ], 200);
    }

    /**
     * API 4: Get Active Lab Tests by Lab ID
     * GET /api/labs/{id}/tests
     */
    public function getTestsByLab(Request $request, $id)
    {
        $lab = Lab::where('id', $id)->where('status', 'active')->first();

        if (!$lab) {
            return response()->json([
                'success' => false,
                'message' => 'Laboratory not found or is currently inactive',
                'data'    => [],
            ], 404);
        }

        $tests = LabTest::where('lab_id', $id)
            ->where('status', 'active')
            ->whereHas('category', function ($q) {
                $q->where('status', 'active');
            })
            ->with(['lab', 'category'])
            ->latest()
            ->get()
            ->map(function ($test) {
                return $this->formatLabTest($test);
            });

        return response()->json([
            'success' => true,
            'message' => "Active lab tests for {$lab->name} fetched successfully",
            'lab'     => [
                'id'             => $lab->id,
                'name'           => $lab->name,
                'logo'           => $lab->logo ? asset($lab->logo) : null,
                'contact_number' => $lab->contact_number,
                'city'           => $lab->city,
            ],
            'data'    => $tests,
        ], 200);
    }

    /**
     * API 5: Get Lab Test Details
     * GET /api/lab-tests/{id}
     */
    public function getTestDetails(Request $request, $id)
    {
        $test = LabTest::where('id', $id)
            ->where('status', 'active')
            ->whereHas('lab', function ($q) {
                $q->where('status', 'active');
            })
            ->whereHas('category', function ($q) {
                $q->where('status', 'active');
            })
            ->with(['lab', 'category'])
            ->first();

        if (!$test) {
            return response()->json([
                'success' => false,
                'message' => 'Lab test not found or is currently inactive',
            ], 404);
        }

        $formatted = $this->formatLabTest($test);

        // Add additional rich details for test screen
        $formatted['lab_details'] = [
            'id'             => $test->lab->id,
            'name'           => $test->lab->name,
            'logo'           => $test->lab->logo ? asset($test->lab->logo) : null,
            'address'        => $test->lab->address,
            'city'           => $test->lab->city,
            'contact_number' => $test->lab->contact_number,
            'email'          => $test->lab->email,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Lab test details fetched successfully',
            'data'    => $formatted,
        ], 200);
    }

    /**
     * API 9: Get Lab Test Pricing
     * GET /api/lab-tests/{id}/price
     */
    public function getTestPrice(Request $request, $id)
    {
        $test = LabTest::where('id', $id)
            ->where('status', 'active')
            ->first();

        if (!$test) {
            return response()->json([
                'success' => false,
                'message' => 'Lab test not found or is currently inactive',
            ], 404);
        }

        $price = (float)$test->price;
        $discountPrice = $test->discount_price !== null ? (float)$test->discount_price : null;
        $savings = 0.00;
        $discountPercentage = 0.00;

        if ($discountPrice !== null && $discountPrice < $price && $price > 0) {
            $savings = round($price - $discountPrice, 2);
            $discountPercentage = round(($savings / $price) * 100, 2);
        }

        $finalPrice = $discountPrice !== null ? $discountPrice : $price;

        return response()->json([
            'success' => true,
            'message' => 'Lab test pricing fetched successfully',
            'data'    => [
                'id'                  => (int)$test->id,
                'test_id'             => (int)$test->id,
                'test_name'           => (string)$test->test_name,
                'test_code'           => (string)($test->test_code ?? ''),
                'lab_name'            => (string)($test->lab->name ?? ''),
                'price'               => $price,
                'mrp_price'           => $price,
                'discount_price'      => $discountPrice,
                'has_discount'        => ($discountPrice !== null && $discountPrice < $price),
                'effective_price'     => $finalPrice,
                'final_price'         => $finalPrice,
                'savings'             => $savings,
                'savings_amount'      => $savings,
                'discount_percentage' => $discountPercentage,
                'currency'            => 'INR',
                'currency_symbol'     => '₹',
            ],
        ], 200);
    }
}
