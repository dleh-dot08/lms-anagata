<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SekolahController extends Controller
{
    public function index()
    {
        return view('layouts.sekolah.dashboard');
    }

    public function peserta(Request $request)
    {
        // Get the current logged-in school's ID
        $sekolahId = Auth::user()->sekolah_id;

        $query = User::with(['role', 'biodata', 'kelas'])
            ->where('role_id', 3)
            ->where('sekolah_id', $sekolahId)
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('biodata', function($q) use ($search) {
                      $q->where('no_hp', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by kelas
        if ($request->has('kelas_id') && $request->kelas_id != '') {
            $query->where('kelas_id', $request->kelas_id);
        }

        $peserta = $query->paginate(10);
        
        // Get all kelas for filter dropdown based on school's jenjang
        $sekolah = \App\Models\Sekolah::find($sekolahId);
        $kelas = \App\Models\Kelas::where('id_jenjang', $sekolah->jenjang_id)->get();

        return view('layouts.sekolah.peserta.index', compact('peserta', 'kelas'));
    }
}