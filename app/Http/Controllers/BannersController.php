<?php

namespace App\Http\Controllers;

use App\Models\BannersModel;
use Illuminate\Http\Request;

class BannersController extends Controller
{
    public function index()
    {
        $banners = BannersModel::all();
        return view('admin.banner', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'img' => 'required|image',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'banner_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('img')) {

            $image = $request->file('img');

            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('uploads/banners'), $imageName);

            $imagePath = 'uploads/banners/' . $imageName;
        }

        // Multiple Images
        $gallery = [];

        if ($request->hasFile('banners_images')) {

            foreach ($request->file('banners_images') as $img) {

                $name = time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();

                $img->move(public_path('uploads/banners/gallery'), $name);

                $gallery[] = 'uploads/banners/gallery/' . $name;
            }
        }

        BannersModel::create([
            'title' => $request->title,
            'description' => $request->description,
            'img' => $imagePath,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'discount' => $request->discount,
            'banners_image' => json_encode($gallery),
        ]);

        return redirect()
            ->route('banner.index')
            ->with('success', 'Banner created successfully.');
    }

    public function toggleStatus($id)
    {
        $banner = BannersModel::findOrFail($id);

        $banner->status = ($banner->status == 'active') ? 'inactive' : 'active';
        $banner->save();

        return response()->json([
            'status' => $banner->status
        ]);
    }

    public function destroy($id)
    {
        $banner = BannersModel::findOrFail($id);
        $banner->delete();

        return redirect()->back()->with('success', 'Banner deleted successfully.');
    }

    public function updatedata(Request $request, $id)
    {
        $banner = BannersModel::findOrFail($id);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'discount' => $request->discount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ];

        if ($request->hasFile('img')) {

            $image = $request->file('img');

            $imageName = time() . '_' .
                $image->getClientOriginalName();

            $image->move(
                public_path('uploads/banners'),
                $imageName
            );

            $data['img'] =
                'uploads/banners/' . $imageName;
        }

        $banner->update($data);

        return redirect()
            ->back()
            ->with('success', 'Banner updated successfully');
    }
}
