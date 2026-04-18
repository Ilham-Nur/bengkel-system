<?php

use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KwitansiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/workorder', [WorkOrderController::class, 'index'])->name('workorder.index');
    Route::get('/workorder/form', [WorkOrderController::class, 'create'])->name('workorder.create');
    Route::post('/workorder', [WorkOrderController::class, 'store'])->name('workorder.store');
    Route::get('/workorder/{workorder}/edit', [WorkOrderController::class, 'edit'])->name('workorder.edit');
    Route::get('/workorder/{workorder}/pdf', [WorkOrderController::class, 'exportPdf'])->name('workorder.pdf');
    Route::put('/workorder/{workorder}', [WorkOrderController::class, 'update'])->name('workorder.update');
    Route::delete('/workorder/{workorder}', [WorkOrderController::class, 'destroy'])->name('workorder.destroy');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/{workorder}/form', [LaporanController::class, 'form'])->name('laporan.form');
    Route::get('/laporan/{workorder}/detail', [LaporanController::class, 'show'])->name('laporan.show');
    Route::post('/laporan/{workorder}', [LaporanController::class, 'save'])->name('laporan.save');
    Route::get('/kwitansi', [KwitansiController::class, 'index'])->name('kwitansi.index');
    Route::get('/kwitansi/create', [KwitansiController::class, 'create'])->name('kwitansi.create');
    Route::post('/kwitansi', [KwitansiController::class, 'store'])->name('kwitansi.store');

    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');

    Route::redirect('/pelanggan', '/workorder')->name('pelanggan.index');
});
