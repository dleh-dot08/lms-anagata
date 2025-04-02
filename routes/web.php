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

Route::prefix('user')->name('user.')->middleware('auth', 'admin')->group(function () {
    // Rute untuk CRUD User
    Route::get('/', [UserController::class, 'index'])->name('index'); // Menampilkan daftar user
    Route::get('/create', [UserController::class, 'create'])->name('create'); // Halaman untuk membuat user baru
    Route::post('/', [UserController::class, 'store'])->name('store'); // Menyimpan user baru
    Route::get('/{user}', [UserController::class, 'show'])->name('show'); // Menampilkan detail user
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit'); // Halaman edit user
    Route::put('/{user}', [UserController::class, 'update'])->name('update'); // Update user
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy'); // Menghapus user (soft delete)
    Route::post('/{user}/restore', [UserController::class, 'restore'])->name('restore'); // Restore user yang dihapus
});

require __DIR__.'/auth.php';
