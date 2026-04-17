<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login.index'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login.index');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::view('/workorder', 'workorder.index')->name('workorder.index');
    Route::get('/workorder/form', function () {
        if (! auth()->user()?->isAdmin()) {
            return redirect()->route('workorder.index');
        }

        return view('workorder.create');
    })->name('workorder.create');

    Route::view('/laporan', 'laporan.index')->name('laporan.index');
    Route::view('/kwitansi', 'kwitansi.index')->name('kwitansi.index');

    Route::get('/user', [LoginController::class, 'indexUser'])->name('user.index');
    Route::redirect('/pelanggan', '/workorder')->name('pelanggan.index');
});
