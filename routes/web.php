<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenjangController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminMiddleware;
//use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\AttendanceController;
use App\Http\Middleware\PesertaMiddleware;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\BiodataController;

use App\Http\Controllers\FaqController;
use App\Http\Controllers\HelpdeskTicketController;
use App\Http\Controllers\HelpdeskMessageController;

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
    Route::middleware(AdminMiddleware::class)->prefix('courses')->name('courses.')->group(function () {
        Route::get('/search-peserta', [CourseController::class, 'searchPeserta'])->name('searchPeserta');
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/create', [CourseController::class, 'create'])->name('create');
        Route::post('/', [CourseController::class, 'store'])->name('store');
        Route::get('/{id}', [CourseController::class, 'show'])->name('show');
        Route::get('/{course}/edit', [CourseController::class, 'edit'])->name('edit');
        Route::put('/{course}', [CourseController::class, 'update'])->name('update');
        Route::delete('/{id}', [CourseController::class, 'destroy'])->name('destroy');
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
        Route::get('/create', [FaqController::class, 'create'])->name('create');
        Route::get('/ticket/{id}', [HelpdeskTicketController::class, 'show'])->name('show');      // Detail tiket
        Route::post('/ticket/{id}/message', [HelpdeskMessageController::class, 'store'])->name('message.store'); // Balas
        Route::post('/ticket-with-message', [HelpdeskTicketController::class, 'storeWithMessage'])->name('store.with.message');
    });


Route::prefix('guest/helpdesk')
    ->name('guest.helpdesk.')
    ->group(function () {
        Route::get('/create', [HelpdeskTicketController::class, 'create'])->name('create');
        Route::post('/store', [HelpdeskTicketController::class, 'store'])->name('store');
        Route::get('/ticket/{id}', [HelpdeskTicketController::class, 'show'])->name('show');
        Route::post('/ticket/{id}/message', [HelpdeskMessageController::class, 'store'])->name('message.store');
    });


Route::get('/peserta/dashboard', [PesertaController::class, 'index'])
    ->name('peserta.dashboard')
    ->middleware(PesertaMiddleware::class);

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

Route::get('/faq', [FaqController::class, 'index'])->name('faq.public');


require __DIR__.'/auth.php';
