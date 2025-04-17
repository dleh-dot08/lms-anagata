<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kegiatan',
        'deskripsi',
        'jenjang_id',
        'status',
        'waktu_mulai',
        'waktu_akhir',
        'jumlah_peserta',
    ];

    // Relasi ke jenjang
    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class);
    }

    // Di model Activity
    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->using(ActivityUser::class)
                    ->withPivot(['tanggal_mulai', 'tanggal_daftar', 'tanggal_selesai', 'status'])
                    ->withTimestamps();
    }

    public function attendances()
{
    return $this->hasMany(Attendance::class);
}

}
