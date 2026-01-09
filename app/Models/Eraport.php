<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eraport extends Model
{
    use HasFactory;

    protected $table = 'eraports';

    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_PUBLISHED = 'PUBLISHED';
    public const STATUS_REVISED = 'REVISED';
    public const STATUS_VOID = 'VOID';

    protected $fillable = [
        'batch_id',
        'user_id',
        'sekolah_id',
        'report_number',
        'verify_token',
        'version',
        'status',
        'snapshot_json',
        'pdf_path',
        'published_at',
        'published_by',
    ];

    protected $casts = [
        'snapshot_json' => 'array',
        'published_at' => 'datetime',
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

    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function revisions()
    {
        return $this->hasMany(EraportRevision::class, 'eraport_id');
    }
}
