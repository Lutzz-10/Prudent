@extends('layouts.auth')
@section('title', 'Masuk — Prudent')

@section('content')
{{-- Ganti div logo-mark yang lama --}}
<div class="logo-mark">
    <img src="{{ asset('prudent-logo-512.png') }}"
         alt="Prudent" style="width:100%; height:100%; object-fit:cover; border-radius:14px;">
</div>

{{-- Heading --}}
<div class="mb-8 animate-fade-up animate-delay-1">
    <h1 class="font-display text-3xl mb-2" style="color: var(--text-main);">
        Selamat datang<br>kembali.
    </h1>
    <p style="color: var(--text-muted); font-size:14px;">Masuk untuk melihat laporan keuanganmu.</p>
</div>

{{-- Error --}}
@if($errors->any())
<div class="mb-5 px-4 py-3 rounded-xl animate-fade-up"
     style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color:#FCA5A5; font-size:14px;">
    {{ $errors->first() }}
</div>
@endif

{{-- Success --}}
@if(session('success'))
<div class="mb-5 px-4 py-3 rounded-xl animate-fade-up"
     style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color:#6EE7B7; font-size:14px;">
    {{ session('success') }}
</div>
@endif

{{-- Form --}}
<form method="POST" action="{{ route('login') }}" class="auth-card p-6 space-y-5 animate-fade-up animate-delay-2">
    @csrf

    <div>
        <label class="label-field">Email</label>
        <input type="email" name="email" class="input-field"
               placeholder="kamu@email.com"
               value="{{ old('email') }}" required autofocus>
    </div>

    <div x-data="{ show: false }">
        <label class="label-field">Password</label>
        <div style="position:relative;">
            <input :type="show ? 'text' : 'password'" name="password" class="input-field"
                   placeholder="••••••••" required style="padding-right: 48px;">
            <button type="button" @click="show = !show"
                    style="position:absolute; right:14px; top:50%; transform:translateY(-50%);
                           background:none; border:none; cursor:pointer; color: var(--text-muted); font-size:18px;">
                <span x-text="show ? '🙈' : '👁️'"></span>
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between" style="font-size:13px;">
        <label class="flex items-center gap-2 cursor-pointer" style="color: var(--text-muted);">
            <input type="checkbox" name="remember" class="rounded" style="accent-color: var(--amber);">
            Ingat saya
        </label>
    </div>

    <button type="submit" class="btn-primary">Masuk</button>
</form>

{{-- Register link --}}
<p class="text-center mt-6 animate-fade-up animate-delay-3"
   style="color: var(--text-muted); font-size:14px;">
    Belum punya akun?
    <a href="{{ route('register') }}"
       style="color: var(--amber); font-weight:500; text-decoration:none;">
        Daftar sekarang
    </a>
</p>
@endsection