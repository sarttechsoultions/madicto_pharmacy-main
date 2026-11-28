<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\LabTest;
use App\Models\LabTestCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LabTestController extends Controller
{
    /**
     * List lab tests with search, category filter, lab filter, status filter.
     */
    public function index(Request $request)
    {
        $query = LabTest::with(['lab', 'category']);

        // Search by test name or test code
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('test_name', 'LIKE', "%{$search}%")
                    ->orWhere('test_code', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by Lab
        if ($request->filled('lab_id') && $request->lab_id !== 'all') {
            $query->where('lab_id', $request->lab_id);
        }

        // Filter by Category
        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        // Filter by Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $tests = $query->latest()->paginate(15)->withQueryString();

        $labs = Lab::orderBy('name')->get();
        $categories = LabTestCategory::orderBy('name')->get();

        $stats = [
            'total'    => LabTest::count(),
            'active'   => LabTest::where('status', 'active')->count(),
            'inactive' => LabTest::where('status', 'inactive')->count(),
        ];

        return view('admin.lab.tests', compact('tests', 'labs', 'categories', 'stats'));
    }

    /**
     * Store new lab test.
     */
    public function store(Request $request)
    {
        $request->validate([
            'test_name'                => 'required|string|max:255',
            'test_code'                => 'nullable|string|max:100',
            'lab_id'                   => 'required|exists:labs,id',
            'category_id'              => 'required|exists:lab_test_categories,id',
            'price'                    => 'required|numeric|min:0',
            'cut_price'                => 'nullable|numeric|min:0',
            'discount_percentage'      => 'nullable|numeric|min:0|max:100',
            'discount_price'           => 'nullable|numeric|min:0',
            'preparation_instructions' => 'nullable|string',
            'sample_type'              => 'nullable|string|max:100',
            'report_delivery_time'     => 'nullable|string|max:100',
            'description'              => 'nullable|string',
            'image'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'                   => 'required|in:active,inactive',
        ]);

        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_labtest_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destination = public_path('uploads/lab_tests');
                if (!File::isDirectory($destination)) {
                    File::makeDirectory($destination, 0755, true, true);
                }
                $file->move($destination, $filename);
                $imagePath = 'uploads/lab_tests/' . $filename;
            }

            // Auto-generate test code if not provided
            $testCode = $request->test_code;
            if (empty($testCode)) {
                $testCode = 'LT-' . strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $request->test_name), 0, 3)) . rand(100, 999);
            }

            $sellingPrice = (float)$request->price;
            $cutPrice = $request->filled('cut_price') ? (float)$request->cut_price : null;
            $discountPercentage = $request->filled('discount_percentage') ? (int)$request->discount_percentage : null;

            // Auto-calculate discount percentage or cut price if one is given
            if ($cutPrice && $cutPrice > $sellingPrice && ($discountPercentage === null || $discountPercentage <= 0)) {
                $discountPercentage = (int)round((($cutPrice - $sellingPrice) / $cutPrice) * 100);
            } elseif ($discountPercentage && $discountPercentage > 0 && ($cutPrice === null || $cutPrice <= $sellingPrice)) {
                $cutPrice = round($sellingPrice / (1 - ($discountPercentage / 100)), 2);
            }

            LabTest::create([
                'test_name'                => $request->test_name,
                'test_code'                => $testCode,
                'lab_id'                   => $request->lab_id,
                'category_id'              => $request->category_id,
                'description'              => $request->description,
                'price'                    => $sellingPrice,
                'cut_price'                => $cutPrice,
                'discount_percentage'      => $discountPercentage,
                'discount_price'           => $request->discount_price,
                'preparation_instructions' => $request->preparation_instructions,
                'sample_type'              => $request->sample_type,
                'report_delivery_time'     => $request->report_delivery_time,
                'image'                    => $imagePath,
                'status'                   => $request->status,
            ]);

            return redirect()->back()->with('success', 'Lab test created successfully!');
        } catch (Exception $e) {
            Log::error('Lab test store error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error creating lab test: ' . $e->getMessage());
        }
    }

    /**
     * Update existing lab test.
     */
    public function update(Request $request, $id)
    {
        $test = LabTest::findOrFail($id);

        $request->validate([
            'test_name'                => 'required|string|max:255',
            'test_code'                => 'nullable|string|max:100',
            'lab_id'                   => 'required|exists:labs,id',
            'category_id'              => 'required|exists:lab_test_categories,id',
            'price'                    => 'required|numeric|min:0',
            'cut_price'                => 'nullable|numeric|min:0',
            'discount_percentage'      => 'nullable|numeric|min:0|max:100',
            'discount_price'           => 'nullable|numeric|min:0',
            'preparation_instructions' => 'nullable|string',
            'sample_type'              => 'nullable|string|max:100',
            'report_delivery_time'     => 'nullable|string|max:100',
            'description'              => 'nullable|string',
            'image'                    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'                   => 'required|in:active,inactive',
        ]);

        try {
            if ($request->hasFile('image')) {
                if ($test->image && File::exists(public_path($test->image))) {
                    File::delete(public_path($test->image));
                }

                $file = $request->file('image');
                $filename = time() . '_labtest_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destination = public_path('uploads/lab_tests');
                if (!File::isDirectory($destination)) {
                    File::makeDirectory($destination, 0755, true, true);
                }
                $file->move($destination, $filename);
                $test->image = 'uploads/lab_tests/' . $filename;
            }

            $sellingPrice = (float)$request->price;
            $cutPrice = $request->filled('cut_price') ? (float)$request->cut_price : null;
            $discountPercentage = $request->filled('discount_percentage') ? (int)$request->discount_percentage : null;

            if ($cutPrice && $cutPrice > $sellingPrice && ($discountPercentage === null || $discountPercentage <= 0)) {
                $discountPercentage = (int)round((($cutPrice - $sellingPrice) / $cutPrice) * 100);
            } elseif ($discountPercentage && $discountPercentage > 0 && ($cutPrice === null || $cutPrice <= $sellingPrice)) {
                $cutPrice = round($sellingPrice / (1 - ($discountPercentage / 100)), 2);
            }

            $test->test_name                = $request->test_name;
            $test->test_code                = $request->test_code ?: $test->test_code;
            $test->lab_id                   = $request->lab_id;
            $test->category_id              = $request->category_id;
            $test->description              = $request->description;
            $test->price                    = $sellingPrice;
            $test->cut_price                = $cutPrice;
            $test->discount_percentage      = $discountPercentage;
            $test->discount_price           = $request->discount_price;
            $test->preparation_instructions = $request->preparation_instructions;
            $test->sample_type              = $request->sample_type;
            $test->report_delivery_time     = $request->report_delivery_time;
            $test->status                   = $request->status;
            $test->save();

            return redirect()->back()->with('success', 'Lab test updated successfully!');
        } catch (Exception $e) {
            Log::error('Lab test update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error updating lab test: ' . $e->getMessage());
        }
    }

    /**
     * Quick price update.
     */
    public function updatePricing(Request $request, $id)
    {
        $test = LabTest::findOrFail($id);

        $request->validate([
            'price'               => 'required|numeric|min:0',
            'cut_price'           => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_price'      => 'nullable|numeric|min:0',
        ]);

        $sellingPrice = (float)$request->price;
        $cutPrice = $request->filled('cut_price') ? (float)$request->cut_price : null;
        $discountPercentage = $request->filled('discount_percentage') ? (int)$request->discount_percentage : null;

        if ($cutPrice && $cutPrice > $sellingPrice && ($discountPercentage === null || $discountPercentage <= 0)) {
            $discountPercentage = (int)round((($cutPrice - $sellingPrice) / $cutPrice) * 100);
        } elseif ($discountPercentage && $discountPercentage > 0 && ($cutPrice === null || $cutPrice <= $sellingPrice)) {
            $cutPrice = round($sellingPrice / (1 - ($discountPercentage / 100)), 2);
        }

        $test->price = $sellingPrice;
        $test->cut_price = $cutPrice;
        $test->discount_percentage = $discountPercentage;
        if ($request->filled('discount_price')) {
            $test->discount_price = $request->discount_price;
        }
        $test->save();

        return redirect()->back()->with('success', "Pricing updated for {$test->test_name}!");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus($id)
    {
        $test = LabTest::findOrFail($id);
        $test->status = $test->status === 'active' ? 'inactive' : 'active';
        $test->save();

        return redirect()->back()->with('success', "Status updated to {$test->status} for {$test->test_name}!");
    }

    /**
     * Delete lab test.
     */
    public function destroy($id)
    {
        try {
            $test = LabTest::findOrFail($id);

            if ($test->image && File::exists(public_path($test->image))) {
                File::delete(public_path($test->image));
            }

            $test->delete();

            return redirect()->back()->with('success', 'Lab test deleted successfully!');
        } catch (Exception $e) {
            Log::error('Lab test delete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting lab test: ' . $e->getMessage());
        }
    }
}
