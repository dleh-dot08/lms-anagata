<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolDocument;
use App\Models\Sekolah; // Pastikan model Sekolah di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Jika perlu audit siapa yang mengunduh/menghapus

class SchoolDocumentController extends Controller
{
    /**
     * Menampilkan daftar semua dokumen yang diunggah oleh semua sekolah.
     */
    public function index(Request $request)
    {
        $query = SchoolDocument::with(['sekolah', 'uploadedBy']);

        // Filter berdasarkan sekolah (opsional, jika Anda ingin admin bisa filter)
        if ($request->filled('sekolah_id')) {
            $query->where('sekolah_id', $request->sekolah_id);
        }
        // Filter berdasarkan jenis dokumen (opsional)
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        $documents = $query->latest()->paginate(10); // Paginate untuk performa

        // Data untuk filter dropdown (opsional)
        $listSekolah = Sekolah::orderBy('nama_sekolah')->get(['id', 'nama_sekolah']);
        $documentTypes = [
            'ktp_kepsek' => 'KTP Kepala Sekolah',
            'ktp_tu' => 'KTP Tata Usaha',
            'sertifikat_akreditasi' => 'Sertifikat Akreditasi',
            'npwp' => 'NPWP Sekolah',
            'logo' => 'Logo Sekolah',
        ];

        return view('admin.document.index', compact('documents', 'listSekolah', 'documentTypes'));
    }

    /**
     * Mengunduh dokumen yang diunggah oleh sekolah.
     */
    public function download(SchoolDocument $document)
    {
        // Admin biasanya punya akses untuk mengunduh semua dokumen
        // Tidak perlu cek kepemilikan seperti di sisi sekolah

        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download($document->file_path, $document->file_name);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan atau sudah dihapus.');
    }

    /**
     * Menghapus dokumen (Opsional, jika admin diizinkan menghapus).
     */
    // public function destroy(SchoolDocument $document)
    // {
    //     if (Storage::disk('public')->exists($document->file_path)) {
    //         Storage::disk('public')->delete($document->file_path);
    //     }

    //     $document->delete();

    //     return redirect()->back()->with('success', 'Dokumen berhasil dihapus oleh Admin.');
    // }
}