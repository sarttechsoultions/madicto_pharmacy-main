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
            'title' => 'required',
            'description' => 'required',
            'img' => 'required|image',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $imagePath = null;

        if ($request->hasFile('img')) {

            $image = $request->file('img');

            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('uploads/banners'), $imageName);

            $imagePath = 'uploads/banners/' . $imageName;
        }

        BannersModel::create([
            'title' => $request->title,
            'description' => $request->description,
            'img' => $imagePath,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
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
}
