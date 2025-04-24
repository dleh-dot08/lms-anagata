<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Jenjang;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('jenjang')->latest()->paginate(10);

        $user = auth()->user();

        if ($user->role_id === 1) {
            return view('activities.index', compact('activities'));
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kegiatan.index', compact('activities'));
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    public function create()
    {
        $jenjangs = Jenjang::all();

        $user = auth()->user();

        if ($user->role_id === 1) {
            return view('activities.create', compact('jenjangs'));
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kegiatan.create', compact('jenjangs'));
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenjang_id' => 'required|exists:jenjang,id',
            'waktu_mulai' => 'required|date',
            'waktu_akhir' => 'required|date|after_or_equal:waktu_mulai',
            'jumlah_peserta' => 'nullable|integer',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        Activity::create($request->all());

        $user = auth()->user();

        if ($user->role_id === 1) {
            return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return redirect()->route('activities.apd.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    public function show(Activity $activity)
    {
        $participants = $activity->users()->with('jenjang')->paginate(10);

        $user = auth()->user();

        if ($user->role_id === 1) {
            return view('activities.show', compact('activity', 'participants'));
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kegiatan.show', compact('activity', 'participants'));
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    public function edit(Activity $activity)
    {
        $jenjangs = Jenjang::all();
        return view('activities.edit', compact('activity', 'jenjangs'));
    }

    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenjang_id' => 'required|exists:jenjang,id',
            'waktu_mulai' => 'required|date',
            'waktu_akhir' => 'required|date|after_or_equal:waktu_mulai',
            'jumlah_peserta' => 'nullable|integer',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        $activity->update($request->all());

        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function manageParticipants(Activity $activity)
    {
        $perPage = request('perPage', 10);

        $users = User::whereDoesntHave('activities', function ($query) use ($activity) {
            $query->where('activity_id', $activity->id);
        })->get();

        $participants = $activity->users;

        return view('activities.participants', compact('activity', 'users', 'participants'));
    }

    public function addParticipant(Request $request, Activity $activity)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_daftar' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|string',
        ]);

        if ($activity->users()->count() >= $activity->jumlah_peserta) {
            return redirect()->back()->with('error', 'Jumlah peserta telah mencapai batas maksimal.');
        }

        $activity->users()->attach($request->user_id, [
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_daftar' => $request->tanggal_daftar,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function addParticipantForm(Activity $activity, Request $request)
    {
        $jenjangId = $activity->jenjang_id;

        $query = User::whereHas('jenjang', function ($q) use ($jenjangId) {
            $q->where('id', $jenjangId);
        });

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        $perPage = $request->input('perPage', 10);
        if ($perPage === 'all') {
            $participants = $query->get();
        } else {
            $participants = $query->paginate((int)$perPage)->appends($request->all());
        }

        return view('activities.participants', compact('activity', 'participants', 'perPage'));
    }
    public function storeParticipants(Request $request, Activity $activity)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);
    
        $now = now();
    
        foreach ($request->user_ids as $userId) {
            $activity->users()->syncWithoutDetaching([
                $userId => [
                    'tanggal_daftar' => $now,
                    'tanggal_mulai' => $now,
                    'status' => 'Aktif',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            ]);
        }
    
        return redirect()->route('activities.show', $activity->id)
                         ->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function removeParticipant(Activity $activity, User $user)
    {
        // Hapus relasi user dari activity
        $activity->users()->detach($user->id);

        return redirect()->route('activities.show', $activity->id)->with('success', 'Peserta berhasil dihapus.');
    }
}
