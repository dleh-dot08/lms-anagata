<?php

namespace App\Http\Controllers;

use App\Models\Jenjang;
use Illuminate\Http\Request;

class JenjangController extends Controller
{
    // Index: Menampilkan semua data jenjang
    public function index()
    {
        $jenjangs = Jenjang::withTrashed()->paginate(10);
        return view('jenjang.index', compact('jenjangs'));
    }

    // Create: Menampilkan form pembuatan jenjang
    public function create()
    {
        return view('jenjang.create');
    }

    // Store: Menyimpan data jenjang baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_jenjang' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        Jenjang::create([
            'nama_jenjang' => $request->nama_jenjang,
            'description' => $request->description,
        ]);

        return redirect()->route('jenjang.index')->with('success', 'Jenjang berhasil ditambahkan.');
    }

    // Show: Menampilkan detail jenjang
    public function show($id)
    {
        $jenjang = Jenjang::withTrashed()->findOrFail($id);
        return view('jenjang.show', compact('jenjang'));
    }

    // Edit: Menampilkan form edit jenjang
    public function edit($id)
    {
        $jenjang = Jenjang::findOrFail($id);
        return view('jenjang.edit', compact('jenjang'));
    }

    // Update: Mengupdate data jenjang
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jenjang' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        $jenjang = Jenjang::findOrFail($id);
        $jenjang->update([
            'nama_jenjang' => $request->nama_jenjang,
            'description' => $request->description,
        ]);

        return redirect()->route('jenjang.index')->with('success', 'Jenjang berhasil diupdate.');
    }

    // Delete: Menghapus data jenjang (Soft Delete)
    public function destroy($id)
    {
        $jenjang = Jenjang::findOrFail($id);
        $jenjang->delete();

        return redirect()->route('jenjang.index')->with('success', 'Jenjang berhasil dihapus.');
    }

    // Restore: Mengembalikan data yang sudah dihapus (soft deleted)
    public function restore($id)
    {
        $jenjang = Jenjang::onlyTrashed()->findOrFail($id);
        $jenjang->restore();

        return redirect()->route('jenjang.index')->with('success', 'Jenjang berhasil direstore.');
    }
}
