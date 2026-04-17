<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login.index'));

Route::view('/login', 'login.index')->name('login.index');

Route::view('/workorder', 'workorder.index')->name('workorder.index');
Route::view('/workorder/form', 'workorder.create')->name('workorder.create');

Route::view('/laporan', 'laporan.index')->name('laporan.index');
Route::view('/kwitansi', 'kwitansi.index')->name('kwitansi.index');

Route::get('/user', function () {
    if (request('role') !== 'admin') {
        return redirect()->route('workorder.index', ['role' => request('role', 'pelanggan')]);
    }

    return view('user.index');
})->name('user.index');

Route::get('/pelanggan', function () {
    return redirect()->route('user.index', ['role' => request('role', 'admin')]);
})->name('pelanggan.index');
