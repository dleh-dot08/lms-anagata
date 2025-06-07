<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;
use App\Models\Activity;

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
        'surat_tugas', 'role_id', 'created_by', 'updated_by',
        'jenis_kelamin', 'pendidikan_terakhir', 'pekerjaan', 'media_sosial',
        'sekolah_id', 'kelas_id' // Add sekolah_id and kelas_id to fillable array
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
     * Relasi ke Kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
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
        return $this->belongsToMany(Course::class, 'enrollments', 'user_id', 'course_id')
                    ->withPivot('status', 'tanggal_daftar', 'tanggal_mulai', 'tanggal_selesai')
                    ->using(Enrollment::class);
    }

    public function coursesTaught()
    {
        return $this->hasMany(Course::class, 'mentor_id');
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_user')
                    ->withPivot(['status', 'tanggal_mulai', 'tanggal_selesai'])
                    ->withTimestamps();
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
                    ->withPivot('mentor_id', 'tanggal_daftar', 'tanggal_mulai', 'tanggal_selesai')
                    ->withTimestamps();
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail());
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Relationship with Sekolah
     */
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function mentorCourses()
    {
        return Course::where(function($query) {
            $query->where('mentor_id', $this->id)
                  ->orWhere('mentor_id_2', $this->id)
                  ->orWhere('mentor_id_3', $this->id);
        });
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function taughtCourses()
    {
        return Course::where(function($query) {
            $query->where('mentor_id', $this->id)
                  ->orWhere('mentor_id_2', $this->id)
                  ->orWhere('mentor_id_3', $this->id);
        });
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'user_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'peserta_id');
    }
}
