<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EraportEntry extends Model
{
    use HasFactory;

    protected $table = 'eraport_entries';

    protected $fillable = [
        'batch_id',
        'user_id',
        'mentor_id',
        'platform',
        'category',
        'avg_project_score',
        'logic_score',
        'logic_predicate',
        'creativity_score',
        'creativity_predicate',
        'mentor_note',
        'hadir_count',
        'sakit_count',
        'izin_count',
        'alpha_count',
        'locked_at',
        'locked_by',
    ];

    protected $casts = [
        'avg_project_score' => 'decimal:2',
        'logic_score' => 'decimal:2',
        'creativity_score' => 'decimal:2',
        'locked_at' => 'datetime',
    ];

    // Relationships
    public function batch()
    {
        return $this->belongsTo(EraportBatch::class, 'batch_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    // Helper
    public function isLocked(): bool
    {
        return !is_null($this->locked_at);
    }
}
