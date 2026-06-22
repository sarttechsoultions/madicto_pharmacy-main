<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '2')->with('orders');

        // Search Name / Email / Number
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('number', 'LIKE', "%{$search}%");
            });
        }

        // Status Filter
        if (
            $request->filled('status')
            && $request->status != 'all'
        ) {
            $query->where('status', $request->status);
        }

        // Date Filter
        if ($request->filled('date') && $request->date != 'all') {

            $days = (int) $request->date;

            $query->where(
                'created_at',
                '>=',
                now()->subDays($days)
            );
        }

        $user = $query->latest()
            ->paginate(20)
            ->withQueryString();

        $adminss = User::where('role', '1')->paginate(20);

        $userall = User::count();
        $useractive = User::where('status', 'Active')->count();
        $userblocked = User::where('status', 'blocked')->count();
        $admins = User::where('role', '1')->count();

        return view(
            'admin.user',
            compact(
                'user',
                'adminss',
                'userall',
                'useractive',
                'userblocked',
                'admins'
            )
        );
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

    public function export()
    {
        $fileName = 'users_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        $callback = function () {

            $file = fopen('php://output', 'w');

            // Header Row
            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'Mobile',
                'Wallet',
                'Status',
                'Role',
                'Refer Code',
                'Total Orders',
            ]);

            User::with('orders')->chunk(500, function ($users) use ($file) {

                foreach ($users as $user) {

                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->number,
                        $user->wallet,
                        $user->status,
                        $user->role,
                        $user->referral_code,
                        $user->orders->count(),
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user = auth()->user();

        $user->update([
            'fcm_token' => $request->fcm_token
        ]);

        return response()->json([
            'status' => true,
            'message' => 'FCM token updated successfully'
        ]);
    }
}
