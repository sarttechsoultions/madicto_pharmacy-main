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

        $notification = noty::create([

            'title' => $request->title,

            'message' => $request->message,

            'type' => $request->type ?? 'General',

            'image' => $image,

            'send_to' => 'All Users',

            'status' => 'Sent',

            'total_users' => User::count(),

            'success_count' => 0,

            'failed_count' => 0,

        ]);

        $success = 0;
        $failed = 0;

        $tokens = User::whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        if (count($tokens) > 0) {

            $messaging = (new Factory)
                ->withServiceAccount(storage_path('app/firebase/firebase-service-account.json'))
                ->createMessaging();

            foreach ($tokens as $token) {

                try {

                    $message = CloudMessage::withTarget('token', $token)
                        ->withNotification(
                            Notification::create(
                                $request->title,
                                $request->message,
                                $image ? asset('uploads/notification/' . $image) : null
                            )
                        );

                    $messaging->send($message);

                    $success++;
                } catch (\Exception $e) {

                    $failed++;
                }
            }
        }

        $notification->update([

            'success_count' => $success,

            'failed_count' => $failed,

            'status' => $failed > 0 ? 'Partial Success' : 'Sent'

        ]);

        return back()->with(
            'success',
            'Notification Sent Successfully.'
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

        $tokens = User::whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        if (count($tokens) > 0) {

            $messaging = (new Factory)
                ->withServiceAccount(storage_path('app/firebase/firebase-service-account.json'))
                ->createMessaging();

            foreach ($tokens as $token) {

                try {

                    $message = CloudMessage::withTarget('token', $token)
                        ->withNotification(
                            Notification::create(
                                $notification->title,
                                $notification->message,
                                $notification->image
                                    ? asset('uploads/notification/' . $notification->image)
                                    : null
                            )
                        );

                    $messaging->send($message);
                } catch (\Exception $e) {
                }
            }
        }

        return back()->with(
            'success',
            'Notification Sent Again Successfully.'
        );
    }
}
