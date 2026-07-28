<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketMessage;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with('user')->orderBy('updated_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('username', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority != '') {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->paginate(20)->appends($request->all());

        return view('admin.support.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = Ticket::with(['user', 'messages.user'])->findOrFail($id);
        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($id);

        if ($ticket->status === 'CLOSED') {
            return back()->with('error', 'Cannot reply to a closed ticket.');
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null, // null means Admin
            'message' => $request->message,
        ]);

        $ticket->status = 'ANSWERED';
        $ticket->save();

        return back()->with('success', 'Reply sent successfully.');
    }

    public function close($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->status = 'CLOSED';
        $ticket->save();

        return back()->with('success', 'Ticket closed successfully.');
    }
}
