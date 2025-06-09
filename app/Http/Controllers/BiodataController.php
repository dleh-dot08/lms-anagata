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
            'foto_3x4' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:5120',
            'ijazah' => 'nullable|mimes:pdf,jpg,png,jpeg|max:5120',
            'sertifikat.*' => 'nullable|mimes:pdf,jpg,png,jpeg|max:5120',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'media_sosial' => 'nullable|string|max:255',
            'bidang_pengajaran' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|string|max:10|in:Laki-laki,Perempuan',
            'nama_bank' => 'nullable|string|max:100',
            'nama_pemilik_bank' => 'nullable|string|max:100',
            'no_rekening' => 'nullable|string|max:50',
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
            'sertifikat.*.mimes' => 'Setiap file sertifikat harus JPG, PNG, JPEG, atau PDF.',
        ]);
    
        if ($validator->fails()) {
            $errors = $validator->errors();
    
            if ($errors->has('nik') || $errors->has('nip')) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
    
            if ($errors->has('foto')) {
                return redirect()->back()->withErrors($validator)->withInput()
                    ->with('warning', 'Ukuran foto profil terlalu besar. Maksimal 5MB.');
            }
    
            return redirect()->back()->withErrors($validator)->withInput()
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
            'nama_lengkap' => $request->name,
            'nip' => $request->nip,
            'nik' => $request->nik,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'nama_bank' => $request->nama_bank,
            'nama_pemilik_bank' => $request->nama_pemilik_bank,
            'no_rekening' => $request->no_rekening,
        ]);
    
        // Upload Foto Profil
        if ($request->hasFile('foto')) {
            if ($biodata->foto) {
                @unlink(public_path('storage/' . $biodata->foto));
            }
            $filename = uniqid() . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move(public_path('storage/data_file_foto_profil'), $filename);
            $biodata->foto = 'data_file_foto_profil/' . $filename;
        }
    
        // Upload Foto KTP
        if ($request->hasFile('foto_ktp')) {
            if ($biodata->data_ktp) {
                @unlink(public_path('storage/' . $biodata->data_ktp));
            }
            $filename = uniqid() . '.' . $request->file('foto_ktp')->getClientOriginalExtension();
            $request->file('foto_ktp')->move(public_path('storage/data_file_ktp'), $filename);
            $biodata->data_ktp = 'data_file_ktp/' . $filename;
        }
    
        // Upload Tanda Tangan
        if ($request->hasFile('data_ttd')) {
            if ($biodata->data_ttd) {
                @unlink(public_path('storage/' . $biodata->data_ttd));
            }
            $filename = uniqid() . '.' . $request->file('data_ttd')->getClientOriginalExtension();
            $request->file('data_ttd')->move(public_path('storage/data_file_ttd'), $filename);
            $biodata->data_ttd = 'data_file_ttd/' . $filename;
        }
    
        // Upload Foto 3x4
        if ($request->hasFile('foto_3x4')) {
            if ($biodata->foto_3x4) {
                @unlink(public_path('storage/' . $biodata->foto_3x4));
            }
            $filename = uniqid() . '.' . $request->file('foto_3x4')->getClientOriginalExtension();
            $request->file('foto_3x4')->move(public_path('storage/foto_3x4'), $filename);
            $biodata->foto_3x4 = 'foto_3x4/' . $filename;
        }
    
        // Upload Ijazah
        if ($request->hasFile('ijazah')) {
            if ($biodata->ijazah) {
                @unlink(public_path('storage/' . $biodata->ijazah));
            }
            $filename = uniqid() . '.' . $request->file('ijazah')->getClientOriginalExtension();
            $request->file('ijazah')->move(public_path('storage/ijazah'), $filename);
            $biodata->ijazah = 'ijazah/' . $filename;
        }
    
        // Upload Sertifikat (multiple)
        if ($request->hasFile('sertifikat')) {
            // Hapus file lama jika ada
            if ($biodata->sertifikat) {
                $oldFiles = json_decode($biodata->sertifikat, true);
                foreach ($oldFiles as $oldFile) {
                    @unlink(public_path('storage/' . $oldFile));
                }
            }
    
            $sertifikatPaths = [];
            foreach ($request->file('sertifikat') as $file) {
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/sertifikat'), $filename);
                $sertifikatPaths[] = 'sertifikat/' . $filename;
            }
            $biodata->sertifikat = json_encode($sertifikatPaths);
        }
    
        $biodata->save();
    
        return redirect()->route('biodata.index')->with('success', 'Biodata berhasil diperbarui.');
    }
    
    public function getKelasByJenjang($jenjangId)
    {
        $kelas = Kelas::where('jenjang_id', $jenjangId)->get(['id', 'nama_kelas']); // Sesuaikan dengan nama kolom Anda
        return response()->json($kelas);
    }

}
