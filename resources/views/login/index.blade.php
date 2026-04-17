@extends('layout.app', ['hideNavbar' => true])

@section('title', 'Login - Bengkel Motor')

@section('content')
    <section class="form-page" style="max-width:420px; margin-top: 4rem;">
        <h1 class="page-title" style="margin-top:0;">Login Sistem Bengkel</h1>
        <p style="margin-top:-.4rem; color: var(--muted); font-size:.9rem;">Masukkan username dan password untuk masuk ke sistem.</p>

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
            Demo akun: <strong>admin</strong> (role admin) dan <strong>pelanggan</strong> (role pelanggan).<br>
            Password default: <strong>password</strong>
        </div>
    </section>
@endsection
