<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterPromotionController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua kolom (karena di DB kamu tidak ada kolom 'nama')
        $semesters = DB::table('semesters')->orderByDesc('id')->get();

        // sekolah pakai nama_sekolah (sesuai DB kamu)
        $schools = DB::table('sekolah')->select('id', 'nama_sekolah')->orderBy('nama_sekolah')->get();

        $activeSemesterId = DB::table('semesters')->where('is_active', 1)->value('id');
        $activeCount = $activeSemesterId
            ? DB::table('student_semesters')->where('semester_id', $activeSemesterId)->count()
            : null;
        $snapshotCounts = DB::table('student_semesters as ss')
            ->join('users as u', 'u.id', '=', 'ss.user_id')
            ->where('u.role_id', 3)
            ->where('ss.status', 'aktif')
            ->select('ss.semester_id', DB::raw('COUNT(*) as total'))
            ->groupBy('ss.semester_id')
            ->pluck('total', 'semester_id'); // hasil: [semester_id => total]

        $preview = session('preview'); // hasil preview disimpan ke session

        return view('admin.semester_promote.index', compact(
            'semesters',
            'schools',
            'activeSemesterId',
            'activeCount',
            'preview',
            'snapshotCounts'
        ));
    }

    public function preview(Request $request)
    {
        $data = $request->validate([
            'from_semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'to_semester_id'   => ['required', 'integer', 'exists:semesters,id', 'different:from_semester_id'],
            'sekolah_id'       => ['nullable', 'integer', 'exists:sekolah,id'],
        ]);

        $from = (int) $data['from_semester_id'];
        $to   = (int) $data['to_semester_id'];
        $schoolId = $data['sekolah_id'] ? (int) $data['sekolah_id'] : null;

        $base = DB::table('student_semesters as ss')
            ->join('users as u', 'u.id', '=', 'ss.user_id')
            ->leftJoin('sekolah as sc', 'sc.id', '=', 'ss.sekolah_id')
            ->leftJoin('jenjang as j', 'j.id', '=', 'ss.jenjang_id')
            ->leftJoin('kelas_promotion_map as kpm', 'kpm.from_kelas_id', '=', 'ss.kelas_id')
            ->leftJoin('kelas as k1', 'k1.id', '=', 'ss.kelas_id')
            ->leftJoin('kelas as k2', 'k2.id', '=', DB::raw('COALESCE(kpm.to_kelas_id, ss.kelas_id)'))
            ->where('ss.semester_id', $from)
            ->where('u.role_id', 3)
            ->where('ss.status', 'aktif');

        if ($schoolId) {
            $base->where('ss.sekolah_id', $schoolId);
        }

        $total = (clone $base)->count();
        $fromCount = (int) (clone $base)->count();

        // hitung total snapshot di semester tujuan (untuk konteks "sudah di-bootstrapped belum")
        $toBase = DB::table('student_semesters as ss')
            ->join('users as u', 'u.id', '=', 'ss.user_id')
            ->where('ss.semester_id', $to)
            ->where('u.role_id', 3)
            ->where('ss.status', 'aktif');

        if ($schoolId) {
            $toBase->where('ss.sekolah_id', $schoolId);
        }

        $toCount = (int) $toBase->count();

        $coveragePct = $fromCount > 0 ? round(($toCount / $fromCount) * 100, 2) : 0;
        $willChange = (clone $base)->whereNotNull('kpm.to_kelas_id')->count();
        $noMapping  = (clone $base)->whereNull('kpm.to_kelas_id')->count();

        $rows = (clone $base)
            ->select([
                'ss.user_id',
                'u.name as user_name',

                'ss.sekolah_id',
                'sc.nama_sekolah as sekolah_nama',

                'ss.jenjang_id',
                'j.nama_jenjang as jenjang_nama',

                'ss.kelas_id as kelas_lama_id',
                'k1.nama as kelas_lama',

                DB::raw('COALESCE(kpm.to_kelas_id, ss.kelas_id) as kelas_baru_id'),
                'k2.nama as kelas_baru',
            ])
            ->orderBy('sc.nama_sekolah')
            ->orderBy('j.nama_jenjang')
            ->orderBy('k1.nama')
            ->orderBy('u.name')
            ->limit(200)
            ->get();

        return redirect()
            ->route('admin.semester_promote.index')
            ->with('preview', [
                'from' => $from,
                'to' => $to,
                'schoolId' => $schoolId,
                'total' => $total,
                'willChange' => $willChange,
                'noMapping' => $noMapping,
                'rows' => $rows,
                'fromCount' => $fromCount,
                'toCount' => $toCount,
                'coveragePct' => $coveragePct,
            ]);
    }

    public function activate(Request $request)
    {
        $data = $request->validate([
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'from_semester_id' => ['nullable', 'integer', 'exists:semesters,id'], // untuk rule 95%
            'sekolah_id' => ['nullable', 'integer', 'exists:sekolah,id'], // optional jika mau cek per sekolah
        ]);

        $targetId = (int) $data['semester_id'];
        $fromId = !empty($data['from_semester_id']) ? (int)$data['from_semester_id'] : null;
        $schoolId = !empty($data['sekolah_id']) ? (int)$data['sekolah_id'] : null;

        // Hitung snapshot target
        $qTarget = DB::table('student_semesters as ss')
            ->join('users as u', 'u.id', '=', 'ss.user_id')
            ->where('ss.semester_id', $targetId)
            ->where('u.role_id', 3)
            ->where('ss.status', 'aktif');

        if ($schoolId) $qTarget->where('ss.sekolah_id', $schoolId);

        $targetCount = (int) $qTarget->count();

        // Rule: minimal >0 (fallback)
        $ok = $targetCount > 0;
        $msgRule = 'minimal ada snapshot peserta (>0).';

        // Rule lebih aman: >=95% dari semester asal (kalau fromId dikirim)
        if ($fromId) {
            $qFrom = DB::table('student_semesters as ss')
                ->join('users as u', 'u.id', '=', 'ss.user_id')
                ->where('ss.semester_id', $fromId)
                ->where('u.role_id', 3)
                ->where('ss.status', 'aktif');

            if ($schoolId) $qFrom->where('ss.sekolah_id', $schoolId);

            $fromCount = (int) $qFrom->count();
            $pct = $fromCount > 0 ? ($targetCount / $fromCount) : 0;

            $ok = ($fromCount > 0) && ($pct >= 0.95);
            $msgRule = 'minimal 95% dari snapshot semester asal.';
        }

        if (!$ok) {
            return redirect()->route('admin.semester_promote.index')
                ->with('error', "Gagal mengaktifkan semester. Snapshot semester target belum memenuhi syarat ({$msgRule})");
        }

        DB::beginTransaction();
        try {
            DB::table('semesters')->update(['is_active' => 0]);
            DB::table('semesters')->where('id', $targetId)->update(['is_active' => 1]);
            DB::commit();

            return redirect()->route('admin.semester_promote.index')
                ->with('success', "Semester {$targetId} berhasil diaktifkan.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.semester_promote.index')
                ->with('error', 'Aktivasi semester gagal: ' . $e->getMessage());
        }
    }

    public function run(Request $request)
    {
        $data = $request->validate([
            'user_ids'   => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],

            'update_users_kelas' => ['nullable', 'in:0,1'],
            'mark_graduates'     => ['nullable', 'in:0,1'],

            'from_semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'to_semester_id'   => ['required', 'integer', 'exists:semesters,id', 'different:from_semester_id'],
            'sekolah_id'       => ['nullable', 'integer', 'exists:sekolah,id'],
        ]);

        $from = (int) $data['from_semester_id'];
        $to   = (int) $data['to_semester_id'];
        $schoolId = $data['sekolah_id'] ? (int) $data['sekolah_id'] : null;

        $markGraduates = ($data['mark_graduates'] ?? '0') === '1';
        $updateUsersKelas = ($data['update_users_kelas'] ?? '0') === '1';

        $userIds = $data['user_ids'] ?? [];

        // ✅ WAJIB PILIH MINIMAL 1 (biar gak promote semua tanpa sadar)
        if (empty($userIds)) {
            return redirect()->route('admin.semester_promote.index')
                ->with('error', 'Silakan centang minimal 1 peserta untuk dipromote.');
        }

        DB::beginTransaction();
        try {
            $src = DB::table('student_semesters as ss')
                ->join('users as u', 'u.id', '=', 'ss.user_id')
                ->leftJoin('kelas_promotion_map as kpm', 'kpm.from_kelas_id', '=', 'ss.kelas_id')
                ->where('ss.semester_id', $from)
                ->where('ss.status', 'aktif')
                ->where('u.role_id', 3)
                ->whereIn('ss.user_id', $userIds);

            if ($schoolId) {
                $src->where('ss.sekolah_id', $schoolId);
            }

            $payload = $src->select([
                'ss.user_id',
                'ss.sekolah_id',
                'ss.jenjang_id',
                DB::raw('COALESCE(kpm.to_kelas_id, ss.kelas_id) as kelas_baru_id'),
            ])->get();

            if ($payload->isEmpty()) {
                DB::rollBack();
                return redirect()->route('admin.semester_promote.index')
                    ->with('error', 'Tidak ada data peserta yang dipromote. (Filter tidak cocok / data tidak ada).');
            }

            $now = now()->toDateTimeString();
            $inserted = 0;

            foreach ($payload->chunk(500) as $chunk) {
                $valuesSql = [];
                $bindings = [];

                foreach ($chunk as $row) {
                    $valuesSql[] = "(?, ?, ?, ?, ?, ?, ?, ?)";
                    $bindings[] = (int)$row->user_id;
                    $bindings[] = $row->sekolah_id !== null ? (int)$row->sekolah_id : null;
                    $bindings[] = $to;
                    $bindings[] = $row->jenjang_id !== null ? (int)$row->jenjang_id : null;
                    $bindings[] = $row->kelas_baru_id !== null ? (int)$row->kelas_baru_id : null;
                    $bindings[] = 'aktif';
                    $bindings[] = $now;
                    $bindings[] = $now;
                }

                $sql = "
                    INSERT INTO student_semesters
                      (user_id, sekolah_id, semester_id, jenjang_id, kelas_id, status, created_at, updated_at)
                    VALUES " . implode(',', $valuesSql) . "
                    ON DUPLICATE KEY UPDATE
                      sekolah_id = VALUES(sekolah_id),
                      jenjang_id = VALUES(jenjang_id),
                      kelas_id   = VALUES(kelas_id),
                      status     = VALUES(status),
                      updated_at = VALUES(updated_at)
                ";

                DB::statement($sql, $bindings);
                $inserted += count($chunk);
            }

            // OPTIONAL: tandai lulus untuk kelas terakhir (yang tidak punya mapping)
            if ($markGraduates) {
                $qGraduate = DB::table('student_semesters as ss_new')
                    ->join('student_semesters as ss_old', function ($join) use ($from) {
                        $join->on('ss_old.user_id', '=', 'ss_new.user_id')
                            ->where('ss_old.semester_id', '=', $from);
                    })
                    ->leftJoin('kelas_promotion_map as kpm', 'kpm.from_kelas_id', '=', 'ss_old.kelas_id')
                    ->where('ss_new.semester_id', $to)
                    ->whereNull('kpm.from_kelas_id')
                    ->whereIn('ss_new.user_id', $userIds);

                if ($schoolId) $qGraduate->where('ss_new.sekolah_id', $schoolId);

                $qGraduate->update([
                    'ss_new.status' => 'lulus',
                    'ss_new.note' => 'Auto lulus (kelas terakhir)',
                    'ss_new.updated_at' => $now,
                ]);
            }

            // ✅ OPSI CEPAT: update users.kelas_id (pakai UPDATE JOIN MySQL paling aman)
            if ($updateUsersKelas) {
                $ids = array_map('intval', $userIds);
                $idsSql = implode(',', $ids);

                $sqlSync = "
                    UPDATE users u
                    JOIN student_semesters ss
                      ON ss.user_id = u.id
                     AND ss.semester_id = ?
                    SET u.kelas_id = ss.kelas_id,
                        u.updated_at = ?
                    WHERE u.role_id = 3
                      AND ss.status = 'aktif'
                      " . ($schoolId ? " AND ss.sekolah_id = " . (int)$schoolId : "") . "
                      AND u.id IN ($idsSql)
                ";

                DB::statement($sqlSync, [$to, $now]);
            }

            DB::commit();

            return redirect()->route('admin.semester_promote.index')
                ->with('success', "Promote selesai. Diproses: {$inserted} peserta (semester {$from} → {$to}).");

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.semester_promote.index')
                ->with('error', 'Promote gagal: ' . $e->getMessage());
        }
    }
}
