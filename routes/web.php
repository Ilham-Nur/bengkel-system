<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login.index'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login.index');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::view('/workorder', 'workorder.index')->name('workorder.index');
    Route::get('/workorder/form', [WorkOrderController::class, 'create'])->name('workorder.create');
    Route::post('/workorder', [WorkOrderController::class, 'store'])->name('workorder.store');

    Route::view('/laporan', 'laporan.index')->name('laporan.index');
    Route::view('/kwitansi', 'kwitansi.index')->name('kwitansi.index');

    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');

    Route::redirect('/pelanggan', '/workorder')->name('pelanggan.index');
});
