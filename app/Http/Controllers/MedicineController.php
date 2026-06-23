<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\medicineModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Imports\MedicineImport;
use App\Exports\MedicineSampleExport;
use Maatwebsite\Excel\Facades\Excel;
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
        // Status Filter (Quantity Based)
        if ($request->filled('status')) {

            switch ($request->status) {

                case 'In Stock':
                    $query->where('quantity', '>', 5);
                    break;

                case 'Low Stock':
                    $query->where('quantity', '>', 0)
                        ->where('quantity', '<=', 5);
                    break;

                case 'Out of Stock':
                    $query->where('quantity', 0);
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

        $medicenesinstocks = medicineModel::where('quantity', '>', 5)->count();

        $mediceneslowstocks = medicineModel::where('quantity', '>', 0)
            ->where('quantity', '<=', 5)
            ->count();

        $medicenesoutofstocks = medicineModel::where('quantity', 0)->count();

        return view(
            'admin.medicine',
            compact(
                'medicenes',
                'category',
                'medicenesall',
                'medicenesinstocks',
                'mediceneslowstocks',
                'medicenesoutofstocks'
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
            $data->quantity = $request->quantity;
            $data->stock = $request->stock;
            $data->manufacturer = $request->manufacturer;
            $data->reorder_level = $request->reorder_level;
            $data->description = $request->description;
            $data->price = $request->price;
            $data->unit_type = $request->unit_type;
            $data->pack_size = $request->pack_size;
            $data->status = $request->status ?? 'In Stock';
            $data->manufacture_date = $request->manufacture_date;
            $data->expiry_date = $request->expiry_date;


            /* Single Image */
            if ($request->hasFile('image')) {

                $img = $request->file('image');

                $imageName = time() . '_main_' . uniqid() . '.' . $img->getClientOriginalExtension();

                $img->move(public_path('uploads/medicine'), $imageName);

                $data->image = 'uploads/medicine/' . $imageName;
            }

            /* Multiple Images */
            $medicineImages = [];

            if ($request->hasFile('medicine_image')) {

                foreach ($request->file('medicine_image') as $img) {

                    $fileName = time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();

                    $img->move(public_path('uploads/medicine/gallery'), $fileName);

                    $medicineImages[] = 'uploads/medicine/gallery/' . $fileName;
                }
            }

            $data->medicine_image = json_encode($medicineImages);

            $data->save();

            return redirect()->back()->with('success', 'Medicine added successfully!');
        } catch (Exception $e) {

            // Log error
            Log::error($e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong! ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);

        try {

            $medicine = medicineModel::findOrFail($id);

            $medicine->name = $request->name;
            $medicine->category_id = $request->category_id;
            $medicine->price = $request->price;
            $medicine->quantity = $request->quantity;
            $medicine->stock = $request->stock;

            $medicine->status = $request->status;

            $medicine->save();

            return redirect()->back()
                ->with('success', 'Medicine updated successfully.');
        } catch (Exception $e) {

            Log::error($e->getMessage());

            return redirect()->back()
                ->with('error', 'Something went wrong.');
        }
    }

    public function destroy($id)
    {
        try {

            $medicine = medicineModel::findOrFail($id);

            $medicine->delete();

            return redirect()->back()
                ->with('success', 'Medicine deleted successfully.');
        } catch (Exception $e) {

            Log::error($e->getMessage());

            return redirect()->back()
                ->with('error', 'Something went wrong.');
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
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new MedicineImport, $request->file('file'));

        return back()->with('success', 'Medicines Imported Successfully');
    }

    public function downloadSample()
    {
        return Excel::download(
            new MedicineSampleExport,
            'medicine_sample.xlsx'
        );
    }
}
