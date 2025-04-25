<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    // INDEX untuk ADMIN
    public function indexAdmin(Request $request)
    {
        // Filter berdasarkan pencarian atau tipe
        $query = Certificate::query();

        // Pencarian berdasarkan nama peserta, kode sertifikat, dll.
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan tipe sertifikat (course atau activity)
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Mengambil sertifikat sesuai dengan filter, paginasi, dan pengurutan
        $certificates = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('certificates.admin.index', compact('certificates'));
    }

    // INDEX untuk PESERTA
    public function indexPeserta(Request $request)
    {
        // Filter pencarian berdasarkan nama kursus/kegiatan dan tipe sertifikat (kursus/aktivitas)
        $query = Certificate::query();

        // Pencarian berdasarkan nama kursus atau kegiatan
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('course', function ($q) use ($searchTerm) {
                    $q->where('nama_kelas', 'like', "%{$searchTerm}%");
                })
                ->orWhereHas('activity', function ($q) use ($searchTerm) {
                    $q->where('nama_kegiatan', 'like', "%{$searchTerm}%");
                });
            });
        }

        // Filter berdasarkan tipe (kursus atau aktivitas)
        if ($request->has('type') && $request->type != '') {
            $type = $request->type;
            if ($type == 'course') {
                $query->whereNotNull('course_id');
            } elseif ($type == 'activity') {
                $query->whereNotNull('activities_id');
            }
        }

        // Ambil sertifikat yang sesuai dengan filter dan paginate
        $certificates = $query->where('user_id', auth()->id())->paginate(10);

        return view('certificates.peserta.index', compact('certificates'));
    }

    // FORM CREATE - hanya untuk admin
    public function create()
    {
        $users = User::where('role_id', 3)->get(); // peserta
        $courses = Course::all();
        $activities = Activity::all();
        return view('certificates.admin.create', compact('users', 'courses', 'activities'));
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'kode_sertifikat' => 'required|unique:certificates,kode_sertifikat',
            'file_sertifikat' => 'required|string',
            'tanggal_terbit' => 'required|date',
            'status' => 'required|in:Diterbitkan,Belum Diterbitkan',
        ]);

        Certificate::create($request->all());

        return redirect()->route('certificates.indexAdmin')->with('success', 'Sertifikat berhasil dibuat.');
    }

    // SHOW - admin bisa lihat semua, peserta hanya bisa lihat miliknya
    /* public function showAdmin($id)
    {
        $certificate = Certificate::with('user', 'course', 'activity')->findOrFail($id);

        if (Auth::user()->role_id == 3 && $certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('certificates.show', compact('certificate'));
    } */

    public function showAdmin($id)
    {
        $certificate = Certificate::with('user', 'course', 'activity')->findOrFail($id);

        return view('certificates.showAdmin', compact('certificate'));
    }

    public function showPeserta($id)
    {
        $certificate = Certificate::with('user', 'course', 'activity')->findOrFail($id);

        return view('certificates.showPeserta', compact('certificate'));
    }

    // FORM EDIT
    public function edit($id)
    {
        $certificate = Certificate::findOrFail($id);
        $users = User::where('role_id', 3)->get(); // peserta
        $courses = Course::all();
        $activities = Activity::all();
        return view('certificates.admin.edit', compact('certificate', 'users', 'courses', 'activities'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $certificate = Certificate::findOrFail($id);

        $request->validate([
            'user_id' => 'required',
            'kode_sertifikat' => 'required|unique:certificates,kode_sertifikat,' . $certificate->id,
            'file_sertifikat' => 'required|string',
            'tanggal_terbit' => 'required|date',
            'status' => 'required|in:Diterbitkan,Belum Diterbitkan',
        ]);

        $certificate->update($request->all());

        return redirect()->route('certificates.indexAdmin')->with('success', 'Sertifikat berhasil diperbarui.');
    }
}
