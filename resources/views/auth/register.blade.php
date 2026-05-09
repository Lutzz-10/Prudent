@extends('layouts.auth')
@section('title', 'Daftar — Prudent')

@section('content')
{{-- Logo --}}
<div class="flex items-center gap-3 mb-10 animate-fade-up">
    <div class="logo-mark">🧾</div>
    <span class="font-display text-2xl" style="color: var(--text-main);">Prudent</span>
</div>

{{-- Heading --}}
<div class="mb-8 animate-fade-up animate-delay-1">
    <h1 class="font-display text-3xl mb-2" style="color: var(--text-main);">
        Mulai kelola<br>keuanganmu.
    </h1>
    <p style="color: var(--text-muted); font-size:14px;">Buat akun gratis dan mulai scan struk pertamamu.</p>
</div>

{{-- Errors --}}
@if($errors->any())
<div class="mb-5 px-4 py-3 rounded-xl animate-fade-up"
     style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color:#FCA5A5; font-size:14px;">
    <ul class="space-y-1">
        @foreach($errors->all() as $err)
            <li>• {{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Form --}}
<form method="POST" action="{{ route('register') }}" class="auth-card p-6 space-y-5 animate-fade-up animate-delay-2">
    @csrf

    <div>
        <label class="label-field">Nama Lengkap</label>
        <input type="text" name="name" class="input-field"
               placeholder="Ahmad Lutfi"
               value="{{ old('name') }}" required autofocus>
    </div>

    <div>
        <label class="label-field">Email</label>
        <input type="email" name="email" class="input-field"
               placeholder="kamu@email.com"
               value="{{ old('email') }}" required>
    </div>

    <div x-data="{ show: false }">
        <label class="label-field">Password</label>
        <div style="position:relative;">
            <input :type="show ? 'text' : 'password'" name="password" class="input-field"
                   placeholder="Min. 8 karakter" required style="padding-right: 48px;">
            <button type="button" @click="show = !show"
                    style="position:absolute; right:14px; top:50%; transform:translateY(-50%);
                           background:none; border:none; cursor:pointer; color: var(--text-muted); font-size:18px;">
                <span x-text="show ? '🙈' : '👁️'"></span>
            </button>
        </div>
    </div>

    <div>
        <label class="label-field">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="input-field"
               placeholder="Ulangi password" required>
    </div>

    {{-- Password strength indicator --}}
    <div x-data="{
        strength: 0,
        check(e) {
            let v = e.target.value;
            this.strength = 0;
            if (v.length >= 8) this.strength++;
            if (/[A-Z]/.test(v)) this.strength++;
            if (/[0-9]/.test(v)) this.strength++;
            if (/[^A-Za-z0-9]/.test(v)) this.strength++;
        }
    }" @input.window="check($event)" style="display:none">
        {{-- placeholder for future strength meter --}}
    </div>

    <button type="submit" class="btn-primary">Buat Akun</button>
</form>

{{-- Login link --}}
<p class="text-center mt-6 animate-fade-up animate-delay-3"
   style="color: var(--text-muted); font-size:14px;">
    Sudah punya akun?
    <a href="{{ route('login') }}"
       style="color: var(--amber); font-weight:500; text-decoration:none;">
        Masuk di sini
    </a>
</p>
@endsection