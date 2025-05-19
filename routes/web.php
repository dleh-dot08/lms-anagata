<?php

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ScoreController;
//use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\DivisiMiddleware;
use App\Http\Middleware\MentorMiddleware;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\MentorController;
use App\Http\Middleware\PesertaMiddleware;
use App\Http\Controllers\BiodataController;
use App\Http\Controllers\JenjangController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;

use App\Http\Middleware\KaryawanMiddleware;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;

use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\HelpdeskTicketController;
use App\Http\Middleware\MentorOrPesertaMiddleware;
use App\Http\Controllers\HelpdeskMessageController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\EmailVerificationPromptController;

Route::get('/', function () {
    return view('welcome');
});

// Route buat verifikasi
Route::get('/verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/peserta/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');


// Route buat kirim ulang email verifikasi
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Jenjang (Admin Only)
    Route::middleware(AdminMiddleware::class)->prefix('jenjang')->name('jenjang.')->group(function () {
        Route::get('/', [JenjangController::class, 'index'])->name('index');
        Route::get('/create', [JenjangController::class, 'create'])->name('create');
        Route::post('/', [JenjangController::class, 'store'])->name('store');
        Route::get('/{id}', [JenjangController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [JenjangController::class, 'edit'])->name('edit');
        Route::put('/{id}', [JenjangController::class, 'update'])->name('update');
        Route::delete('/{id}', [JenjangController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [JenjangController::class, 'restore'])->name('restore');
    });

    // Kategori (Admin Only)
    Route::middleware(AdminMiddleware::class)->prefix('kategori')->name('kategori.')->group(function () {
        // Rute untuk CRUD Kategori
        Route::resource('/', KategoriController::class);
        Route::get('/', [KategoriController::class, 'index'])->name('index');
        Route::get('/create', [KategoriController::class, 'create'])->name('create');
        Route::post('/', [KategoriController::class, 'store'])->name('store');
        Route::get('/{id}', [KategoriController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [KategoriController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KategoriController::class, 'update'])->name('update');
        Route::delete('/{id}', [KategoriController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [KategoriController::class, 'restore'])->name('restore');
    });

    // Course Management (Admin Only
    Route::middleware(AdminMiddleware::class)->get('/courses/search-peserta', [CourseController::class, 'searchPeserta'])->name('courses.searchPeserta');
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/search-mentor', [CourseController::class, 'searchMentor'])->name('searchMentor');
    });
    Route::middleware(AdminMiddleware::class)->prefix('courses')->name('courses.')->group(function () {
        Route::get('/search-peserta', [CourseController::class, 'searchPeserta'])->name('searchPeserta');
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/create', [CourseController::class, 'create'])->name('create');
        Route::post('/', [CourseController::class, 'store'])->name('store');
        Route::get('/{id}', [CourseController::class, 'show'])->name('show');
        Route::get('/{course}/edit', [CourseController::class, 'edit'])->name('edit');
        Route::put('/{course}', [CourseController::class, 'update'])->name('update');
        Route::delete('/{course}', [CourseController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [CourseController::class, 'restore'])->name('restore');

        // untuk silabus admin
        Route::post('/{course}/upload-silabus', [CourseController::class, 'uploadSilabus'])->name('uploadSilabus');
        Route::get('/{course}/silabus', [CourseController::class, 'showSilabusAdmin'])->name('showSilabus');
        Route::post('/{course}/upload-rpp', [CourseController::class, 'uploadRpp'])->name('uploadRpp');

        // Form untuk tambah peserta ke kursus tertentu
        Route::get('{course}/formparticipant', [ParticipantController::class, 'form'])->name('formparticipant');
        // Simpan peserta (single / bulk)
        Route::post('{course}/participants', [ParticipantController::class, 'store'])->name('participants.store');

        // AJAX: Search peserta untuk Select2 autocomplete
        Route::get('search-peserta', [ParticipantController::class, 'search'])->name('participants.search');

        // Hapus peserta dari kursus
        Route::delete('{course}/participants/{user}', [ParticipantController::class, 'destroy'])->name('participants.destroy');

        // Lesson
        //Route::get('/{course}/lessons/create', [LessonController::class, 'create'])->name('createLessonForm');
        //Route::post('/{course}/lessons', [LessonController::class, 'store'])->name('storeLesson');
        //Route::get('/{course}/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('editLesson');
        //Route::put('/{course}/lessons/{lesson}', [LessonController::class, 'update'])->name('updateLesson');
        //Route::delete('/{course}/lessons/{lesson}', [LessonController::class, 'destroy'])->name('deleteLesson');
        //Route::get('/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('showLesson');

    });

    Route::middleware(AdminMiddleware::class)->prefix('courses')->name('courses.admin.')->group(function () {
        Route::get('/{course}/lessons/create', [LessonController::class, 'create'])->name('createLessonForm');
        Route::post('/{course}/lessons', [LessonController::class, 'store'])->name('storeLesson');
        Route::get('/{course}/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('editLesson');
        Route::put('/{course}/lessons/{lesson}', [LessonController::class, 'update'])->name('updateLesson');
        Route::delete('/{course}/lessons/{lesson}', [LessonController::class, 'destroy'])->name('deleteLesson');
        Route::get('/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('showLesson');
    });

    //Biodata 
    Route::get('/biodata', [BiodataController::class, 'index'])->name('biodata.index');
    Route::get('/biodata/{id}/edit', [BiodataController::class, 'edit'])->name('biodata.edit');
    Route::put('/biodata/{id}', [BiodataController::class, 'update'])->name('biodata.update');  
});
    
// Admin routes
Route::middleware(['auth', 'verified'])->prefix('admin/attendances')->group(function () {
    Route::get('/', [AttendanceController::class, 'adminIndex'])->name('attendances.admin.index');
    Route::get('/{attendance}', [AttendanceController::class, 'show'])->name('attendances.admin.show');
});

// Peserta & Mentor routes
Route::middleware(['auth', 'role:2,3'])->group(function () {
    Route::get('/absensi/{course}/create', [AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('/absensi', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('/rekap-absensi', [AttendanceController::class, 'rekap'])->name('attendances.rekap');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin/dashboard', [AdminController::class, 'index'])
->name('admin.dashboard')
->middleware(AdminMiddleware::class);

// Admin User Management
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
});

Route::middleware(['auth', AdminMiddleware::class])
    ->prefix('users')
    ->name('users.')
    ->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('restore');
    });

    Route::get('/faq', [FaqController::class, 'redirectBasedOnRole'])->name('faq');

Route::middleware(AdminMiddleware::class)
    ->prefix('admin/faq')
    ->name('admin.faq.')
    ->group(function () {

        // FAQ CRUD 
        Route::get('/', [FaqController::class, 'index'])->name('index');
        Route::get('/create', [FaqController::class, 'create'])->name('create');
        Route::post('/', [FaqController::class, 'store'])->name('store');
        Route::get('/{faq}/edit', [FaqController::class, 'edit'])->name('edit');
        Route::put('/{faq}', [FaqController::class, 'update'])->name('update');
        Route::get('/{faq}', [FaqController::class, 'show'])->name('show');
        Route::delete('/{faq}', [FaqController::class, 'destroy'])->name('destroy');
    });

Route::middleware(['web', AdminMiddleware::class])
    ->prefix('admin/helpdesk')
    ->name('admin.helpdesk.')
    ->group(function () {
        Route::get('/', [HelpdeskTicketController::class, 'index'])->name('tickets.index');
        Route::get('/ticket/{id}', [HelpdeskTicketController::class, 'show'])->name('tickets.show');
        Route::post('/ticket/{id}/message', [HelpdeskMessageController::class, 'store'])->name('tickets.message.store');
        Route::put('/ticket/{id}/close', [HelpdeskTicketController::class, 'close'])->name('tickets.close');
    });

Route::middleware(['web', PesertaMiddleware::class])
    ->prefix('peserta/helpdesk')
    ->name('peserta.helpdesk.')
    ->group(function () {
        Route::get('/', [HelpdeskTicketController::class, 'index'])->name('index');               // List tiket peserta
        Route::get('/create', [HelpdeskTicketController::class, 'create'])->name('create');
        Route::post('/', [HelpdeskTicketController::class, 'store'])->name('store');
        Route::get('/ticket/{id}', [HelpdeskTicketController::class, 'show'])->name('tickets.show');      // Detail tiket
        Route::post('/ticket/{id}/message', [HelpdeskMessageController::class, 'store'])->name('message.store'); // Balas
        Route::post('/ticket-with-message', [HelpdeskTicketController::class, 'storeWithMessage'])->name('store.with.message');
        Route::get('/faq/{id}', [FaqController::class, 'show'])->name('faq.show');
    });


Route::prefix('guest/helpdesk')
    ->name('guest.helpdesk.')
    ->group(function () {
        Route::get('/create', [HelpdeskTicketController::class, 'create'])->name('create');
        Route::post('/store', [HelpdeskTicketController::class, 'store'])->name('store');
        Route::get('/ticket/{id}', [HelpdeskTicketController::class, 'show'])->name('show');
        Route::post('/ticket/{id}/message', [HelpdeskMessageController::class, 'store'])->name('message.store');
        Route::get('/faq/{id}', [FaqController::class, 'show'])->name('faq.show');
    });


Route::get('/peserta/dashboard', [PesertaController::class, 'index'])
    ->name('peserta.dashboard')
    ->middleware(['auth', 'verified', PesertaMiddleware::class]);

/*Route::middleware(['web', PesertaMiddleware::class])
    ->prefix('layouts/peserta')
    ->name('layouts.peserta.')
    ->group(function () {
        Route::get('/layouts/peserta/biodata', [BiodataController::class, 'index'])->name('biodata.index');
        Route::get('/layouts/peserta/biodata/{id}/edit', [BiodataController::class, 'edit'])->name('biodata.edit');
        Route::put('/layouts/peserta/biodata/{id}', [BiodataController::class, 'update'])->name('biodata.update');
    });

Route::middleware(['web', KaryawanMiddleware::class])
    ->prefix('layouts/karyawan')
    ->name('layouts.karyawan.')
    ->group(function () {
        Route::get('/biodata', [BiodataController::class, 'index'])->name('biodata.index');
        Route::get('/biodata/{id}/edit', [BiodataController::class, 'edit'])->name('biodata.edit');
        Route::put('/biodata/{id}', [BiodataController::class, 'update'])->name('biodata.update');
    });*/

Route::get('/Karyawan/dashboard', [KaryawanController::class, 'index'])
    ->name('karyawan.dashboard')
    ->middleware(KaryawanMiddleware::class);

Route::middleware(['auth', DivisiMiddleware::class . ':APD'])->group(function () {
    Route::get('/karyawan/kursus', [CourseController::class , 'index'])->name('courses.apd.index');
    Route::get('/karyawan/kursus/create', [CourseController::class, 'create'])->name('courses.apd.create');
    Route::post('/', [CourseController::class, 'store'])->name('courses.apd.store');
    Route::get('/karyawan/kursus/{id}', [CourseController::class, 'show'])->name('courses.apd.show');
    Route::get('karyawan/{course}/edit', [CourseController::class, 'edit'])->name('courses.apd.edit');
    Route::put('karyawan/{course}', [CourseController::class, 'update'])->name('courses.apd.update');
    Route::delete('karyawan/{course}', [CourseController::class, 'destroy'])->name('courses.apd.destroy');
    Route::post('karyawan/{id}/restore', [CourseController::class, 'restore'])->name('courses.apd.restore');

    Route::get('karyawan/{course}/lessons/create', [LessonController::class, 'create'])->name('courses.apd.createLessonForm');
    Route::post('karyawan/{course}/lessons', [LessonController::class, 'store'])->name('courses.apd.storeLesson');
    Route::get('karyawan/{course}/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('courses.apd.editLesson');
    Route::put('karyawan/{course}/lessons/{lesson}', [LessonController::class, 'update'])->name('courses.apd.updateLesson');
    Route::delete('karyawan/{course}/lessons/{lesson}', [LessonController::class, 'destroy'])->name('courses.apd.deleteLesson');
    Route::get('karyawan/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('courses.apd.showLesson');

    Route::get('karyawan/{course}/formparticipant', [ParticipantController::class, 'form'])->name('courses.apd.formparticipant');
    Route::post('karyawan/{course}/participants', [ParticipantController::class, 'store'])->name('courses.apd.participants.store');
    Route::get('karyawan/search-peserta', [ParticipantController::class, 'search'])->name('courses.apd.participants.search');
    Route::delete('karyawan/{course}/participants/{user}', [ParticipantController::class, 'destroy'])->name('courses.apd.participants.destroy');

    Route::get('karyawan/activities', [ActivityController::class, 'index'])->name('activities.apd.index');
    Route::get('karyawan/activities/create', [ActivityController::class, 'create'])->name('activities.apd.create');
    Route::post('karyawan/activities', [ActivityController::class, 'store'])->name('activities.apd.store');
    Route::get('karyawan/activities/{activity}', [ActivityController::class, 'show'])->name('activities.apd.show');
    Route::get('karyawan/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.apd.edit');
    Route::put('karyaan/activities/{activity}', [ActivityController::class, 'update'])->name('activities.apd.update');
    Route::delete('karyawan/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.apd.destroy');

    Route::get('karyawan/activities/{activity}/participants', [ActivityController::class, 'manageParticipants'])->name('activities.apd.participants');
    Route::get('karyawan/activities/{activity}/participants/add', [ActivityController::class, 'addParticipantForm'])->name('activities.apd.participants.add');
    Route::post('karyawan/activities/{activity}/participants', [ActivityController::class, 'storeParticipants'])->name('activities.apd.participants.store');
    Route::delete('karyawan/activities/{activity}/participants/{user}', [ActivityController::class, 'removeParticipant'])->name('activities.apd.participants.remove');
});


Route::middleware(['auth',DivisiMiddleware::class . ':MRC'])->prefix('karyawan/faq')->name('faq.mrc.')->group(function () {
    Route::get('/', [FaqController::class, 'index'])->name('index');
    Route::get('/create', [FaqController::class, 'create'])->name('create');
    Route::post('/', [FaqController::class, 'store'])->name('store');
    Route::get('/{faq}/edit', [FaqController::class, 'edit'])->name('edit');
    Route::put('/{faq}', [FaqController::class, 'update'])->name('update');
    Route::get('/{faq}', [FaqController::class, 'show'])->name('show');
    Route::delete('/{faq}', [FaqController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth','divisi:Help Desk'])->group(function(){
    Route::resource('helpdesk', HelpdeskTicketController::class);
});


Route::get('/peserta/kursus', [CourseController::class, 'indexpeserta'])
    ->name('courses.indexpeserta')
    ->middleware(PesertaMiddleware::class);

// Untuk project dari course
Route::get('/courses/{course}/projects/create', [ProjectController::class, 'createCourse'])->name('projects.peserta.createCourse');
Route::post('/courses/{course}/projects/store', [ProjectController::class, 'storeCourse'])->name('projects.peserta.storeCourse');
Route::get('/courses/{course}/projects/{project}/show', [ProjectController::class, 'showCourse'])->name('projects.peserta.showCourse');

// Halaman detail kursus untuk peserta
Route::get('/peserta/kursus/{course}', [CourseController::class, 'showPeserta'])
    ->name('courses.showPeserta')
    ->middleware(PesertaMiddleware::class);

// Halaman detail materi kursus
Route::get('/peserta/kursus/{course}/lesson/{lesson}', [CourseController::class, 'showLesson'])
    ->name('courses.showLesson')
    ->middleware(PesertaMiddleware::class);

    Route::middleware(['auth', PesertaMiddleware::class])->group(function () {
        //route kursus untuk peserta
        Route::get('/peserta/gabung-kursus', [CourseController::class, 'showJoinFormPeserta'])
        ->name('courses.joinPeserta');
        Route::post('/peserta/gabung-kursus', [CourseController::class, 'joinWithCodePeserta'])
        ->name('courses.joinPeserta.submit');


    });
    Route::middleware(['auth', MentorOrPesertaMiddleware::class])->group(function () {
        // route absensi untuk mentor dan peserta
        Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendances.index');
        Route::get('/absensi/kursus', [AttendanceController::class, 'courses'])->name('attendances.courses');
        Route::get('/absensi/kegiatan', [AttendanceController::class, 'activities'])->name('attendances.activities');
        Route::get('/absensi/create/{course}', [AttendanceController::class, 'create'])->name('attendances.create');
        Route::get('/absensi/kegiatan/{activity}/buat', [AttendanceController::class, 'createActivity'])->name('attendances.activity.create');
        Route::post('/absensi/kegiatan/{activity}/simpan', [AttendanceController::class, 'storeActivity'])->name('attendances.activity.store');
        Route::post('/absensi/store', [AttendanceController::class, 'store'])->name('attendances.store');
        Route::get('/absensi/rekap', [AttendanceController::class, 'rekap'])->name('attendances.rekap');
        Route::get('/absensi/{course}/input', [AttendanceController::class, 'input'])->name('attendances.input');
        Route::post('/absensi/{course}/store', [AttendanceController::class, 'storeInput'])->name('attendances.storeInput');
    });
    
    // Route::middleware(['auth', MentorMiddleware::class])->group(function () {
    //     Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendances.index');
    //     Route::get('/absensi/kursus', [AttendanceController::class, 'courses'])->name('attendances.courses');
    //     Route::get('/absensi/kegiatan', [AttendanceController::class, 'activities'])->name('attendances.activities');
    //     Route::get('/absensi/create/{course}', [AttendanceController::class, 'create'])->name('attendances.create');
    //     Route::get('/absensi/kegiatan/{activity}/buat', [AttendanceController::class, 'createActivity'])->name('attendances.activity.create');
    //     Route::post('/absensi/kegiatan/{activity}/simpan', [AttendanceController::class, 'storeActivity'])->name('attendances.activity.store');
    //     Route::post('/absensi/store', [AttendanceController::class, 'store'])->name('attendances.store');
    //     Route::get('/absensi/rekap', [AttendanceController::class, 'rekap'])->name('attendances.rekap');
    // });
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/admin/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/admin/activities/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/admin/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::get('/admin/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
    Route::get('/admin/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::put('/admin/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/admin/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

    Route::get('/admin/activities/{activity}/participants', [ActivityController::class, 'manageParticipants'])->name('activities.participants');
    Route::get('/admin/activities/{activity}/participants/add', [ActivityController::class, 'addParticipantForm'])->name('activities.participants.add');
    Route::post('/admin/activities/{activity}/participants', [ActivityController::class, 'storeParticipants'])->name('activities.participants.store');
    Route::delete('/admin/activities/{activity}/participants/{user}', [ActivityController::class, 'removeParticipant'])->name('activities.participants.remove');
});

Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/certificates', [CertificateController::class, 'indexAdmin'])->name('certificates.indexAdmin');
    Route::get('/certificates/create', [CertificateController::class, 'create'])->name('certificates.create');
    Route::get('/certificates/create/{courseId}', [CertificateController::class, 'createCourses'])->name('certificates.createCourses');
    Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');
    Route::get('/certificates/{id}/edit', [CertificateController::class, 'edit'])->name('certificates.edit');
    Route::put('/certificates/{id}', [CertificateController::class, 'update'])->name('certificates.update');
    Route::get('/certificates/{id}', [CertificateController::class, 'showAdmin'])->name('certificates.showAdmin');
    Route::delete('/certificates/{id}', [ActivityController::class, 'destroy'])->name('certificates.destroy');
});

// Khusus untuk Peserta
Route::middleware(['auth', PesertaMiddleware::class])->group(function () {
    Route::get('my-certificates', [CertificateController::class, 'indexPeserta'])->name('certificates.indexPeserta');
    Route::get('my-certificates/{id}', [CertificateController::class, 'showPeserta'])->name('certificates.showPeserta');
});

// SHARED (ADMIN & PESERTA)
/*Route::middleware(['auth'])->group(function () {
    Route::get('/certificates/{id}', [CertificateController::class, 'show'])->name('certificates.show');
});*/

Route::get('/mentor/dashboard', [MentorController::class, 'index'])
    ->name('mentor.dashboard')
    ->middleware(MentorMiddleware::class);

    Route::middleware(['auth', MentorMiddleware::class])
    ->prefix('mentor')
    ->name('mentor.')
    ->group(function () {
        Route::get('/kursus', [CourseController::class, 'indexMentor'])->name('kursus.index');
        Route::get('/kursus/{course}', [CourseController::class, 'showMentor'])->name('kursus.show');
        Route::get('/kursus/{course}/lesson/{lesson}', [CourseController::class, 'showLessonMentor'])->name('kursus.lesson.show');
    });


/// Admin Routes
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('admin/projects', [ProjectController::class, 'index'])->name('admin.projects.index'); // Menampilkan daftar project untuk admin
    Route::get('admin/projects/{project}', [ProjectController::class, 'showAdmin'])->name('projects.admin.show'); // Menampilkan detail project untuk admin
});

// Peserta Routes
Route::middleware(['auth', PesertaMiddleware::class])->group(function () {
    Route::get('projects/peserta', [ProjectController::class, 'index'])->name('projects.peserta.index'); // Menampilkan daftar project untuk peserta
    Route::get('/projects/{project}', [ProjectController::class, 'showPeserta'])->name('projects.peserta.show'); // Menampilkan detail project untuk peserta
    Route::get('projects/peserta/create', [ProjectController::class, 'create'])->name('projects.peserta.create'); // Form untuk membuat project baru
    Route::post('projects/peserta', [ProjectController::class, 'store'])->name('projects.peserta.store'); // Menyimpan project baru
    Route::get('projects/peserta/{project}/edit', [ProjectController::class, 'edit'])->name('projects.peserta.edit'); // Form untuk mengedit project
    Route::put('projects/peserta/{project}', [ProjectController::class, 'update'])->name('projects.peserta.update'); // Update project
    Route::delete('projects/peserta/{project}', [ProjectController::class, 'destroy'])->name('projects.peserta.destroy'); // Hapus project

    Route::post('/assignments/{assignment}/submit', [SubmissionController::class, 'store'])->name('assignments.submit');
    Route::post('/assignments/{assignment}/update', [SubmissionController::class, 'update'])->name('assignments.update');
    Route::get('/assignments/{assignment}/submit', [SubmissionController::class, 'submissionForm'])->name('assignments.submit.form');
    Route::get('/assignments', [SubmissionController::class, 'index'])->name('assignments.index');
   
});

// Karyawan Routes
Route::middleware(['auth', DivisiMiddleware::class.':APD,MRC'])->group(function () {
    Route::get('karyawan/projects', [ProjectController::class, 'index'])->name('karyawan.projects.index'); // Menampilkan daftar project untuk karyawan
    Route::get('karyawan/projects/{project}', [ProjectController::class, 'show'])->name('karyawan.projects.show'); // Menampilkan detail project untuk karyawan
});

// Mentor Routes
Route::middleware(['auth', MentorMiddleware::class])->group(function () {
    Route::get('mentor/projects', [ProjectController::class, 'index'])->name('mentor.projects.index'); // Menampilkan daftar project untuk mentor
    Route::get('mentor/projects/{project}', [ProjectController::class, 'showMentor'])->name('mentor.projects.show'); // Menampilkan detail project untuk mentor
});

Route::prefix('admin/courses/{course}')->middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('meetings/create', [MeetingController::class, 'create'])->name('meetings.create');
    Route::post('meetings', [MeetingController::class, 'store'])->name('meetings.store');
    Route::get('meetings/{meeting}/edit', [MeetingController::class, 'edit'])->name('meetings.edit');
    Route::put('meetings/{meeting}', [MeetingController::class, 'update'])->name('meetings.update');
    Route::delete('meetings/{meeting}', [MeetingController::class, 'destroy'])->name('meetings.destroy');
});
  
Route::middleware(['auth', MentorOrPesertaMiddleware::class])->group(function () {
    Route::get('courses/{course}/meetings/{meeting}', [MeetingController::class, 'show'])->name('kursus.pertemuan.show');

    Route::get('/mentor/kursus/{course}/overview', [CourseController::class, 'overview'])->name('kursus.mentor.overview');

    Route::get('/mentor/kursus/{course}/assignment', [CourseController::class, 'showAssignment'])->name('kursus.mentor.assignment');
    Route::post('{course}/assignment/store', [AssignmentController::class, 'store'])->name('kursus.mentor.assignment.store');
    Route::get('/mentor/assignment/{assignment}/submissions', [AssignmentController::class, 'submissions'])->name('mentor.assignment.submissions');

   
    Route::get('mentor/kursus/{course}/scores', [CourseController::class, 'listMeetingsForScoring'])->name('mentor.scores.index');
    Route::get('mentor/{course}/{meeting}/input', [ScoreController::class, 'input'])->name('mentor.scores.input');
    Route::post('mentor/scores/{course}/{meeting}/store', [ScoreController::class, 'store'])->name('mentor.scores.store');

    Route::get('/mentor/kursus/{id}/silabus', [CourseController::class, 'showSilabus'])->name('kursus.mentor.silabus');
    Route::get('/mentor/kursus/{id}/silabus-preview', [CourseController::class, 'previewSilabus'])->name('kursus.mentor.silabus.preview');

    Route::get('/mentor/kursus/{course}/project', [CourseController::class, 'project'])->name('kursus.mentor.project');

    Route::get('/mentor/kursus/{course}/peserta', [CourseController::class, 'showAllPeserta'])->name('kursus.mentor.peserta');
});

require __DIR__.'/auth.php';
