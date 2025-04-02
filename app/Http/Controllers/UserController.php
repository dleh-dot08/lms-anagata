<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        return view('users.create');
    }

    /**
     * Menyimpan user baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Simpan user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
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
        $user = User::withTrashed()->findOrFail($id); // Mengambil user termasuk yang terhapus (Soft Delete)
        $roles = Role::all();
        $jenjangs = Jenjang::all();

        return view('users.edit', compact('user', 'roles', 'jenjangs'));
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

        // Menghandle upload foto_diri jika ada
        if ($request->hasFile('foto_diri')) {
            if ($user->foto_diri && Storage::exists($user->foto_diri)) {
                Storage::delete($user->foto_diri);
            }
            $user->foto_diri = $request->file('foto_diri')->store('uploads/foto_diri', 'public');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
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
            'updated_by' => auth()->id(),
        ]);

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

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore(); // Restore from soft delete
        return redirect()->route('users.index')->with('success', 'User telah dipulihkan.');
    }

    
}
