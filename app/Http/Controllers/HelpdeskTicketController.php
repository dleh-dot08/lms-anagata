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
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:15', // Optional phone
        ]);
    
        $ticket = new HelpdeskTicket();
        $ticket->subject = $validated['subject'];
        $ticket->guest_name = $validated['guest_name'];
        $ticket->guest_email = $validated['guest_email'];
        $ticket->guest_phone = $validated['guest_phone'];
        $ticket->status = 'open';  // Status default
        $ticket->save();
    
        // Create a default message (or let the user create a message after ticket creation)
        $message = new HelpdeskMessage();
        $message->ticket_id = $ticket->id;
        $message->user_id = null;  // No user, because it's from guest
        $message->sender_type = 'guest';
        $message->message = "Ticket created by guest.";
        $message->save();
    
        return response()->json(['status' => 'ticket_created', 'ticket_id' => $ticket->id]);
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
