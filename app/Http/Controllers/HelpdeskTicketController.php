<?php

namespace App\Http\Controllers;

use App\Models\HelpdeskTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpdeskTicketController extends Controller
{
    // Menampilkan daftar tiket
    public function index()
    {
        if (auth()->user()->role_id == 1) {
            $tickets = HelpdeskTicket::latest()->get(); // Admin: semua tiket
        } else {
            $tickets = HelpdeskTicket::where('user_id', auth()->id())->latest()->get(); // Peserta: hanya miliknya
        }

        return view(
            auth()->user()->role_id == 1
                ? 'admin.helpdesk.index'
                : 'peserta.helpdesk.index',
            compact('tickets')
        );
    }

    // Form membuat tiket baru (hanya untuk peserta)
    public function create()
    {
        return view('peserta.helpdesk.create');
    }

    // Simpan tiket baru
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
        ]);

        $ticket = new HelpdeskTicket();
        $ticket->subject = $request->subject;
        $ticket->user_id = auth()->id();
        $ticket->status = 'open';
        $ticket->save();

        return redirect()->route('peserta.helpdesk.index')->with('success', 'Tiket berhasil dibuat.');
    }

    // Detail tiket + chat/message
    public function show($id)
    {
        $ticket = HelpdeskTicket::with('messages.user')->findOrFail($id);

        if (auth()->user()->role_id != 1 && $ticket->user_id != auth()->id()) {
            abort(403); // Peserta hanya bisa lihat tiket sendiri
        }

        return view(
            auth()->user()->role_id == 1
                ? 'admin.helpdesk.show'
                : 'peserta.helpdesk.show',
            compact('ticket')
        );
    }

    // Admin menutup tiket
    public function close($id)
    {
        $ticket = HelpdeskTicket::findOrFail($id);
        $ticket->status = 'closed';
        $ticket->closed_at = now();
        $ticket->save();

        return back()->with('success', 'Tiket berhasil ditutup.');
    }

    // Admin hapus tiket (opsional: soft delete)
    public function destroy($id)
    {
        $ticket = HelpdeskTicket::findOrFail($id);
        $ticket->delete(); // Kalau pakai soft deletes
        return back()->with('success', 'Tiket berhasil dihapus.');
    }

    public function guestCreate()
    {
        return view('guest.helpdesk.create');
    }

    public function guestStore(Request $request)
    {
        $request->validate([
            'guest_name' => 'required|string|max:100',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $ticket = HelpdeskTicket::create([
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'subject' => $request->subject,
            'status' => 'open',
        ]);

        HelpdeskMessage::create([
            'ticket_id' => $ticket->id,
            'message' => $request->message,
            'sender_type' => 'guest',
        ]);

        return redirect()->route('guest.helpdesk.create')->with('success', 'Tiket berhasil dikirim. Kami akan menghubungi Anda via email atau WhatsApp.');
    }
}
