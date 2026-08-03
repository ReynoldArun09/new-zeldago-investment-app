<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = auth()->user()->tickets()->orderBy('updated_at', 'desc')->paginate(10);
        return view('user.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('user.support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
            'message' => 'required|string',
        ]);

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'ticket_id' => 'TKT-' . strtoupper(Str::random(8)),
            'subject' => $request->subject,
            'priority' => $request->priority,
            'status' => 'OPEN',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        $admins = \App\Models\Admin::all();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GenericNotification(
            'New Support Ticket',
            'A new support ticket (' . $ticket->ticket_id . ') has been created by ' . auth()->user()->username . '.',
            'ph-envelope-simple'
        ));

        return redirect()->route('user.support.index')->with('success', 'Support ticket created successfully.');
    }

    public function show($id)
    {
        $ticket = auth()->user()->tickets()->with('messages.user')->findOrFail($id);
        return view('user.support.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = auth()->user()->tickets()->findOrFail($id);

        if ($ticket->status === 'CLOSED') {
            return back()->with('error', 'Cannot reply to a closed ticket.');
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        // If ticket was answered by admin, moving it back to OPEN status
        if ($ticket->status === 'ANSWERED') {
            $ticket->status = 'OPEN';
            $ticket->save();
        } else {
            $ticket->touch(); // Update updated_at timestamp
        }

        $admins = \App\Models\Admin::all();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\GenericNotification(
            'New Ticket Reply',
            auth()->user()->username . ' has replied to ticket ' . $ticket->ticket_id . '.',
            'ph-chat-circle-dots'
        ));

        return back()->with('success', 'Reply sent successfully.');
    }
}
