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
    use App\Models\User;

    public function index(Request $request)
    {
        // Mendapatkan pencarian dan filter role jika ada
        $search = $request->input('search');
        $roleId = $request->input('role');

        // Query untuk mendapatkan users, mengikutkan yang sudah dihapus (soft deleted)
        $usersQuery = User::withTrashed();

        // Pencarian berdasarkan nama atau email
        if ($search) {
            $usersQuery->where(function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan role, jika ada
        if ($roleId) {
            $usersQuery->where('role_id', $roleId);
        }

        // Ambil data dengan paginate
        $users = $usersQuery->paginate(10); // Pagination untuk 10 per halaman

        // Ambil semua role untuk filter di form
        $roles = Role::all();

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
        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
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
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        // Update data user
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
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
