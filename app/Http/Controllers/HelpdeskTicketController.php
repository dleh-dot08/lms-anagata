<?php

namespace App\Http\Controllers;

use App\Models\HelpdeskTicket;
use App\Models\HelpdeskMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpdeskTicketController extends Controller
{
    // Menampilkan daftar tiket
    public function index()
    {
        if (auth()->user()->role_id == 1) {
            // Admin: Semua tiket dengan paginasi
            $tickets = HelpdeskTicket::latest()->paginate(10); // Paginasi 10 tiket per halaman
        } else {
            // Peserta: Hanya tiket miliknya sendiri dengan paginasi
            $tickets = HelpdeskTicket::where('user_id', auth()->id())->latest()->paginate(10); // Paginasi 10 tiket per halaman
        }

        // Menentukan view yang akan digunakan berdasarkan role pengguna
        return view(
            (auth()->user()->role_id == 1) ? 'admin.helpdesk.index' :
            ((auth()->user()->role_id == 3) ? 'peserta.helpdesk.index' :
            ((auth()->user()->role_id == 4) ? 'karyawan.helpdesk.index' : 'guest.helpdesk.index')),
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
        $isGuest = !auth()->check();

        // Validasi untuk guest & peserta
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'guest_name' => $isGuest ? 'required|string|max:255' : 'nullable',
            'guest_email' => $isGuest ? 'required|email|max:255' : 'nullable',
            'guest_phone' => $isGuest ? 'nullable|string|max:15' : 'nullable',
        ]);

        $ticket = new HelpdeskTicket();
        $ticket->subject = $validated['subject'];
        $ticket->status = 'open';

        if ($isGuest) {
            $ticket->guest_name = $validated['guest_name'];
            $ticket->guest_email = $validated['guest_email'];
            $ticket->guest_phone = $validated['guest_phone'];
            $ticket->user_id = null;
        } else {
            $ticket->user_id = auth()->id();
        }

        $ticket->save();

        // Simpan pesan pertama
        $message = new HelpdeskMessage();
        $message->ticket_id = $ticket->id;
        $message->message = $validated['message'];
        $message->user_id = $isGuest ? null : auth()->id();
        $message->sender_type = $isGuest ? 'guest' : 'user';
        $message->save();

        return response()->json([
            'status' => 'ticket_created',
            'ticket_id' => $ticket->id,
            'message' => 'Tiket berhasil dibuat. Tim kami akan segera menghubungi Anda.'
        ]);
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
