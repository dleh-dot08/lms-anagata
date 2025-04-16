<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'course_id',
        'activity_id',
        'tanggal',
        'waktu_absen',
        'longitude',
        'latitude',
        'file_attache',
        'ttd_digital',
        'status',
    ];

    protected $dates = ['tanggal'];

    // Relasi ke user (peserta)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

}
