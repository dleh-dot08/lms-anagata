<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EraportRevision extends Model
{
    use HasFactory;

    protected $table = 'eraport_revisions';

    public $timestamps = false; // karena kita hanya pakai created_at

    protected $fillable = [
        'eraport_id',
        'version',
        'changed_by',
        'reason',
        'diff_json',
        'created_at',
    ];

    protected $casts = [
        'diff_json' => 'array',
        'created_at' => 'datetime',
    ];

    public function eraport()
    {
        return $this->belongsTo(Eraport::class, 'eraport_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
