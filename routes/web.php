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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::prefix('jenjang')->name('jenjang.')->group(function () {
        Route::get('/', [JenjangController::class, 'index'])->name('index');
        Route::get('/create', [JenjangController::class, 'create'])->name('create');
        Route::post('/', [JenjangController::class, 'store'])->name('store');
        Route::get('/{id}', [JenjangController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [JenjangController::class, 'edit'])->name('edit');
        Route::put('/{id}', [JenjangController::class, 'update'])->name('update');
        Route::delete('/{id}', [JenjangController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [JenjangController::class, 'restore'])->name('restore');
    });

    Route::prefix('kategori')->name('kategori.')->group(function () {
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

    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/create', [CourseController::class, 'create'])->name('create');
        Route::post('/', [CourseController::class, 'store'])->name('store');
        Route::get('/{id}', [CourseController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CourseController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CourseController::class, 'update'])->name('update');
        Route::delete('/{id}', [CourseController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [CourseController::class, 'restore'])->name('restore');
        
        // Tambah & hapus peserta
        Route::post('/{id}/add-participant', [CourseController::class, 'addParticipant'])->name('addParticipant');
        Route::get('/search-peserta', [CourseController::class, 'searchPeserta'])->name('courses.searchPeserta');
        Route::delete('/{id}/remove-participant/{participant_id}', [CourseController::class, 'removeParticipant'])->name('removeParticipant');
    });
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin/dashboard', [AdminController::class, 'index'])
->name('admin.dashboard')
->middleware(AdminMiddleware::class);

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
});

Route::prefix('users')->name('users.')->group(function () {
    // Rute untuk melihat daftar pengguna (index)
    Route::get('/', [UserController::class, 'index'])->name('index');
    // Rute untuk menampilkan form tambah pengguna (create)
    Route::get('/create', [UserController::class, 'create'])->name('create');
    // Rute untuk menyimpan pengguna baru (store)
    Route::post('/', [UserController::class, 'store'])->name('store');
    // Rute untuk menampilkan detail pengguna (show)
    Route::get('/{id}', [UserController::class, 'show'])->name('show');
    // Rute untuk menampilkan form edit pengguna (edit)
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
    // Rute untuk memperbarui pengguna (update)
    Route::put('/{id}', [UserController::class, 'update'])->name('update');
    // Rute untuk menghapus pengguna (destroy)
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('restore');
});

require __DIR__.'/auth.php';
