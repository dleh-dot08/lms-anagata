<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'mentor_id',
        'nama_kelas',
        'deskripsi',
        'kategori_id',
        'jenjang_id',
        'level',
        'status',
        'waktu_mulai',
        'waktu_akhir',
        'harga',
        'jumlah_peserta',
    ];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'jenjang_id');
    }
}
