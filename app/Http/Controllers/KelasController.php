<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Jenjang;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('jenjang')->paginate(10);
        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        $jenjangs = Jenjang::all();
        return view('admin.kelas.create', compact('jenjangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'id_jenjang' => 'required|exists:jenjang,id',
        ]);

        Kelas::create($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show($id)
    {
        $kelas = Kelas::with('jenjang')->findOrFail($id);
        return view('admin.kelas.show', compact('kelas'));
    }

    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        $jenjangs = Jenjang::all();
        return view('admin.kelas.edit', compact('kelas', 'jenjangs'));
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'id_jenjang' => 'required|exists:jenjang,id',
        ]);

        $kelas->update($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();
        
        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    public function restore($id)
    {
        $kelas = Kelas::withTrashed()->findOrFail($id);
        $kelas->restore();
        
        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dipulihkan.');
    }

    public function getKelasByJenjang($jenjangId)
    {
        $kelas = Kelas::where('id_jenjang', $jenjangId)->get();
        return response()->json($kelas);
    }
} 