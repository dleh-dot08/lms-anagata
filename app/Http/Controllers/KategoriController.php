<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    // Menampilkan daftar kategori
    public function index()
    {
        $kategoris = Kategori::latest()->paginate(10); // Mengambil data kategori dengan pagination
        return view('kategori.index', compact('kategoris'));
    }

    // Menampilkan form untuk menambah kategori baru
    public function create()
    {
        return view('kategori.create');
    }

    // Menyimpan kategori baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Menyimpan data kategori ke database
        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'description' => $request->description,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // Menampilkan detail kategori
    public function show(Kategori $kategori)
    {
        return view('kategori.show', compact('kategori'));
    }

    // Menampilkan form untuk mengedit kategori
    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    // Memperbarui data kategori
    public function update(Request $request, Kategori $kategori)
    {
        // Validasi input
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Memperbarui data kategori
        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'description' => $request->description,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    // Menghapus kategori secara soft delete
    public function destroy(Kategori $kategori)
    {
        $kategori->delete(); // Soft delete
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    // Mengembalikan kategori yang dihapus (restore)
    public function restore($id)
    {
        $kategori = Kategori::onlyTrashed()->findOrFail($id); // Mengambil kategori yang terhapus
        $kategori->restore(); // Restore kategori

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dipulihkan.');
    }
}
