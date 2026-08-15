<?php

namespace App\Http\Controllers\Web\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{


    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);

        return view('shared.notifications', compact('notifications'));
    }
    public function unread(Request $request)
    {
        $notifications = $request->user()->unreadNotifications()->take(5)->get();
        return response()->json($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}