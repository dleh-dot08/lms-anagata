<?php

namespace App\Http\Controllers;

use App\Models\HelpdeskMessage;
use Illuminate\Http\Request;

class HelpdeskMessageController extends Controller
{
    // Simpan pesan/chat pada tiket
    public function store(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:helpdesk_tickets,id',
            'message' => 'required|string',
        ]);

        HelpdeskMessage::create([
            'ticket_id'   => $request->ticket_id,
            'user_id'     => auth()->id(),
            'sender_type' => auth()->user()->role_id == 1 ? 'admin' : 'user',
            'message'     => $request->message,
        ]);

        return back()->with('success', 'Pesan berhasil dikirim.');
    }
}
