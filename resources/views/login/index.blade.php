@extends('layout.app', ['hideNavbar' => true])

@section('title', 'Login - Bengkel Motor')

@section('content')
    <section class="form-page" style="max-width:420px; margin-top: 4rem;">
        <h1 class="page-title" style="margin-top:0;">Login Sistem Bengkel</h1>
        <p style="margin-top:-.4rem; color: var(--muted); font-size:.9rem;">Silakan login sebelum masuk ke sistem.</p>

        <form>
            <div style="margin-bottom:.8rem;">
                <label for="username">ID / Username</label>
                <input class="input" id="username" type="text" placeholder="Masukkan username">
            </div>
            <div style="margin-bottom:.9rem;">
                <label for="password">Password</label>
                <input class="input" id="password" type="password" placeholder="Masukkan password">
            </div>
            <a class="btn btn-primary" href="{{ route('workorder.index', ['role' => 'admin']) }}" style="width:100%; justify-content:center;">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
        </form>
    </section>
@endsection
