<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentorNote extends Model
{
    protected $fillable = [
        'meeting_id', 'materi', 'project', 'sikap_siswa',
        'hambatan', 'solusi', 'masukan', 'lain_lain'
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}

