<?php

namespace App\Http\Controllers;

use App\Models\User;
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
                'email' => 'required|email|unique:users,email',
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

            // Prepare user data
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'created_by' => Auth::id(),
                'email_verified_at' => $request->has('verify_email') ? now() : null
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
        return view('users.import', compact('roles'));
    }

    /**
     * Import users from CSV file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            
            // Read CSV file
            $handle = fopen($path, "r");
            
            // Read header row
            $header = fgetcsv($handle);
            
            // Convert header to lowercase and trim
            $header = array_map(function($value) {
                return strtolower(trim($value));
            }, $header);
            
            // Required columns
            $requiredColumns = ['no', 'nama', 'email', 'waktu verifikasi email', 'password'];
            
            // Verify required columns exist
            foreach ($requiredColumns as $column) {
                if (!in_array($column, $header)) {
                    throw new \Exception("Kolom '$column' tidak ditemukan di file CSV");
                }
            }

            $users = [];
            $row = 1;
            
            while (($data = fgetcsv($handle)) !== false) {
                $row++;
                
                // Create associative array of row data
                $userData = array_combine($header, $data);
                
                // Validate email
                if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("Baris $row: Format email tidak valid");
                }
                
                // Check if email already exists
                if (User::where('email', $userData['email'])->exists()) {
                    throw new \Exception("Baris $row: Email sudah terdaftar");
                }

                // Convert date format from d/m/Y H:i:s to Y-m-d H:i:s
                $verificationDate = \DateTime::createFromFormat('d/m/Y H:i:s', $userData['waktu verifikasi email']);
                if (!$verificationDate) {
                    throw new \Exception("Baris $row: Format tanggal verifikasi tidak valid. Gunakan format dd/mm/yyyy HH:ii:ss");
                }
                
                // Prepare user data
                $user = [
                    'name' => $userData['nama'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => $verificationDate->format('Y-m-d H:i:s'),
                    'role_id' => $request->role_id,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $users[] = $user;
            }
            
            fclose($handle);
            
            // Insert all users
            User::insert($users);
            
            return redirect()->route('users.import.form')
                ->with('success', count($users) . ' user berhasil diimport');

        } catch (\Exception $e) {
            return redirect()->route('users.import.form')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
