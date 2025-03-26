<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'mentor_id',
        'course_id',
        'tanggal_daftar',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
