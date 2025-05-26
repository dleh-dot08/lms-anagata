<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        $query = User::with(['role', 'biodata'])
            ->where('role_id', 3) // Only get participants (role_id 3)
            ->where('sekolah_id', $sekolahId) // Only get peserta assigned to this school
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

        $peserta = $query->paginate(10);

        return view('layouts.sekolah.peserta.index', compact('peserta'));
    }
} 