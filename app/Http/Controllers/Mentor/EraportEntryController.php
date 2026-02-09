<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\EraportBatch;
use App\Models\EraportEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EraportEntryController extends Controller
{
    public function index()
    {
        // batch yang masih bisa diinput mentor
        $batches = EraportBatch::whereIn('status', [
                EraportBatch::STATUS_DRAFT,
                EraportBatch::STATUS_VALIDATING,
                EraportBatch::STATUS_READY,
                EraportBatch::STATUS_REOPENED,
            ])
            ->orderByDesc('id')
            ->paginate(20);

        return view('mentor.eraport.batches.index', compact('batches'));
    }

    public function edit(EraportEntry $entry)
    {
        if ($entry->locked_at) {
            return back()->with('error', 'Entry sudah dikunci (batch publish).');
        }

        $entry->load('batch.course.kategori');

        $courseCategoryName = optional($entry->batch->course->kategori)->nama_kategori;

        return view('mentor.eraport.entries.edit', compact('entry', 'courseCategoryName'));
    }

    public function update(Request $request, EraportEntry $entry)
    {
        if ($entry->locked_at) {
            return back()->with('error', 'Entry sudah dikunci (batch publish).');
        }

        $data = $request->validate([
            'platform' => ['nullable','string','max:80'],

            'avg_project_score' => ['nullable','numeric','min:0','max:100'],
            'logic_score' => ['nullable','numeric','min:0','max:100'],
            'logic_predicate' => ['nullable','string','max:30'],

            'creativity_score' => ['nullable','numeric','min:0','max:100'],
            'creativity_predicate' => ['nullable','string','max:30'],

            'mentor_note' => ['nullable','string','max:5000'],

            'hadir_count' => ['nullable','integer','min:0'],
            'sakit_count' => ['nullable','integer','min:0'],
            'izin_count' => ['nullable','integer','min:0'],
            'alpha_count' => ['nullable','integer','min:0'],
        ]);

        $data['mentor_id'] = Auth::id();

        $entry->update($data);

        return back()->with('success', 'Data e-raport tersimpan.');
    }

    public function showBatch(EraportBatch $batch)
    {
        // OPTIONAL: Batasi mentor hanya bisa melihat batch untuk course yang dia pegang.
        // Karena struktur tabel assignment mentor bisa beda-beda, saya buat fleksibel:
        $mentorId = Auth::id();

        $isAssigned = null;

        // Contoh 1: tabel course_mentors (course_id, mentor_id)
        if (Schema::hasTable('course_mentors')) {
            $isAssigned = DB::table('course_mentors')
                ->where('course_id', $batch->course_id)
                ->where('mentor_id', $mentorId)
                ->exists();
        }

        // Contoh 2: tabel course_user (course_id, user_id, role/jenis)
        if ($isAssigned === null && Schema::hasTable('course_user')) {
            $q = DB::table('course_user')
                ->where('course_id', $batch->course_id)
                ->where('user_id', $mentorId);

            // kalau ada kolom role/jenis, silakan aktifkan salah satu:
            // if (Schema::hasColumn('course_user', 'role_id')) $q->where('role_id', 2);
            // if (Schema::hasColumn('course_user', 'jenis')) $q->where('jenis', 'mentor');

            $isAssigned = $q->exists();
        }

        // Kalau tabel assignment mentor tidak ditemukan, default: tidak memblok.
        // Kalau kamu mau "fail safe" (lebih ketat), ubah jadi abort(403) saat $isAssigned === null.
        if ($isAssigned === false) {
            abort(403, 'Anda tidak terdaftar sebagai mentor untuk course batch ini.');
        }

        $batch->load(['course', 'template']);

        $entries = EraportEntry::where('batch_id', $batch->id)
            ->with('student')
            ->orderBy('id')
            ->paginate(50);

        return view('mentor.eraport.batches.show', compact('batch', 'entries'));
    }
}
