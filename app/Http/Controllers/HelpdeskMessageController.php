<?php

namespace App\Http\Controllers;

use App\Models\HelpdeskMessage;
use Illuminate\Http\Request;

class HelpdeskMessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:helpdesk_tickets,id',
            'message' => 'required|string',
        ]);

        HelpdeskMessage::create([
            'ticket_id' => $request->ticket_id,
            'user_id' => auth()->id(),
            'sender_type' => auth()->check() ? 'user' : 'guest',
            'message' => $request->message,
            'guest_name' => $request->guest_name ?? null,
        ]);

        return back()->with('success', 'Pesan berhasil dikirim.');
    }
}
