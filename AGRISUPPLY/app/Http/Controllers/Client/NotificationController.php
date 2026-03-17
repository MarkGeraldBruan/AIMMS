<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('client.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    public function getRecent()
    {
        $notifications = Auth::user()->notifications()->limit(10)->get();
        return response()->json(['notifications' => $notifications]);
    }
}