<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use App\Models\medicineModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class MedicineController extends Controller
{


    public function index()
    {
        $medicenes = medicineModel::with('category')->orderByDesc('created_at')
            ->paginate(10);

        $category = CategoryModel::orderByDesc('created_at')
            ->get();

        $medicenesall = medicineModel::count();
        $medicenesinstocks = medicineModel::where('status', 'In Stock')->count();
        $mediceneslowstocks = medicineModel::where('status', 'Low Stock')->count();
        $medicenesoutofstocks = medicineModel::where('status', 'Out of Stock')->count();
        return view('admin.medicine', compact('medicenes', 'category', 'medicenesall', 'medicenesinstocks', 'mediceneslowstocks', 'medicenesoutofstocks'));
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
            $data->status = $request->status;
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
}
