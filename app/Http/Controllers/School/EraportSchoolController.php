<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Eraport; // Pastikan model ini ada & sesuai tabel rapor kamu
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EraportSchoolController extends Controller
{
    /**
     * Sekolah: list rapor peserta yang terikat pada sekolah tsb
     */
    public function index(Request $request)
    {
        $schoolUser = Auth::user();
        $studentIds = $this->getStudentIdsForSchool($schoolUser);

        // kalau mapping sekolah->peserta belum ditemukan
        if (empty($studentIds)) {
            $eraports = Eraport::query()->whereRaw('1=0')->paginate(10);
            return view('sekolah.eraport.index', compact('eraports'));
        }

        $q = Eraport::query()
            ->whereIn('user_id', $studentIds)
            ->where(function ($w) {
                // ✅ status kamu pakai kapital semua
                $w->where('status', 'PUBLISHED')
                ->orWhereNotNull('published_at');
            });

        // =========================
        // ✅ SEARCH (q)
        // =========================
        if ($request->filled('q')) {
            $kw = trim($request->q);

            $q->where(function ($w) use ($kw) {
                $w->where('report_number', 'like', "%{$kw}%")
                ->orWhere('snapshot_json', 'like', "%{$kw}%");
                // snapshot_json biasanya berisi student.name / course.title / semester.label
            });
        }

        // =========================
        // ✅ FILTER SEMESTER (semester)
        // =========================
        if ($request->filled('semester')) {
            $sem = trim($request->semester);
            $q->where('snapshot_json', 'like', "%{$sem}%");
            // kalau mau lebih spesifik: cari "semester":{"label":"..."}
            // tapi LIKE sederhana biasanya cukup.
        }

        // =========================
        // ✅ FILTER PROGRAM / COURSE (program)
        // =========================
        if ($request->filled('program')) {
            $prg = trim($request->program);
            $q->where('snapshot_json', 'like', "%{$prg}%");
        }

        // =========================
        // ✅ SORT (opsional)
        // sort=newest|oldest
        // =========================
        $sort = $request->get('sort', 'newest');
        if ($sort === 'oldest') {
            $q->orderBy('published_at')->orderBy('id');
        } else {
            $q->orderByDesc('published_at')->orderByDesc('id');
        }

        // ✅ 10 per page + tetap bawa query string saat pindah page
        $eraports = $q->paginate(10)->appends($request->query());

        return view('sekolah.eraport.index', compact('eraports'));
    }

    /**
     * Sekolah: download PDF rapor siswa milik sekolah tsb
     */
    public function download(Eraport $eraport)
    {
        $schoolUser = Auth::user();
        $studentIds = $this->getStudentIdsForSchool($schoolUser);

        // Guard: rapor harus milik siswa yang terdaftar di sekolah tersebut
        abort_if(empty($studentIds) || !in_array((int)$eraport->user_id, array_map('intval', $studentIds), true), 403, 'Akses ditolak.');

        // hanya jika publish
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

    protected function isPublished(Eraport $eraport): bool
    {
        return ($eraport->status === 'published') || !empty($eraport->published_at);
    }

    /**
     * Ambil daftar user_id peserta milik sekolah.
     * Karena struktur DB bisa beda, saya buat fallback beberapa pola umum.
     */
    protected function getStudentIdsForSchool($schoolUser): array
    {
        // 1) Pola umum: users.sekolah_id / users.school_id
        if (Schema::hasTable('users')) {
            // users.sekolah_id
            if (Schema::hasColumn('users', 'sekolah_id')) {
                // nilai sekolah_id bisa ada di akun sekolah atau di tabel sekolah terpisah
                $sekolahId = $schoolUser->sekolah_id ?? $schoolUser->id;
                return DB::table('users')
                    ->where('sekolah_id', $sekolahId)
                    ->pluck('id')
                    ->map(fn($v) => (int)$v)
                    ->toArray();
            }

            // users.school_id
            if (Schema::hasColumn('users', 'school_id')) {
                $schoolId = $schoolUser->school_id ?? $schoolUser->id;
                return DB::table('users')
                    ->where('school_id', $schoolId)
                    ->pluck('id')
                    ->map(fn($v) => (int)$v)
                    ->toArray();
            }
        }

        // 2) Pivot: sekolah_peserta (sekolah_id, user_id)
        if (Schema::hasTable('sekolah_peserta')) {
            $sekolahId = $schoolUser->sekolah_id ?? $schoolUser->id;
            return DB::table('sekolah_peserta')
                ->where('sekolah_id', $sekolahId)
                ->pluck('user_id')
                ->map(fn($v) => (int)$v)
                ->toArray();
        }

        // 3) Pivot: school_students (school_id, user_id)
        if (Schema::hasTable('school_students')) {
            $schoolId = $schoolUser->school_id ?? $schoolUser->id;
            return DB::table('school_students')
                ->where('school_id', $schoolId)
                ->pluck('user_id')
                ->map(fn($v) => (int)$v)
                ->toArray();
        }

        // 4) Fallback: tidak ketemu mapping
        return [];
    }
}
