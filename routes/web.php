<?php

use App\Models\Attendance;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\KaryawanMiddleware;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Middleware\MentorMiddleware;
use App\Http\Controllers\CourseController;
//use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\LessonController;
use App\Http\Middleware\PesertaMiddleware;
use App\Http\Controllers\BiodataController;
use App\Http\Controllers\JenjangController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ParticipantController;

use App\Http\Controllers\FaqController;
use App\Http\Controllers\HelpdeskTicketController;
use App\Http\Controllers\HelpdeskMessageController;
use App\Http\Controllers\MentorController;
use App\Http\Middleware\DivisiMiddleware;
use App\Http\Middleware\MentorOrPesertaMiddleware;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
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
Route::middleware(['auth'])->prefix('admin/attendances')->group(function () {
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
    ->middleware(PesertaMiddleware::class);

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
    Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');
    Route::get('/certificates/{id}/edit', [CertificateController::class, 'edit'])->name('certificates.edit');
    Route::put('/certificates/{id}', [CertificateController::class, 'update'])->name('certificates.update');
    Route::get('/certificates/{id}', [CertificateController::class, 'show'])->name('certificates.showAdmin');
});

// Khusus untuk Peserta
Route::middleware(['auth', PesertaMiddleware::class])->group(function () {
    Route::get('my-certificates', [CertificateController::class, 'indexPeserta'])->name('certificates.indexPeserta');
    Route::get('my-certificates/{id}', [CertificateController::class, 'showPeserta'])->name('certificates.showPeserta');
});

// SHARED (ADMIN & PESERTA)
Route::middleware(['auth'])->group(function () {
    Route::get('/certificates/{id}', [CertificateController::class, 'show'])->name('certificates.show');
});

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

    
require __DIR__.'/auth.php';
