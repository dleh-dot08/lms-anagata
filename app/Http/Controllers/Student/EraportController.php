<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Eraport; // Pastikan model ini ada & sesuai tabel rapor kamu
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EraportController extends Controller
{
    /**
     * Peserta: list rapor miliknya
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $eraports = Eraport::query()
            ->where('user_id', $userId)
            ->where(function ($q) {
                // aman: jika kolom status belum selalu konsisten
                $q->where('status', 'published')
                  ->orWhereNotNull('published_at');
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('peserta.eraport.index', compact('eraports'));
    }

    /**
     * Peserta: detail rapor miliknya
     */
    public function show(Eraport $eraport)
    {
        $this->guardStudentOwnership($eraport);

        // biasanya rapor hanya bisa dilihat kalau sudah publish
        if (!$this->isPublished($eraport)) {
            abort(404);
        }

        return view('peserta.eraport.show', compact('eraport'));
    }

    /**
     * Peserta: download PDF rapor miliknya
     */
    public function download(Eraport $eraport)
    {
        $this->guardStudentOwnership($eraport);

        if (!$this->isPublished($eraport)) {
            return back()->with('error', 'E-raport belum diterbitkan.');
        }

        if (empty($eraport->pdf_path)) {
            return back()->with('error', 'File PDF belum tersedia.');
        }

        $disk = Storage::disk('public');
        if (!$disk->exists($eraport->pdf_path)) {
            return back()->with('error', 'File PDF tidak ditemukan di storage.');
        }

        $safeName = $eraport->report_number ?: ('eraport-' . $eraport->id);
        $filename = Str::slug($safeName) . '.pdf';

        return $disk->download($eraport->pdf_path, $filename);
    }

    /* =========================
       Helpers
    ========================= */

    protected function guardStudentOwnership(Eraport $eraport): void
    {
        abort_if($eraport->user_id !== Auth::id(), 403, 'Akses ditolak.');
    }

    protected function isPublished(Eraport $eraport): bool
    {
        return ($eraport->status === 'published') || !empty($eraport->published_at);
    }
}
