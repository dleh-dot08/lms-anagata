<?php

namespace App\Http\Controllers;

use App\Models\HelpdeskTicket;
use Illuminate\Http\Request;

class HelpdeskTicketController extends Controller
{
    public function index()
    {
        if (auth()->user()->role_id == 1) {
            $tickets = HelpdeskTicket::all(); // Admin can view all tickets
        } else {
            $tickets = HelpdeskTicket::where('user_id', auth()->id())->get(); // Peserta can view their own tickets
        }
        return view('admin.helpdesk.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = HelpdeskTicket::findOrFail($id);
        return view('admin.helpdesk.show', compact('ticket'));
    }

    public function create()
    {
        return view('peserta.helpdesk.create');
    }

    public function store(Request $request)
    {
        $ticket = new HelpdeskTicket();
        $ticket->subject = $request->subject;
        $ticket->user_id = auth()->id();
        $ticket->save();

        return redirect()->route('peserta.helpdesk.index');
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
        $ticket->status = 'closed';
        $ticket->closed_at = now();
        $ticket->save();

        return redirect()->route('admin.helpdesk.tickets.index');
    }
}
