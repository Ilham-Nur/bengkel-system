<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
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

    Route::middleware(function ($request, $next) {
        if (! $request->user()?->isAdmin()) {
            return redirect()->route('workorder.index');
        }

        return $next($request);
    })->group(function () {
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::post('/user', [UserController::class, 'store'])->name('user.store');
        Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    });

    Route::redirect('/pelanggan', '/workorder')->name('pelanggan.index');
});
