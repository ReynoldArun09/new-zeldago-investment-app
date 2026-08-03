<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::guard('admin')->user()->notifications()->paginate(15);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::guard('admin')->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back();
    }

    public function history()
    {
        $logs = \App\Models\AdminNotificationLog::with('user')->latest()->paginate(15);
        return view('admin.notifications.history', compact('logs'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'target' => 'required|in:all,investors,agents,specific',
            'target_user_id' => 'required_if:target,specific|nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $users = collect();

        if ($request->target === 'all') {
            $users = \App\Models\User::all();
        } elseif ($request->target === 'investors') {
            $users = \App\Models\User::where('role', 'investor')->get();
        } elseif ($request->target === 'agents') {
            $users = \App\Models\User::where('role', 'agent')->get();
        } elseif ($request->target === 'specific') {
            $user = \App\Models\User::find($request->target_user_id);
            if ($user) $users->push($user);
        }

        foreach ($users as $user) {
            $user->notify(new \App\Notifications\UserMessageNotification($request->title, $request->message));
        }

        \App\Models\AdminNotificationLog::create([
            'target' => $request->target,
            'target_user_id' => $request->target === 'specific' ? $request->target_user_id : null,
            'title' => $request->title,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Notification sent successfully to ' . $users->count() . ' user(s).');
    }
}
