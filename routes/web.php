<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login.index'));

Route::view('/login', 'login.index')->name('login.index');

Route::view('/workorder', 'workorder.index')->name('workorder.index');
Route::view('/workorder/form', 'workorder.create')->name('workorder.create');

Route::view('/laporan', 'laporan.index')->name('laporan.index');
Route::view('/kwitansi', 'kwitansi.index')->name('kwitansi.index');
Route::view('/pelanggan', 'pelanggan.index')->name('pelanggan.index');
