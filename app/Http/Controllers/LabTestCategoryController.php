<?php

namespace App\Http\Controllers;

use App\Models\LabTestCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LabTestCategoryController extends Controller
{
    /**
     * List lab test categories with test count.
     */
    public function index(Request $request)
    {
        $query = LabTestCategory::withCount(['tests', 'activeTests']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $categories = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'    => LabTestCategory::count(),
            'active'   => LabTestCategory::where('status', 'active')->count(),
            'inactive' => LabTestCategory::where('status', 'inactive')->count(),
        ];

        return view('admin.lab.categories', compact('categories', 'stats'));
    }

    /**
     * Store a new category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'icon'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
        ]);

        try {
            $iconPath = null;
            if ($request->hasFile('icon')) {
                $file = $request->file('icon');
                $filename = time() . '_lab_cat_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destination = public_path('uploads/lab_categories');
                if (!File::isDirectory($destination)) {
                    File::makeDirectory($destination, 0755, true, true);
                }
                $file->move($destination, $filename);
                $iconPath = 'uploads/lab_categories/' . $filename;
            }

            LabTestCategory::create([
                'name'        => $request->name,
                'icon'        => $iconPath,
                'description' => $request->description,
                'status'      => $request->status,
            ]);

            return redirect()->back()->with('success', 'Lab test category added successfully!');
        } catch (Exception $e) {
            Log::error('Lab category store error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error adding category: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, $id)
    {
        $category = LabTestCategory::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'icon'        => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
        ]);

        try {
            if ($request->hasFile('icon')) {
                if ($category->icon && File::exists(public_path($category->icon))) {
                    File::delete(public_path($category->icon));
                }

                $file = $request->file('icon');
                $filename = time() . '_lab_cat_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destination = public_path('uploads/lab_categories');
                if (!File::isDirectory($destination)) {
                    File::makeDirectory($destination, 0755, true, true);
                }
                $file->move($destination, $filename);
                $category->icon = 'uploads/lab_categories/' . $filename;
            }

            $category->name = $request->name;
            $category->description = $request->description;
            $category->status = $request->status;
            $category->save();

            return redirect()->back()->with('success', 'Lab test category updated successfully!');
        } catch (Exception $e) {
            Log::error('Lab category update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error updating category: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status.
     */
    public function toggleStatus($id)
    {
        $category = LabTestCategory::findOrFail($id);
        $category->status = $category->status === 'active' ? 'inactive' : 'active';
        $category->save();

        return redirect()->back()->with('success', "Category status updated to {$category->status}!");
    }

    /**
     * Delete category.
     */
    public function destroy($id)
    {
        try {
            $category = LabTestCategory::findOrFail($id);

            if ($category->icon && File::exists(public_path($category->icon))) {
                File::delete(public_path($category->icon));
            }

            $category->delete();

            return redirect()->back()->with('success', 'Lab test category deleted successfully!');
        } catch (Exception $e) {
            Log::error('Lab category delete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }
}
