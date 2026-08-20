<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function index(Request $request)
    {
        $notifications = $request->user()->notifications;

        return response()->json([
            'status' => 'success',
            'data' => $notifications
        ]);
    }


    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'status' => 'success',
                'message' => 'Notification marked as read'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Notification not found'
        ], 404);
    }
}
