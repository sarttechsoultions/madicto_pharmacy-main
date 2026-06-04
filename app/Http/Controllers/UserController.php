<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = User::where('role', '2')->with('orders')->paginate(20);
        $adminss = User::where('role', '1')->paginate(20);
        $userall = User::count();
        $useractive = User::where('status', 'Active')->count();
        $userblocked = User::where('status', 'Blocked')->count();
        $admins = User::where('role', '1')->count();
        return view('admin.user', compact('user', 'adminss', 'userall', 'useractive', 'userblocked', 'admins'));
    }

    public function profile()
    {
        $roles = User::where('role', '1')->paginate(20);
        return view('admin.setting', compact('roles'));
    }

    public function toggleStatus(Request $request)
    {
        $user = User::findOrFail($request->id);

        $user->status = $request->status;
        $user->save();

        return response()->json([
            'success' => true,
            'status' => $user->status
        ]);
    }

    public function toggleRole(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ]);
        }

        // toggle role
        $user->role = ($user->role == 1) ? 2 : 1;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User role updated successfully',
            'role' => $user->role
        ]);
    }
    public function profileUpdate(Request $request)
    {
        $user = auth()->user();

        $imageName = $user->profile_img;

        // DEBUG CHECK (IMPORTANT)
        $imagepath = null;
        if ($request->hasFile('profile_img')) {

            $image = $request->file('profile_img');

            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('uploads/profile'), $imageName);
        }

        $user->update([
            'profile_img' => 'uploads/profile/' . $imageName,
            'name'        => $request->name,
            'email'       => $request->email,
            'number'      => $request->number,
        ]);

        return back()->with('success', 'Profile updated successfully');
    }

    public function store(Request $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return back()->with('success', 'User created');
    }
}
