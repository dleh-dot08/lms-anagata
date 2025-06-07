<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'sekolah_id',
        'document_type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'uploaded_by_user_id',
    ];

    /**
     * Get the school that owns the document.
     */
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    /**
     * Get the user who uploaded the document.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}