<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth('sanctum')->user();

        $unreadNotifications = $user->unreadNotifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => $notification->data,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $readNotifications = $user->readNotifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => $notification->data,
                'read_at' => $notification->read_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'unread' => $unreadNotifications,
                'read' => $readNotifications,
            ]
        ], 200);
    }

    public function markAsRead($id)
    {
        $user =  auth('sanctum')->user();

        $notification = Notification::where('notifiable_id', $user->id)->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'status' => 'success',
                'message' => 'Notification marked as read'
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Notification not found'
        ], 404);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read'
        ], 200);
    }
}
