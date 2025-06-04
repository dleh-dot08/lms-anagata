<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Biodata;
use App\Models\Jenjang;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
            case 6: // Sekolah
                return view('layouts.sekolah.biodata.index', compact('user', 'biodata'));
            default:
                return view('layouts.peserta.biodata.index', compact('user', 'biodata'));
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $jenjangs = Jenjang::all();
        $kelas = Kelas::all();
        $biodata = Biodata::where('id_user', $id)->first();

        switch ($user->role_id) {
            case 2: // Mentor
                return view('layouts.mentor.biodata.edit', compact('user', 'jenjangs', 'kelas', 'biodata'));
            case 4: // Karyawan
                return view('layouts.karyawan.biodata.edit', compact('user', 'jenjangs', 'kelas', 'biodata'));
            case 3: // Peserta
                return view('layouts.peserta.biodata.edit', compact('user', 'jenjangs', 'kelas', 'biodata'));
            case 6: // Sekolah
                return view('layouts.sekolah.biodata.edit', compact('user', 'jenjangs', 'kelas', 'biodata'));
            default:
                return view('layouts.peserta.biodata.edit', compact('user', 'jenjangs', 'kelas', 'biodata'));
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role_id != '1' && Auth::id() != $id) {
            abort(403, 'Unauthorized access.');
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'no_telepon' => 'nullable|string|max:14',
            'alamat' => 'nullable|string',
            'alamat_tempat_tinggal' => 'nullable|string',
            'nip' => 'nullable|string|max:20|unique:biodata,nip,' . $id . ',id_user',
            'nik' => 'nullable|string|max:20|unique:biodata,nik,' . $id . ',id_user',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:15',
            'instansi' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'jenjang_id' => 'nullable|exists:jenjang,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'foto' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:5120',
            'foto_ktp' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:5120',
            'data_ttd' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:5120',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'media_sosial' => 'nullable|string|max:255',
            'bidang_pengajaran' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|string|max:10|in:Laki-laki,Perempuan',
        ], [
            'nik.unique' => 'NIK sudah terdaftar di akun lain.',
            'nip.unique' => 'NIP sudah terdaftar di akun lain.',
            'email.unique' => 'Email sudah digunakan.',
            'foto.max' => 'Ukuran foto profil tidak boleh lebih dari 5MB.',
            'foto_ktp.max' => 'Ukuran foto KTP tidak boleh lebih dari 5MB.',
            'data_ttd.max' => 'Ukuran tanda tangan tidak boleh lebih dari 5MB.',
            'foto.mimes' => 'Format foto profil harus JPG, PNG, atau GIF.',
            'foto_ktp.mimes' => 'Format foto KTP harus JPG, PNG, atau GIF.',
            'data_ttd.mimes' => 'Format tanda tangan harus JPG, PNG, atau GIF.',
        ]);


        if ($validator->fails()) {
            $errors = $validator->errors();
        
            // If error is about NIK or NIP → only red box, no warning
            if ($errors->has('nik') || $errors->has('nip')) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        
            // If error is about foto size → add a specific warning
            if ($errors->has('foto')) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('warning', 'Ukuran foto profil terlalu besar. Maksimal 2MB.');
            }
        
            // For other general errors → general warning
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('warning', 'Periksa kembali: ada data yang tidak valid.');
        }
        

        $user = User::findOrFail($id);
        $biodata = Biodata::where('id_user', $user->id)->firstOrFail();

        // Update data Users
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'alamat_tempat_tinggal' => $request->alamat_tempat_tinggal,
            'jenis_kelamin' => $request->jenis_kelamin,
            'instansi' => $request->instansi,
            'pekerjaan' => $request->pekerjaan,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'media_sosial' => $request->media_sosial,
            'bidang_pengajaran' => $request->bidang_pengajaran, 
            'jenjang_id' => $request->jenjang_id,
            'kelas_id' => $request->kelas_id,
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
