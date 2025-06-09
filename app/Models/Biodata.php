<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Biodata extends Model
{
    use HasFactory;

    protected $table = 'biodata';
    protected $fillable = [
        'id_user', 'nip', 'nama_lengkap', 'jabatan', 'divisi', 'nik',
        'tempat_lahir', 'tanggal_lahir', 'foto', 'data_ktp','data_ttd', 'no_hp', 'alamat',
        'tanggal_bergabung', 'status', 'foto_ktp', 'ijazah', 'nama_bank', 'nama_pemilik_bank',
        'no_rekening', 'sertifikat'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
