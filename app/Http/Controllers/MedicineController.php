<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\medicineModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Imports\MedicineImport;
use App\Exports\MedicineSampleExport;
use App\Models\ReviewModel;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Exception;

class MedicineController extends Controller
{


    public function index(Request $request)
    {
        $query = medicineModel::with('category');

        // Global Search
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('id', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhere('price', 'LIKE', "%{$search}%")
                    ->orWhere('stock', 'LIKE', "%{$search}%")
                    ->orWhere('quantity', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('unit_type', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', function ($cat) use ($search) {
                        $cat->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Status Filter
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'In Stock':
                    $query->where(function ($q) {
                        $q->whereNull('status')->orWhere('status', '!=', 'Inactive');
                    })->where('quantity', '>', 5);
                    break;

                case 'Low Stock':
                    $query->where(function ($q) {
                        $q->whereNull('status')->orWhere('status', '!=', 'Inactive');
                    })->where('quantity', '>', 0)
                      ->where('quantity', '<=', 5);
                    break;

                case 'Out of Stock':
                    $query->where(function ($q) {
                        $q->whereNull('status')->orWhere('status', '!=', 'Inactive');
                    })->where('quantity', 0);
                    break;

                case 'Inactive':
                    $query->where('status', 'Inactive');
                    break;
            }
        }

        // Unit Type Filter
        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->unit_type);
        }

        // Sorting
        if ($request->filled('sort')) {

            switch ($request->sort) {

                case 'Name (A-Z)':
                    $query->orderBy('name', 'asc');
                    break;

                case 'Name (Z-A)':
                    $query->orderBy('name', 'desc');
                    break;

                case 'Price ↑':
                    $query->orderBy('price', 'asc');
                    break;

                case 'Price ↓':
                    $query->orderBy('price', 'desc');
                    break;

                case 'Stock ↑':
                    $query->orderBy('stock', 'asc');
                    break;

                case 'Stock ↓':
                    $query->orderBy('stock', 'desc');
                    break;

                default:
                    $query->orderByDesc('created_at');
                    break;
            }
        } else {
            $query->orderByDesc('created_at');
        }

        $medicenes = $query->paginate(10)->withQueryString();

        $category = CategoryModel::orderByDesc('created_at')->get();

        $medicenesall = medicineModel::count();

        $medicenesinstocks = medicineModel::where(function ($q) {
            $q->whereNull('status')->orWhere('status', '!=', 'Inactive');
        })->where('quantity', '>', 5)->count();

        $mediceneslowstocks = medicineModel::where(function ($q) {
            $q->whereNull('status')->orWhere('status', '!=', 'Inactive');
        })->where('quantity', '>', 0)
          ->where('quantity', '<=', 5)
          ->count();

        $medicenesoutofstocks = medicineModel::where(function ($q) {
            $q->whereNull('status')->orWhere('status', '!=', 'Inactive');
        })->where('quantity', 0)->count();

        $medicenesinactive = medicineModel::where('status', 'Inactive')->count();

