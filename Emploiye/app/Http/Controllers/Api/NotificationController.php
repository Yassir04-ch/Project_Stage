<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $request->user()->notifications()->latest()->get();

        return response()->json([
            'notifications'=>$notifications,
            'user'=>$user,
            ],200);
    }

    public function unreadCount(Request $request)
    {
        $count = $request->user()->notifications()->where('is_read', false)->count();

        return response()->json([
            'count' => $count
        ]);
    }

    public function markAsRead($id, Request $request)
    {
        $notification = $request->user()->notifications()->findOrFail($id);

        $notification->update([
            'is_read' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue'
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->notifications()->where('is_read', false)->update([
                'is_read' => true
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications sont lues'
        ]);
    }

    public function destroy($id, Request $request)
    {
        $notification = $request->user()->notifications()->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification supprimée'
        ]);
    }
}