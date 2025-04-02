<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenjangController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Admin\UserController;

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

require __DIR__.'/auth.php';