        return view(
            'admin.medicine',
            compact(
                'medicenes',
                'category',
                'medicenesall',
                'medicenesinstocks',
                'mediceneslowstocks',
                'medicenesoutofstocks',
                'medicenesinactive'
            )
        );
    }
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'medicine_image.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {

            $data = new medicineModel();

            $data->name = $request->name;
            $data->category_id = $request->category_id;
            $data->batch_no = $request->batch_no;
            $data->usage_instructions = $request->usage_instructions;
            $data->discount = $request->discount;

            $data->manufacturer = $request->manufacturer;
            $data->reorder_level = $request->reorder_level;
            $data->description = $request->description;
            $data->price = $request->price;
            $data->unit_type = $request->unit_type;
            $data->pack_size = $request->pack_size;
            $data->manufacture_date = $request->manufacture_date;
            $data->expiry_date = $request->expiry_date;
            $data->quantity = $request->quantity ?? 0;
            $data->stock = $request->stock ?? $request->quantity ?? 0;

            // Preserve explicit status (e.g. Inactive, In Stock, etc.) or calculate based on quantity
            if ($request->filled('status')) {
                $data->status = $request->status;
            } else {
                $qty = (int)$data->quantity;
                $data->status = $qty > 5 ? 'In Stock' : ($qty > 0 ? 'Low Stock' : 'Out of Stock');
            }

            /* Single Image */
            if ($request->hasFile('image')) {
                \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('uploads/medicine'));
                $img = $request->file('image');
                $imageName = time() . '_main_' . uniqid() . '.' . $img->getClientOriginalExtension();
                $img->move(public_path('uploads/medicine'), $imageName);
                $data->image = 'uploads/medicine/' . $imageName;
            }

            /* Multiple Images */
            $medicineImages = [];
            if ($request->hasFile('medicine_image')) {
                \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('uploads/medicine/gallery'));
                foreach ($request->file('medicine_image') as $img) {
                    $fileName = time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();
                    $img->move(public_path('uploads/medicine/gallery'), $fileName);
                    $medicineImages[] = 'uploads/medicine/gallery/' . $fileName;
                }
            }

            $data->medicine_image = json_encode($medicineImages);
            $data->save();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'status'  => true,
                    'message' => 'Medicine added successfully!',
                    'data'    => $data->fresh('category'),
                ], 201);
            }

            return redirect()->back()->with('success', 'Medicine added successfully!');
        } catch (Exception $e) {
            Log::error('Medicine store error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'Something went wrong! ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong! ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'medicine_image.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {

            $medicine = medicineModel::findOrFail($id);

            $medicine->name = $request->name;
            $medicine->category_id = $request->category_id;
            $medicine->batch_no = $request->batch_no;
            $medicine->usage_instructions = $request->usage_instructions;
            $medicine->discount = $request->discount;

            $medicine->manufacturer = $request->manufacturer;
            $medicine->reorder_level = $request->reorder_level;
            $medicine->description = $request->description;
            $medicine->price = $request->price;
            $medicine->unit_type = $request->unit_type;
            $medicine->pack_size = $request->pack_size;
            $medicine->manufacture_date = $request->manufacture_date;
            $medicine->expiry_date = $request->expiry_date;
            $medicine->quantity = $request->quantity ?? $medicine->quantity;
            $medicine->stock = $request->stock ?? $request->quantity ?? $medicine->stock;

            // Preserve explicit status or calculate based on quantity
            if ($request->filled('status')) {
                $medicine->status = $request->status;
            } else {
                $qty = (int)$medicine->quantity;
                $medicine->status = $qty > 5 ? 'In Stock' : ($qty > 0 ? 'Low Stock' : 'Out of Stock');
            }

            /* Main Image */
            if ($request->hasFile('image')) {
                // Old image delete safely
                if ($medicine->image) {
                    $imgFull = public_path($medicine->image);
                    $imgPrefix = public_path('uploads/medicine/' . $medicine->image);
                    if (\Illuminate\Support\Facades\File::exists($imgFull)) {
                        \Illuminate\Support\Facades\File::delete($imgFull);
                    } elseif (\Illuminate\Support\Facades\File::exists($imgPrefix)) {
                        \Illuminate\Support\Facades\File::delete($imgPrefix);
                    }
                }

                \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('uploads/medicine'));
                $img = $request->file('image');
                $imageName = time() . '_main_' . uniqid() . '.' . $img->getClientOriginalExtension();
                $img->move(public_path('uploads/medicine'), $imageName);
                $medicine->image = 'uploads/medicine/' . $imageName;
            }

            /* Gallery Images */
            if ($request->hasFile('medicine_image')) {
                \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('uploads/medicine/gallery'));
                if (!empty($medicine->medicine_image)) {
                    $oldGallery = is_array($medicine->medicine_image)
                        ? $medicine->medicine_image
                        : (json_decode($medicine->medicine_image, true) ?: []);

                    foreach ($oldGallery as $oldImage) {
                        if ($oldImage && \Illuminate\Support\Facades\File::exists(public_path($oldImage))) {
                            \Illuminate\Support\Facades\File::delete(public_path($oldImage));
                        }
                    }
                }

                $gallery = [];
                foreach ($request->file('medicine_image') as $img) {
                    $fileName = time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();
                    $img->move(public_path('uploads/medicine/gallery'), $fileName);
                    $gallery[] = 'uploads/medicine/gallery/' . $fileName;
                }

                $medicine->medicine_image = json_encode($gallery);
            }

            $medicine->save();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'status'  => true,
                    'message' => 'Medicine updated successfully!',
                    'data'    => $medicine->fresh('category'),
                ], 200);
            }

            return redirect()->back()->with('success', 'Medicine updated successfully!');
        } catch (Exception $e) {
            Log::error('Medicine update error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'Something went wrong! ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong! ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $medicine = medicineModel::findOrFail($id);

            if ($medicine->image) {
                $imgFull = public_path($medicine->image);
                $imgPrefix = public_path('uploads/medicine/' . $medicine->image);
                if (\Illuminate\Support\Facades\File::exists($imgFull)) {
                    \Illuminate\Support\Facades\File::delete($imgFull);
                } elseif (\Illuminate\Support\Facades\File::exists($imgPrefix)) {
                    \Illuminate\Support\Facades\File::delete($imgPrefix);
                }
            }

            $medicine->delete();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'status'  => true,
                    'message' => 'Medicine deleted successfully.',
                ], 200);
            }

            return redirect()->back()
                ->with('success', 'Medicine deleted successfully.');
        } catch (Exception $e) {
            Log::error('Medicine delete error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'Something went wrong.',
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Something went wrong.');
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $medicine = medicineModel::findOrFail($id);

            if ($medicine->status === 'Inactive') {
                $qty = (int)$medicine->quantity;
                $medicine->status = $qty > 5 ? 'In Stock' : ($qty > 0 ? 'Low Stock' : 'Out of Stock');
            } else {
                $medicine->status = 'Inactive';
            }

            $medicine->save();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'status'  => true,
                    'message' => "Medicine status changed to {$medicine->status}",
                    'data'    => $medicine,
                ], 200);
            }

            return redirect()->back()->with('success', "Medicine status changed to {$medicine->status}");
        } catch (Exception $e) {
            Log::error('Medicine toggleStatus error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'Something went wrong.',
                ], 500);
            }

            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function toggleDod($id)
    {
        $medicine = medicineModel::findOrFail($id);

        $medicine->dod = $medicine->dod == 1 ? 2 : 1;

        $medicine->save();

        return back()->with(
            'success',
            'Deal Of Day status updated successfully'
        );
    }



    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:51200',
        ], [
            'file.required' => 'Please select an Excel or CSV file to import.',
            'file.mimes' => 'The uploaded file must be a valid .xlsx, .xls, or .csv spreadsheet.',
            'file.max' => 'The file size must not exceed 50MB.',
        ]);

        // Allocate sufficient time and memory for large Excel spreadsheets
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        // Prevent column slug collision (e.g. M.R.P. vs MRP) by keeping raw headers
        HeadingRowFormatter::default('none');

        try {
            $import = new MedicineImport();
            Excel::import($import, $request->file('file'));

            $summary = [
                'total_rows'   => $import->getTotalRows(),
                'imported'     => $import->getImportedCount(),
                'skipped'      => $import->getSkippedCount(),
                'failed'       => $import->getFailedCount(),
                'total_errors' => $import->getTotalValidationErrors(),
            ];

            // Store max 20 errors for user display (prevents session size limits)
            $first20Errors = $import->getValidationErrors();

            $message = "Import Completed: {$summary['imported']} imported, {$summary['skipped']} skipped (blank/headers), {$summary['failed']} failed.";

            $response = back()->with('import_summary', $summary)
                             ->with('import_errors', $first20Errors);

            if ($summary['imported'] > 0 && $summary['failed'] === 0) {
                return $response->with('success', $message);
            } elseif ($summary['imported'] > 0) {
                return $response->with('warning', $message);
            } else {
                return $response->with('error', $message);
            }
        } catch (Exception $e) {
            Log::error('Medicine import error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Import failed due to an error: ' . $e->getMessage());
        } finally {
            HeadingRowFormatter::reset();
        }
    }

    public function downloadSample()
    {
        $fileName = 'medicine_sample_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Header Row
            fputcsv($file, [
                'name',
                'category_id',
                'batch_no',
                'usage_instructions',
                'discount',
                'quantity',
                'stock',
                'manufacturer',
                'reorder_level',
                'description',
                'price',
                'unit_type',
                'pack_size',
                'status',
                'manufacture_date',
                'expiry_date',
                'image',
                'medicine_images'
            ]);

            // Sample Row 1
            fputcsv($file, [
                'Paracetamol 500mg',
                9,
                'B123',
                'After meal twice daily',
                10,
                100,
                100,
                'ABC Pharma',
                20,
                'Effective for fever and headache relief',
                50,
                'Tablets',
                '10 Tablets',
                'In Stock',
                '2026-01-01',
                '2028-01-01',
                'uploads/medicine/paracetamol.jpg',
                'uploads/medicine/gallery/g1.jpg, uploads/medicine/gallery/g2.jpg'
            ]);

            // Sample Row 2
            fputcsv($file, [
                'Vitamin C 1000mg',
                5,
                'B124',
                'One tablet daily with water',
                5,
                80,
                80,
                'HealthCare Ltd',
                15,
                'Immunity booster and antioxidant supplement',
                35,
                'Tablets',
                '30 Tablets',
                'In Stock',
                '2026-02-01',
                '2028-02-01',
                'uploads/medicine/vitaminc.jpg',
                'uploads/medicine/gallery/v1.jpg'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show($id)
    {

        $medicine = medicineModel::with([
            'category',
            'reviews.coustmer'
        ])->findOrFail($id);

        $averageRating = ReviewModel::where('medicine_id', $id)
            ->avg('rating');

        $totalReviews = ReviewModel::where('medicine_id', $id)
            ->count();

        $five = ReviewModel::where('medicine_id', $id)->where('rating', 5)->count();

        $four = ReviewModel::where('medicine_id', $id)->where('rating', 4)->count();

        $three = ReviewModel::where('medicine_id', $id)->where('rating', 3)->count();

        $two = ReviewModel::where('medicine_id', $id)->where('rating', 2)->count();

        $one = ReviewModel::where('medicine_id', $id)->where('rating', 1)->count();

        return view(
            'admin.medicine-details',
            compact(
                'medicine',
                'averageRating',
                'totalReviews',
                'five',
                'four',
                'three',
                'two',
                'one'
            )
        );
    }
}
