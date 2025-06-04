<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Kelas;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_unik',
        'mentor_id',
        'mentor_id_2', // Tambahkan ini
        'mentor_id_3', // Tambahkan ini
        'sekolah_id',
        'nama_kelas',
        'deskripsi',
        'kategori_id',
        'jenjang_id',
        'kelas_id',
        'program_id',
        'level',
        'status',
        'waktu_mulai',
        'waktu_akhir',
        'harga',
        'jumlah_peserta',
        'kode_kursus',
        'created_by',
        'updated_by',
        'deleted_by',
        'tanggal_mulai',
        'tanggal_selesai',
        'silabus',
        'rpp',
    ];

    protected $casts = [
        'waktu_mulai' => 'date',
        'waktu_akhir' => 'date',
    ];

    // Relasi ke Mentor (User dengan role_id = 2)
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id')->where('role_id', 2);
    }

            // Relasi untuk mentor kedua
            public function mentor2()
            {
                return $this->belongsTo(User::class, 'mentor_id_2')->where('role_id', 2);
            }
        
            // Relasi untuk mentor ketiga
            public function mentor3()
            {
                return $this->belongsTo(User::class, 'mentor_id_3')->where('role_id', 2);
            }

    // Relasi ke Kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    // Relasi ke Jenjang
    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class);
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'course_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public static function generateKodeKelas($length = 5)
    {
        do {
            $kode = 'CDM-' . strtoupper(Str::random($length));
        } while (self::where('kode_unik', $kode)->exists());

        return $kode;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'enrollments')
                    ->withPivot('mentor_id', 'tanggal_daftar', 'tanggal_mulai', 'tanggal_selesai')
                    ->withTimestamps();
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'user_id')
                    ->withPivot('status', 'tanggal_daftar', 'tanggal_mulai', 'tanggal_selesai')
                    ->using(Enrollment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'user_id');
    }

    public function assignments()
    {
        return $this->hasManyThrough(
            Assignment::class, // The final model you want to access (Assignments)
            Meeting::class,    // The intermediate model (Meetings)
            'course_id',       // Foreign key on the intermediate (Meetings) table relating to the Course
            'meeting_id',      // Foreign key on the final (Assignments) table relating to the Meeting
            'id',              // Local key on the Course model (default is 'id')
            'id'               // Local key on the Meeting model (default is 'id')
        );
    }

    



}
