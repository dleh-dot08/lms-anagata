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
    public function indexAdmin()
    {
        $certificates = Certificate::with('user', 'course', 'activity')->latest()->get();
        return view('certificates.admin.index', compact('certificates'));
    }

    // INDEX untuk PESERTA
    public function indexPeserta()
    {
        $certificates = Certificate::where('user_id', Auth::id())
            ->with('course', 'activity')
            ->latest()->get();

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
    public function show($id)
    {
        $certificate = Certificate::with('user', 'course', 'activity')->findOrFail($id);

        if (Auth::user()->role_id == 3 && $certificate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('certificates.show', compact('certificate'));
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
