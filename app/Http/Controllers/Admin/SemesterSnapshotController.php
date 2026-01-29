<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterSnapshotController extends Controller
{
    public function index(Request $request)
    {
        $semesters = DB::table('semesters')->orderByDesc('id')->get();
        $schools   = DB::table('sekolah')->select('id', 'nama_sekolah')->orderBy('nama_sekolah')->get();

        $activeSemesterId = DB::table('semesters')->where('is_active', 1)->value('id');

        // hasil cek disimpan di session
        $check = session('snapshot_check'); // array|null

        return view('admin.semester_snapshot.index', compact(
            'semesters',
            'schools',
            'activeSemesterId',
            'check'
        ));
    }

    public function check(Request $request)
    {
        $data = $request->validate([
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'sekolah_id'  => ['nullable', 'integer', 'exists:sekolah,id'],
        ]);

        $semesterId = (int) $data['semester_id'];
        $schoolId   = !empty($data['sekolah_id']) ? (int) $data['sekolah_id'] : null;

        // Cari peserta (role_id=3) yang belum punya snapshot di student_semesters untuk semester ini
        $q = DB::table('users as u')
            ->leftJoin('sekolah as sc', 'sc.id', '=', 'u.sekolah_id')
            ->leftJoin('jenjang as j', 'j.id', '=', 'u.jenjang_id')
            ->leftJoin('kelas as k', 'k.id', '=', 'u.kelas_id')
            ->leftJoin('student_semesters as ss', function ($join) use ($semesterId) {
                $join->on('ss.user_id', '=', 'u.id')
                     ->where('ss.semester_id', '=', $semesterId);
            })
            ->where('u.role_id', 3)
            ->whereNull('ss.user_id');

        if ($schoolId) {
            $q->where('u.sekolah_id', $schoolId);
        }

        $totalMissing = (clone $q)->count();

        $rows = (clone $q)
            ->select([
                'u.id as user_id',
                'u.name as user_name',
                'u.sekolah_id',
                'sc.nama_sekolah as sekolah_nama',
                'u.jenjang_id',
                'j.nama_jenjang as jenjang_nama',
                'u.kelas_id',
                'k.nama as kelas_nama',
                'u.created_at',
            ])
            ->orderBy('sc.nama_sekolah')
            ->orderBy('j.nama_jenjang')
            ->orderBy('k.nama')
            ->orderBy('u.name')
            ->limit(500) // biar aman di UI, bisa dinaikkan
            ->get();

        return redirect()
            ->route('admin.semester_snapshot.index')
            ->with('snapshot_check', [
                'semester_id' => $semesterId,
                'sekolah_id'  => $schoolId,
                'total_missing' => $totalMissing,
                'rows' => $rows,
            ]);
    }

    public function apply(Request $request)
    {
        $data = $request->validate([
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'sekolah_id'  => ['nullable', 'integer', 'exists:sekolah,id'],

            'mode'        => ['required', 'in:selected,all'],
            'user_ids'    => ['nullable', 'array'],
            'user_ids.*'  => ['integer', 'exists:users,id'],
        ]);

        $semesterId = (int) $data['semester_id'];
        $schoolId   = !empty($data['sekolah_id']) ? (int) $data['sekolah_id'] : null;

        $mode = $data['mode'];
        $userIds = $data['user_ids'] ?? [];

        // kalau mode selected, wajib ada yang dicentang
        if ($mode === 'selected' && empty($userIds)) {
            return redirect()->route('admin.semester_snapshot.index')
                ->with('error', 'Mode "yang dicentang" dipilih, tapi tidak ada peserta yang dicentang.');
        }

        $now = now()->toDateTimeString();

        // INSERT yang belum punya snapshot semester ini, ambil data dari users (kelas/jenjang/sekolah)
        // Penting: kita left join student_semesters agar tidak double insert.
        $baseSql = "
            INSERT INTO student_semesters (user_id, sekolah_id, semester_id, jenjang_id, kelas_id, status, created_at, updated_at)
            SELECT
              u.id,
              u.sekolah_id,
              ? AS semester_id,
              u.jenjang_id,
              u.kelas_id,
              'aktif',
              ?,
              ?
            FROM users u
            LEFT JOIN student_semesters ss
              ON ss.user_id = u.id AND ss.semester_id = ?
            WHERE u.role_id = 3
              AND ss.user_id IS NULL
        ";

        $bindings = [$semesterId, $now, $now, $semesterId];

        if ($schoolId) {
            $baseSql .= " AND u.sekolah_id = " . (int)$schoolId;
        }

        if ($mode === 'selected') {
            $ids = array_map('intval', $userIds);
            $idsSql = implode(',', $ids);
            $baseSql .= " AND u.id IN ($idsSql)";
        }

        DB::beginTransaction();
        try {
            $affected = DB::affectingStatement($baseSql, $bindings);
            DB::commit();

            return redirect()->route('admin.semester_snapshot.index')
                ->with('success', "Snapshot berhasil ditambahkan. Baris baru: {$affected} (semester {$semesterId}).");

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.semester_snapshot.index')
                ->with('error', 'Gagal menambahkan snapshot: ' . $e->getMessage());
        }
    }
}
