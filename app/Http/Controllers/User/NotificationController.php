<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'unread');

        if ($tab === 'history') {
            $notifications = Auth::user()->readNotifications()->paginate(15);
        } else {
            $notifications = Auth::user()->unreadNotifications()->paginate(15);
        }

        $notifications->appends(['tab' => $tab]);

        return view('user.notifications.index', compact('notifications', 'tab'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }
}
