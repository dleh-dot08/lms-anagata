<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Jenjang; // Import model Jenjang
use App\Models\Sekolah; // Import model Sekolah
use App\Models\Kelas;

class Enrollment extends Pivot
{
    protected $table = 'enrollments';
    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'mentor_id',
        'course_id',
        'tanggal_daftar',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'jenjang_id', 
        'sekolah_id', 
        'kelas_id',   
    ];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'jenjang_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class,'kelas_id');
    }


}
