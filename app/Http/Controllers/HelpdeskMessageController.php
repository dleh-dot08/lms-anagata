<?php

namespace App\Http\Controllers;

use App\Models\HelpdeskMessage;
use App\Models\HelpdeskTicket;
use Illuminate\Http\Request;

class HelpdeskMessageController extends Controller
{
    // Simpan pesan/chat pada tiket
    public function store(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = HelpdeskTicket::findOrFail($id);

        // Tentukan siapa pengirimnya
        $user = auth()->user();
        $senderType = 'guest';
        $userId = null;

        if ($user) {
            $userId = $user->id;

            if ($user->role_id == 1) {
                $senderType = 'admin'; // role_id 1 = admin
            } else {
                $senderType = 'user'; // selain admin dianggap user (peserta/mentor/karyawan)
            }

            // Bolehkan hanya peserta (pemilik tiket) atau admin
            if ($user->role_id != 1 && $ticket->user_id !== $user->id) {
                abort(403, 'Anda tidak berhak membalas tiket ini.');
            }
        }

        // Simpan pesan
        HelpdeskMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'sender_type' => $senderType,
            'message' => $request->message,
        ]);

        // Redirect sesuai role
        if ($senderType == 'admin') {
            return redirect()->route('admin.helpdesk.tickets.show', $ticket->id)
                            ->with('success', 'Pesan berhasil dikirim.');
        } else {
            return redirect()->route('peserta.helpdesk.tickets.show', $ticket->id)
                            ->with('success', 'Pesan berhasil dikirim.');
        }
    }
}
