<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\notification as noty;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\File;

class NotificationController extends Controller
{

    public function index()
    {
        $notifications = noty::latest()->paginate(10);

        $todayCount = noty::whereDate('created_at', today())->count();

        $failedCount = noty::where('status', 'Failed')->count();

        $totalUsers = User::count();

        return view('admin.notification', compact(
            'notifications',
            'todayCount',
            'failedCount',
            'totalUsers'
        ));
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'message' => 'required',
            'type' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $image = null;

        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $image = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('uploads/notification'), $image);
        }

        $totalRecipients = User::count();

        $notification = noty::create([

            'title' => $request->title,

            'message' => $request->message,

            'type' => $request->type ?? 'General',

            'image' => $image,

            'send_to' => 'All Users',

            'status' => 'Pending',

            'total_users' => $totalRecipients,

            'success_count' => 0,

            'failed_count' => 0,

        ]);

        $firebaseService = app(\App\Services\FirebaseNotificationService::class);
        $imageUrl = $image ? asset('uploads/notification/' . $image) : null;

        if (!$firebaseService->isConfigured()) {
            $notification->update([
                'status' => 'Failed (Missing Firebase Credentials)',
                'failed_count' => $totalRecipients,
            ]);

            return back()->with(
                'warning',
                'Notification saved, but push delivery was skipped because Firebase credentials (firebase-service-account.json) are not configured in storage/app/firebase/.'
            );
        }

        $result = $firebaseService->sendToAll(
            $request->title,
            $request->message,
            ['type' => $request->type ?? 'General'],
            $imageUrl
        );

        $notification->update([
            'success_count' => $result['success_count'],
            'failed_count'  => $result['failed_count'],
            'status'        => $result['failed_count'] > 0
                ? ($result['success_count'] > 0 ? 'Partial Success' : 'Failed')
                : 'Sent'
        ]);

        return back()->with(
            'success',
            "Notification processed: {$result['success_count']} delivered, {$result['failed_count']} failed."
        );
    }

    public function show($id)
    {
        $notification = noty::findOrFail($id);

        return response()->json($notification);
    }

    public function destroy($id)
    {
        $notification = noty::findOrFail($id);

        if (
            $notification->image &&
            File::exists(public_path('uploads/notification/' . $notification->image))
        ) {

            File::delete(public_path('uploads/notification/' . $notification->image));
        }

        $notification->delete();

        return back()->with(
            'success',
            'Notification Deleted Successfully.'
        );
    }

    public function resend($id)
    {
        $notification = noty::findOrFail($id);
        $firebaseService = app(\App\Services\FirebaseNotificationService::class);
        $imageUrl = $notification->image ? asset('uploads/notification/' . $notification->image) : null;

        if (!$firebaseService->isConfigured()) {
            return back()->with(
                'warning',
                'Firebase credentials (firebase-service-account.json) are not configured in storage/app/firebase/.'
            );
        }

        $result = $firebaseService->sendToAll(
            $notification->title,
            $notification->message,
            ['type' => $notification->type ?? 'General'],
            $imageUrl
        );

        $notification->update([
            'success_count' => $result['success_count'],
            'failed_count'  => $result['failed_count'],
            'status'        => $result['failed_count'] > 0
                ? ($result['success_count'] > 0 ? 'Partial Success' : 'Sent')
                : 'Sent'
        ]);

        return back()->with(
            'success',
            "Notification resent: {$result['success_count']} delivered, {$result['failed_count']} failed."
        );
    }
}
