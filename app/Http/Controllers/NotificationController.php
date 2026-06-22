<?php

namespace App\Http\Controllers;

use App\Models\notification;
use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class NotificationController extends Controller
{

    public function index()
    {
        return view('admin.notification');
    }
    public function sendNotification(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'message' => 'required'
        ]);

        notification::create([
            'title' => $request->title,
            'message' => $request->message
        ]);

        $factory = (new Factory)
            ->withServiceAccount(
                config('firebase.credentials')
            );

        $messaging = $factory->createMessaging();

        $tokens = User::whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        foreach ($tokens as $token) {

            try {

                $message = CloudMessage::withTarget(
                    'token',
                    $token
                )->withNotification(
                    FirebaseNotification::create(
                        $request->title,
                        $request->message
                    )
                );

                $messaging->send($message);
            } catch (\Exception $e) {
            }
        }

        return back()->with(
            'success',
            'Notification Sent Successfully'
        );
    }
}
