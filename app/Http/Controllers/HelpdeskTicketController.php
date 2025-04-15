<?php

namespace App\Http\Controllers;

use App\Models\HelpdeskTicket;
use Illuminate\Http\Request;

class HelpdeskTicketController extends Controller
{
    public function index()
    {
        $tickets = HelpdeskTicket::latest()->paginate(10);
        return view('helpdesk.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = HelpdeskTicket::with('messages')->findOrFail($id);
        return view('helpdesk.show', compact('ticket'));
    }

    public function create()
    {
        return view('helpdesk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
        ]);

        $ticket = HelpdeskTicket::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
        ]);

        return redirect()->route('helpdesk.show', $ticket->id)->with('success', 'Tiket berhasil dibuat.');
    }

    public function guestStore(Request $request)
    {
        $request->validate([
            'guest_name' => 'required|string|max:100',
            'guest_email' => 'required|email|max:100',
            'subject' => 'required|string|max:255',
        ]);

        $ticket = HelpdeskTicket::create([
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'subject' => $request->subject,
        ]);

        return redirect()->route('helpdesk.show', $ticket->id)->with('success', 'Tiket berhasil dikirim.');
    }

    public function close($id)
    {
        $ticket = HelpdeskTicket::findOrFail($id);
        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Tiket berhasil ditutup.');
    }
}
