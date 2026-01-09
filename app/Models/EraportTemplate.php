<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EraportTemplate extends Model
{
    use HasFactory;

    protected $table = 'eraport_templates';

    protected $fillable = [
        'name',
        'code',
        'layout_type',
        'view_path',
        'html',
        'css',
        'background_path',
        'field_map',
        'jenjang_id',
        'is_active',
        'config',
    ];

    protected $casts = [
        'field_map' => 'array',
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function batches()
    {
        return $this->hasMany(EraportBatch::class, 'template_id');
    }

    // Jika Anda punya model Jenjang, bisa diaktifkan:
    // public function jenjang()
    // {
    //     return $this->belongsTo(Jenjang::class, 'jenjang_id');
    // }
}
