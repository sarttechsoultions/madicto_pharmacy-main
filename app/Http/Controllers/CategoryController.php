<?php

namespace App\Http\Controllers;

use App\Models\CategoryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = CategoryModel::orderByDesc('created_at')
            ->paginate(10);

        return view('admin.category', compact('categories'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $category = new CategoryModel();
            $category->name = $request->name;
            $category->description = $request->description;
            $category->status = $request->status;

            if ($request->hasFile('icon')) {

                $image = $request->file('icon');

                $imageName = time() . '.' . $image->getClientOriginalExtension();

                $image->move(public_path('uploads/category'), $imageName);

                $category->icon = $imageName;
            }
            $category->save();

            return redirect()->back()->with('success', 'Category created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $category = CategoryModel::findOrFail($id);

        $data = $request->all();

        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/category'), $filename);
            $data['icon'] = $filename;
        }

        $category->update($data);

        return back()->with('success', 'Category updated successfully');
    }

    public function destroy($id)
    {
        $category = CategoryModel::findOrFail($id);
        $category->delete();

        return back()->with('success', 'Category deleted successfully');
    }
}
