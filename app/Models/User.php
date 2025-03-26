<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'foto_diri', 'tanggal_lahir', 'tempat_lahir',
        'alamat_tempat_tinggal', 'instansi', 'jenjang_id', 'jabatan',
        'bidang_pengajaran', 'divisi', 'no_telepon', 'tanggal_bergabung',
        'surat_tugas', 'role_id', 'created_by', 'updated_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

        // Relasi ke Role
        public function role()
        {
            return $this->belongsTo(Role::class);
        }
    
        // Relasi ke Jenjang
        public function jenjang()
        {
            return $this->belongsTo(Jenjang::class);
        }
    
        // Relasi ke User (Created By)
        public function creator()
        {
            return $this->belongsTo(User::class, 'created_by');
        }
    
        // Relasi ke User (Updated By)
        public function updater()
        {
            return $this->belongsTo(User::class, 'updated_by');
        }
}
