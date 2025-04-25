<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Biodata;
use App\Models\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BiodataController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $biodata = $user->biodata;
    
        switch ($user->role_id) {
            case 2: // Mentor
                return view('layouts.mentor.biodata.index', compact('user', 'biodata'));
            case 4: // Karyawan
                return view('layouts.karyawan.biodata.index', compact('user', 'biodata'));
            case 3: // Peserta
                return view('layouts.peserta.biodata.index', compact('user', 'biodata'));
            default:
                return view('layouts.peserta.biodata.index', compact('user', 'biodata'));
        }
    }

    public function edit($id)
    {
        $jenjangs = \App\Models\Jenjang::all(); // ambil semua jenjang
        // 🛡️ Block if user is not admin and tries to access other user's biodata
        if (Auth::user()->role_id != '1' && Auth::id() != $id) {
            abort(403, 'Unauthorized access.');
        }
    
        $user = User::findOrFail($id);
        $biodata = Biodata::where('id_user', $user->id)->first();
    
        switch ($user->role_id) {
            case 2: // Karyawan
                return view('layouts.mentor.biodata.edit', compact('user', 'biodata'));
    
            case 4: // Mentor
                return view('layouts.karyawan.biodata.edit', compact('user', 'biodata'));
    
            case 3: // Peserta
                return view('layouts.peserta.biodata.edit', compact('user', 'biodata'));
            default:
                return view('layouts.peserta.biodata.edit', compact('user', 'biodata'));
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role_id != '1' && Auth::id() != $id) {
            abort(403, 'Unauthorized access.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'no_telepon' => 'nullable|string|max:14',
            'alamat' => 'nullable|string',
            'nip' => 'nullable|string|max:20|unique:biodata,nip,' . $id . ',id_user',
            'nik' => 'nullable|string|max:20|unique:biodata,nik,' . $id . ',id_user',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:15',
            'foto' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'foto_ktp' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'data_ttd' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $user = User::findOrFail($id);
        $biodata = Biodata::where('id_user', $user->id)->firstOrFail();

        // Update data Users
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'alamat' => $request->alamat,
            'foto_diri' => $request->foto_diri
        ]);

        // Update data Biodata
        $biodata->update([
            'nama_lengkap' => $request->name, // Nama lengkap ikut diubah jika name diubah
            'nip' => $request->nip,
            'nik' => $request->nik,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'foto' => $request->foto,
        ]);

        // Upload Foto KTP jika ada file baru
        if ($request->hasFile('foto_ktp')) {
            if ($biodata->data_ktp) {
                @unlink(public_path('storage/' . $biodata->data_ktp));
            }

            $filename = uniqid() . '.' . $request->file('foto_ktp')->getClientOriginalExtension();
            $request->file('foto_ktp')->move(public_path('storage/data_file_ktp'), $filename);
            $biodata->data_ktp = 'data_file_ktp/' . $filename;
        }

        // Upload Tanda Tangan jika ada file baru
        if ($request->hasFile('data_ttd')) {
            if ($biodata->data_ttd) {
                @unlink(public_path('storage/' . $biodata->data_ttd));
            }

            $filename = uniqid() . '.' . $request->file('data_ttd')->getClientOriginalExtension();
            $request->file('data_ttd')->move(public_path('storage/data_file_ttd'), $filename);
            $biodata->data_ttd = 'data_file_ttd/' . $filename;
        }

        // Upload foto jika ada file baru
        if ($request->hasFile('foto')) {
            if ($biodata->foto) {
                @unlink(public_path('storage/' . $biodata->foto));
            }

            $filename = uniqid() . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move(public_path('storage/data_file_foto_profil'), $filename);
            $biodata->foto = 'data_file_foto_profil/' . $filename;
        }


        $biodata->save();

        return redirect()->route('biodata.index')->with('success', 'Biodata berhasil diperbarui.');
    }

}
