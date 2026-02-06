<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Jenjang;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mengambil semua roles
        $roles = Role::all();

        // Mulai query untuk User
        $users = User::query();

        // Pencarian berdasarkan nama atau email
        if ($request->has('search') && $request->search != '') {
            $search = $request->get('search');
            $users->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan role
        if ($request->has('role') && $request->get('role') != '') {
            $roleId = $request->get('role');
            $users->where('role_id', $roleId);
        }

        // Menampilkan semua pengguna (termasuk yang dihapus)
        $users = $users->withTrashed()->paginate(10);

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Menampilkan form untuk menambah user baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::all();
        $jenjangs = Jenjang::all();
        return view('users.create', compact('roles', 'jenjangs'));
    }

    /**
     * Menyimpan user baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'role_id' => 'required|exists:roles,id',
                'foto_diri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'verify_email' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return redirect()->route('users.create')
                    ->withErrors($validator)
                    ->withInput();
            }

            $hasRealEmail = $request->filled('email');

            $email = $hasRealEmail
                ? $request->email
                : ('placeholder_' . uniqid() . '@placeholder.local');

            $emailIsPlaceholder = !$hasRealEmail;

            // Prepare user data
            $userData = [
                'name' => $request->name,
                'email' => $email,
                'email_is_placeholder' => $emailIsPlaceholder,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'created_by' => Auth::id(),
                'email_verified_at' => ($request->has('verify_email') && !$emailIsPlaceholder) ? now() : null,
            ];

            // Handle foto_diri upload if present
            if ($request->hasFile('foto_diri')) {
                $userData['foto_diri'] = $request->file('foto_diri')->store('uploads/foto_diri', 'public');
            }

            // Simpan user baru
            $user = User::create($userData);

            return redirect()->route('users.index')
                ->with('success', 'User berhasil dibuat.');

        } catch (\Exception $e) {
            return redirect()->route('users.create')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menampilkan detail user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Menampilkan form untuk mengedit user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $jenjangs = Jenjang::all();
        $kelas = Kelas::all();

        return view('users.edit', compact('user', 'roles', 'jenjangs', 'kelas'));
    }

    /**
     * Memperbarui data user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id); // Mengambil user termasuk yang terhapus

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'role_id' => 'required|exists:roles,id',
            'foto_diri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Prepare update data
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'jenis_kelamin' => $request->jenis_kelamin,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'pekerjaan' => $request->pekerjaan,
            'media_sosial' => $request->media_sosial,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tempat_lahir' => $request->tempat_lahir,
            'alamat_tempat_tinggal' => $request->alamat_tempat_tinggal,
            'instansi' => $request->instansi,
            'jenjang_id' => $request->jenjang_id,
            'jabatan' => $request->jabatan,
            'bidang_pengajaran' => $request->bidang_pengajaran,
            'divisi' => $request->divisi,
            'no_telepon' => $request->no_telepon,
            'tanggal_bergabung' => $request->tanggal_bergabung,
            'surat_tugas' => $request->surat_tugas,
            'updated_by' => Auth::id(),
        ];

        // Handle foto_diri upload if present
        if ($request->hasFile('foto_diri')) {
            // Delete old file if exists
            if ($user->foto_diri && Storage::disk('public')->exists($user->foto_diri)) {
                Storage::disk('public')->delete($user->foto_diri);
            }
            $updateData['foto_diri'] = $request->file('foto_diri')->store('uploads/foto_diri', 'public');
        }

        $user->update($updateData);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus user (soft delete).
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Soft delete
        return redirect()->route('users.index')->with('success', 'User telah dihapus.');
    }

    /**
     * Memulihkan user yang telah dihapus.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore(); // Restore from soft delete
        return redirect()->route('users.index')->with('success', 'User telah dipulihkan.');
    }

    /**
     * Show the form for importing users.
     *
     * @return \Illuminate\View\View
     */
    public function showImportForm()
    {
        $roles = Role::all();

        $jenjangs = Jenjang::query()
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get(['id', 'nama_jenjang']);

        $kelas = Kelas::query()
            ->whereNull('deleted_at')
            ->orderBy('id_jenjang')
            ->orderBy('id')
            ->get(['id', 'nama', 'id_jenjang']);

        return view('users.import', compact('roles', 'jenjangs', 'kelas'));
    }

    /**
     * Import users from CSV file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        @set_time_limit(0);

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $roleId = (int) $request->role_id;

            // ✅ definisikan: role peserta (sesuaikan kalau id kamu beda)
            $PESERTA_ROLE_ID = 3;
            $needJenjangKelas = ($roleId === $PESERTA_ROLE_ID);

            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            $handle = fopen($path, "r");
            $header = fgetcsv($handle);

            if (!$header) {
                throw new \Exception("File CSV kosong atau header tidak terbaca.");
            }

            $header = array_map(fn($v) => strtolower(trim($v)), $header);

            // ✅ wajib minimal untuk semua role
            $requiredColumns = ['nama', 'email', 'password'];
            foreach ($requiredColumns as $column) {
                if (!in_array($column, $header)) {
                    throw new \Exception("Kolom '$column' tidak ditemukan di file CSV");
                }
            }

            // ✅ kalau peserta, wajib ada kolom jenjang_id & kelas_id
            if ($needJenjangKelas) {
                foreach (['jenjang_id', 'kelas_id'] as $column) {
                    if (!in_array($column, $header)) {
                        throw new \Exception("Kolom '$column' wajib untuk role peserta.");
                    }
                }
            }

            $now = now();
            $adminId = Auth::id();

            // ✅ cache master jenjang & kelas untuk validasi jika diperlukan
            $validJenjangIds = [];
            $kelasMap = null;

            if ($needJenjangKelas) {
                $validJenjangIds = Jenjang::query()
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->flip()
                    ->toArray();

                $kelasMap = Kelas::query()
                    ->whereNull('deleted_at')
                    ->get(['id', 'id_jenjang'])
                    ->keyBy('id'); // kelas_id => model
            }

            $rows = [];
            $emails = [];
            $rowNum = 1;

            while (($data = fgetcsv($handle)) !== false) {
                $rowNum++;

                if (count(array_filter($data, fn($v) => trim((string)$v) !== '')) === 0) {
                    continue;
                }

                if (count($data) < count($header)) {
                    $data = array_pad($data, count($header), null);
                }

                $userData = array_combine($header, $data);

                $name  = trim((string)($userData['nama'] ?? ''));
                $email = trim((string)($userData['email'] ?? ''));
                $pass  = (string)($userData['password'] ?? '');

                if ($name === '') throw new \Exception("Baris $rowNum: Nama kosong");
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new \Exception("Baris $rowNum: Format email tidak valid");
                if ($pass === '') throw new \Exception("Baris $rowNum: Password kosong");

                $jenjangId = null;
                $kelasId = null;

                if ($needJenjangKelas) {
                    $jenjangId = (int)($userData['jenjang_id'] ?? 0);
                    $kelasId   = (int)($userData['kelas_id'] ?? 0);

                    if ($jenjangId <= 0 || !isset($validJenjangIds[$jenjangId])) {
                        throw new \Exception("Baris $rowNum: jenjang_id '$jenjangId' tidak valid.");
                    }
                    if ($kelasId <= 0 || !$kelasMap->has($kelasId)) {
                        throw new \Exception("Baris $rowNum: kelas_id '$kelasId' tidak valid.");
                    }
                    if ((int)$kelasMap[$kelasId]->id_jenjang !== $jenjangId) {
                        throw new \Exception("Baris $rowNum: kelas_id '$kelasId' tidak sesuai dengan jenjang_id '$jenjangId'.");
                    }
                } else {
                    // role mentor/admin dll: kolom jenjang_id/kelas_id boleh ada boleh tidak, kalau kosong -> null
                    $jenjangIdRaw = $userData['jenjang_id'] ?? null;
                    $kelasIdRaw   = $userData['kelas_id'] ?? null;
                    $jenjangId = (is_numeric($jenjangIdRaw) && (int)$jenjangIdRaw > 0) ? (int)$jenjangIdRaw : null;
                    $kelasId   = (is_numeric($kelasIdRaw) && (int)$kelasIdRaw > 0) ? (int)$kelasIdRaw : null;
                }

                $rows[] = [
                    'name' => $name,
                    'email' => $email,
                    'password_raw' => $pass,
                    'jenjang_id' => $jenjangId,
                    'kelas_id' => $kelasId,
                ];

                $emails[] = strtolower($email);
            }

            fclose($handle);

            if (count($rows) === 0) {
                throw new \Exception("Tidak ada data valid untuk diimport.");
            }

            // ✅ cek duplikat di CSV
            $dup = array_diff_assoc($emails, array_unique($emails));
            if (!empty($dup)) {
                $dupeEmail = array_values($dup)[0];
                throw new \Exception("Duplikat email di CSV: '$dupeEmail'");
            }

            // ✅ cek email sudah ada di DB (sekali query)
            $existing = User::query()
                ->whereIn('email', array_unique($emails))
                ->pluck('email')
                ->map(fn($e) => strtolower($e))
                ->toArray();

            if (!empty($existing)) {
                throw new \Exception("Email sudah terdaftar: " . implode(', ', array_slice($existing, 0, 5)) . (count($existing) > 5 ? ' ...' : ''));
            }

            // ✅ insert users
            $insertUsers = [];
            foreach ($rows as $r) {
                $insertUsers[] = [
                    'name' => $r['name'],
                    'email' => $r['email'],
                    'password' => Hash::make($r['password_raw']),
                    'role_id' => $roleId,
                    'created_by' => $adminId,
                    'jenjang_id' => $r['jenjang_id'],
                    'kelas_id' => $r['kelas_id'],

                    // ✅ auto verified (biar tidak ke /verify-email)
                    'email_verified_at' => $now,
                    'verified_by_admin_at' => $now,
                    'verified_by_admin_id' => $adminId,

                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            foreach (array_chunk($insertUsers, 500) as $chunk) {
                User::insert($chunk);
            }

            // ✅ ambil id user yang baru masuk (by email)
            $newUsers = User::query()
                ->whereIn('email', array_unique($emails))
                ->get(['id', 'email']);

            // ✅ bikin biodata untuk user yang belum punya biodata
            // asumsi tabel: biodata (kolom: user_id, created_at, updated_at)
            $newUserIds = $newUsers->pluck('id')->all();

            $existingBiodataUserIds = \DB::table('biodata')
                ->whereIn('id_user', $newUserIds)
                ->pluck('id_user')
                ->all();

            $existingSet = array_flip($existingBiodataUserIds);

            $insertBiodata = [];
            foreach ($newUserIds as $uid) {
                if (!isset($existingSet[$uid])) {
                    $insertBiodata[] = [
                        'id_user' => $uid,
                        'tanggal_bergabung' => $now->toDateString(), 
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($insertBiodata)) {
                foreach (array_chunk($insertBiodata, 500) as $chunk) {
                    \DB::table('biodata')->insert($chunk);
                }
            }

            return redirect()->route('users.import.form')
                ->with('success', count($insertUsers) . " user berhasil diimport. Biodata dibuat: " . count($insertBiodata));

        } catch (\Exception $e) {
            return redirect()->route('users.import.form')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }



    public function verifyByAdmin($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if (Auth::user()->role_id != 1) abort(403);

        $now = now();

        // 1) Update via Query Builder (bypass Model event/observer/mutator)
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'verified_by_admin_at' => $now,
                'verified_by_admin_id' => Auth::id(),
                'email_verified_at'    => $now,
                'updated_at'           => $now,
            ]);

        // 2) Ambil ulang dari DB untuk memastikan
        $fresh = User::withTrashed()->find($user->id);

        // sementara untuk cek (hapus setelah aman)
        // dd($fresh->id, $fresh->email_verified_at, $fresh->verified_by_admin_at);

        return back()->with('success', 'Akun diverifikasi oleh Admin.');
    }
}
