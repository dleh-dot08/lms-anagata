<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
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
     * The attributes that should be cast.
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

    /**
     * Relasi ke Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relasi ke Jenjang
     */
    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class);
    }

    /**
     * Relasi ke User (Created By)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User (Updated By)
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function getMentors()
    {
        return self::where('role_id', 2)->get(); // Role mentor
    }

    /**
     * Relasi ke Biodata
     */
    public function biodata()
    {
        return $this->hasOne(Biodata::class, 'id_user');
    }

    
    /**
     * Method untuk soft delete user
     */
    public function deleteUser()
    {
        $this->delete(); // Soft delete
    }

    /**
     * Method untuk restore user
     */
    public function restoreUser()
    {
        $this->restore(); // Restore the soft deleted user
    }

    /**
     * Scope untuk mendapatkan data yang soft deleted
     */
    public function scopeTrashedUsers($query)
    {
        return $query->onlyTrashed();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'user_id', 'course_id');
    }

    public function coursesTaught()
    {
        return $this->hasMany(Course::class, 'mentor_id');
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class)
        ->withPivot('tanggal_mulai', 'tanggal_daftar', 'tanggal_selesai', 'status')
        ->withTimestamps();
    }
}
