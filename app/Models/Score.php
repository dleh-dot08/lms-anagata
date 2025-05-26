<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Score extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'meeting_id',
        'peserta_id',
        'mentor_id',
        'creativity_score',
        'program_score',
        'design_score',
        'total_score',
        'feedback'
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function peserta()
    {
        return $this->belongsTo(User::class, 'peserta_id');
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
} 