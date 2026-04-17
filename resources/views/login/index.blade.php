@extends('layout.app', ['hideNavbar' => true])

@section('title', 'Login - Reno Motor')

@section('content')
    <section class="login-shell">
        <aside class="login-showcase">
            <img src="{{ asset('images/reno-motor-logo.svg') }}" alt="Logo Reno Motor">
            <h2>Reno Motor</h2>
            <p>Selamat datang di sistem manajemen bengkel. Pantau work order, laporan, dan kwitansi dalam satu dashboard yang ringkas.</p>
            <div class="login-contact">
                <p><i class="bi bi-geo-alt"></i> Jl. Cikpuan No.6, Sungai Panas, Kec. Batam Kota, Kota Batam, Kepulauan Riau 29444</p>
                <p><i class="bi bi-telephone"></i> 0812-7088-8722</p>
            </div>
        </aside>

        <div class="form-page" style="max-width:100%; margin: 0;">
            <h1 class="page-title" style="margin-top:0;">Login Sistem Bengkel</h1>
            <p style="margin-top:-.4rem; color: var(--muted); font-size:.9rem;">Masukkan username dan password untuk masuk ke sistem Reno Motor.</p>

            @if ($errors->any())
                <div style="margin-bottom:.9rem; padding:.7rem .8rem; border-radius:10px; background:#fee2e2; color:#b91c1c; font-size:.86rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.authenticate') }}" method="POST">
                @csrf
                <div style="margin-bottom:.8rem;">
                    <label for="username">Username</label>
                    <input class="input" id="username" name="username" type="text" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus>
                </div>
                <div style="margin-bottom:.9rem;">
                    <label for="password">Password</label>
                    <input class="input" id="password" name="password" type="password" placeholder="Masukkan password" required>
                </div>
                <button class="btn btn-primary" type="submit" style="width:100%; justify-content:center;">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </form>

            <div class="role-box" style="margin-top:1rem;">
                Bengkel Reno Motor &mdash; Solusi servis motor cepat dan terpercaya.
            </div>
        </div>
    </section>
@endsection
