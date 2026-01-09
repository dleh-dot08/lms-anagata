<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EraportBatch extends Model
{
    use HasFactory;

    protected $table = 'eraport_batches';

    // Status constants biar rapi
    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_VALIDATING = 'VALIDATING';
    public const STATUS_READY = 'READY';
    public const STATUS_PUBLISHED = 'PUBLISHED';
    public const STATUS_REOPENED = 'REOPENED';

    protected $fillable = [
        'course_id',
        'template_id',
        'semester_id',
        'semester_label',
        'status',
        'created_by',
        'published_by',
        'published_at',
        'reopen_reason',
        'reopened_by',
        'reopened_at',
        'notes_admin',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'reopened_at' => 'datetime',
    ];

    // Relationships
    public function template()
    {
        return $this->belongsTo(EraportTemplate::class, 'template_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function entries()
    {
        return $this->hasMany(EraportEntry::class, 'batch_id');
    }

    public function raports()
    {
        return $this->hasMany(Eraport::class, 'batch_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function reopenedBy()
    {
        return $this->belongsTo(User::class, 'reopened_by');
    }

    // Scopes (opsional)
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }
}
