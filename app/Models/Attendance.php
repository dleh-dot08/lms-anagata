<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'enrollment_id',
        'meeting_id',
        'user_id',
        'course_id',
        'activity_id',
        'tanggal',
        'waktu_absen',
        'longitude',
        'latitude',
        'file_attache',
        'ttd_digital',
        'recorded_by_user_id',
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

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class);
    }

    public function recordedByMentor() // Nama relasi untuk mentor yang merekam
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id'); // Menggunakan 'recorded_by_user_id'
    }

}
