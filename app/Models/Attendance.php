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
        'tanggal',
        'longitude',
        'latitude',
        'file_attache',
        'ttd_digital',
        'status',
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
