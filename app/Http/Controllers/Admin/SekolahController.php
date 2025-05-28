<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Jenjang;
use App\Models\Sekolah;
use App\Models\Kelas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SekolahController extends Controller
{
    public function index(Request $request)
    {
        $query = Sekolah::with(['jenjang', 'pic']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_sekolah', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhereHas('jenjang', function($q) use ($search) {
                      $q->where('nama_jenjang', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by jenjang
        if ($request->has('jenjang_id') && $request->jenjang_id != '') {
            $query->where('jenjang_id', $request->jenjang_id);
        }

        $sekolah = $query->orderBy('created_at', 'desc')->paginate(10);
        $jenjang = Jenjang::all();

        return view('admin.sekolah.index', compact('sekolah', 'jenjang'));
    }

    public function create()
    {
        $jenjang = Jenjang::all();
        $available_pics = User::where('role_id', 6)
            ->whereNull('sekolah_id')
            ->get();

        return view('admin.sekolah.create', compact('jenjang', 'available_pics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'alamat' => 'required|string',
            'jenjang_id' => 'required|exists:jenjang,id',
            'pic_id' => 'nullable|exists:users,id'
        ]);

        DB::beginTransaction();
        try {
            $sekolah = Sekolah::create([
                'nama_sekolah' => $request->nama_sekolah,
                'alamat' => $request->alamat,
                'jenjang_id' => $request->jenjang_id
            ]);

            if ($request->pic_id) {
                User::where('id', $request->pic_id)->update(['sekolah_id' => $sekolah->id]);
            }

            DB::commit();
            return redirect()->route('admin.sekolah.index')->with('success', 'Sekolah berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat menambahkan sekolah');
        }
    }

    public function edit($id)
    {
        $sekolah = Sekolah::with(['jenjang', 'pic'])->findOrFail($id);
        $jenjang = Jenjang::all();
        $kelas = Kelas::all();
        $available_pics = User::where('role_id', 6)
            ->where(function($query) use ($sekolah) {
                $query->whereNull('sekolah_id')
                      ->orWhere('sekolah_id', $sekolah->id);
            })
            ->get();

        // Get all peserta (role_id 3) with matching jenjang_id
        $available_peserta = User::where('role_id', 3)
            ->where('jenjang_id', $sekolah->jenjang_id)  // Filter by matching jenjang_id
            ->with(['sekolah', 'kelas'])  // Eager load sekolah and kelas relationships
            ->orderBy('name')
            ->get();

        return view('admin.sekolah.edit', compact('sekolah', 'jenjang', 'kelas', 'available_pics', 'available_peserta'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'alamat' => 'required|string',
            'jenjang_id' => 'required|exists:jenjang,id',
            'pic_id' => 'nullable|exists:users,id'
        ]);

        DB::beginTransaction();
        try {
            $sekolah = Sekolah::findOrFail($id);
            $sekolah->update([
                'nama_sekolah' => $request->nama_sekolah,
                'alamat' => $request->alamat,
                'jenjang_id' => $request->jenjang_id
            ]);

            // Update current PIC's sekolah_id to null
            User::where('sekolah_id', $sekolah->id)
                ->where('role_id', 6)
                ->update(['sekolah_id' => null]);

            // Assign new PIC if selected
            if ($request->pic_id) {
                User::where('id', $request->pic_id)
                    ->update(['sekolah_id' => $sekolah->id]);
            }

            DB::commit();
            return redirect()->route('admin.sekolah.index')->with('success', 'Sekolah berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui sekolah');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $sekolah = Sekolah::findOrFail($id);
            
            // Remove sekolah_id from associated users
            User::where('sekolah_id', $id)->update(['sekolah_id' => null]);
            
            $sekolah->delete();
            
            DB::commit();
            return redirect()->route('admin.sekolah.index')->with('success', 'Sekolah berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat menghapus sekolah');
        }
    }

    public function restore($id)
    {
        $sekolah = Sekolah::withTrashed()->findOrFail($id);
        $sekolah->restore();
        
        return redirect()->route('admin.sekolah.index')->with('success', 'Sekolah berhasil dipulihkan');
    }

    public function show($id)
    {
        $sekolah = Sekolah::with(['jenjang', 'pic', 'peserta'])->findOrFail($id);
        return view('admin.sekolah.show', compact('sekolah'));
    }

    public function updatePeserta(Request $request, $id)
    {
        $request->validate([
            'peserta_ids' => 'nullable|array',
            'peserta_ids.*' => 'exists:users,id'
        ]);

        DB::beginTransaction();
        try {
            $sekolah = Sekolah::findOrFail($id);
            
            // Remove all current peserta assignments for this school
            User::where('sekolah_id', $sekolah->id)
                ->where('role_id', 3)
                ->update(['sekolah_id' => null]);

            // Assign selected peserta to this school
            if ($request->has('peserta_ids')) {
                User::whereIn('id', $request->peserta_ids)
                    ->where('role_id', 3)
                    ->update(['sekolah_id' => $sekolah->id]);
            }

            DB::commit();
            return redirect()->route('admin.sekolah.show', $id)
                           ->with('success', 'Peserta berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui peserta');
        }
    }
} 