@extends('layouts.app')
@section('title', 'Profil — Prudent')

@section('content')
<div class="pb-32" x-data="{ tab: '{{ session('tab', 'profil') }}' }">

    {{-- Header --}}
    <div class="px-5 pt-10 pb-6"
         style="background:linear-gradient(180deg, #1A1A1A 0%, var(--bg-dark) 100%);">

        {{-- Avatar + Info --}}
        <div style="display:flex; flex-direction:column; align-items:center; text-align:center; padding:20px 0;">
            <div style="position:relative; margin-bottom:16px;">
                {{-- Avatar --}}
                <div style="width:88px; height:88px; border-radius:28px; overflow:hidden;
                            border:3px solid var(--amber); background:var(--bg-card);">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}"
                             alt="Avatar" style="width:100%; height:100%; object-fit:cover;">
                    @else
                        <div style="width:100%; height:100%; display:flex; align-items:center;
                                    justify-content:center; background:var(--amber);
                                    font-size:36px; font-weight:700; color:#0F0F0F;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </div>
            <h1 class="font-display text-2xl mb-1">{{ $user->name }}</h1>
            <p style="color:var(--text-muted); font-size:13px;">{{ $user->email }}</p>
            <p style="color:var(--text-muted); font-size:12px; margin-top:4px;">
                Member sejak {{ $memberSince }}
            </p>
        </div>

        {{-- Stats row --}}
        {{-- Ganti bagian stats row --}}
@php
    $short = $totalSpent >= 1000000000
        ? number_format($totalSpent/1000000000, 1) . 'M'
        : ($totalSpent >= 1000000
            ? number_format($totalSpent/1000000, 1) . 'jt'
            : ($totalSpent >= 1000
                ? number_format($totalSpent/1000, 0) . 'rb'
                : number_format($totalSpent, 0)));
@endphp

{{-- Stats row --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-top:8px;">
    @foreach([
        ['label'=>'Transaksi',   'value'=> $totalTransactions],
        ['label'=>'Struk',       'value'=> $totalReceipts],
        ['label'=>'Total Keluar','value'=> 'Rp ' . $short],
    ] as $stat)
    <div style="background:var(--bg-card); border:1px solid var(--border);
                border-radius:16px; padding:12px; text-align:center;">
        <p class="font-display" style="font-size:20px; color:var(--amber);">
            {{ $stat['value'] }}
        </p>
        <p style="font-size:11px; color:var(--text-muted); margin-top:2px;">
            {{ $stat['label'] }}
        </p>
    </div>
    @endforeach
</div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="px-5 mt-4">
        <div style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3);
                    color:#6EE7B7; padding:12px 16px; border-radius:12px; font-size:14px;">
            {{ session('success') }}
        </div>
    </div>
    @endif

    {{-- Tab switcher --}}
    <div class="px-5 mt-6 mb-4">
        <div style="display:flex; background:var(--bg-card); border:1px solid var(--border);
                    border-radius:14px; padding:4px; gap:4px;">
            @foreach(['profil'=>'✏️ Edit Profil', 'password'=>'🔐 Password', 'akun'=>'⚙️ Akun'] as $key => $label)
            <button @click="tab = '{{ $key }}'"
                    :style="tab === '{{ $key }}'
                        ? 'background:var(--amber); color:#0F0F0F; font-weight:600;'
                        : 'background:transparent; color:var(--text-muted);'"
                    style="flex:1; padding:10px 6px; border:none; border-radius:10px;
                           font-family:'DM Sans',sans-serif; font-size:12px; cursor:pointer;
                           transition: all 0.2s;">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- TAB: Edit Profil --}}
    <div x-show="tab === 'profil'" x-transition class="px-5">
        <div style="background:var(--bg-card); border:1px solid var(--border);
                    border-radius:24px; padding:20px;">
            <form method="POST" action="{{ route('profile.update') }}"
                  enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- Upload Avatar --}}
                <div style="margin-bottom:20px; text-align:center;"
                     x-data="{
                         preview: '{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}',
                         handleFile(e) {
                             const file = e.target.files[0];
                             if (!file) return;
                             const reader = new FileReader();
                             reader.onload = ev => this.preview = ev.target.result;
                             reader.readAsDataURL(file);
                         }
                     }">
                    <label for="avatar_input" style="cursor:pointer; display:inline-block;">
                        <div style="width:72px; height:72px; border-radius:22px; overflow:hidden;
                                    border:2px dashed var(--border); margin:0 auto 8px;
                                    display:flex; align-items:center; justify-content:center;
                                    background:var(--bg-input); position:relative;">
                            <template x-if="preview">
                                <img :src="preview" style="width:100%; height:100%; object-fit:cover;">
                            </template>
                            <template x-if="!preview">
                                <span style="font-size:28px;">📷</span>
                            </template>
                            <div style="position:absolute; bottom:0; left:0; right:0;
                                        background:rgba(0,0,0,0.5); padding:3px;
                                        font-size:10px; color:#fff; text-align:center;">
                                Ubah
                            </div>
                        </div>
                    </label>
                    <input type="file" id="avatar_input" name="avatar"
                           accept="image/*" class="hidden"
                           @change="handleFile($event)">
                </div>

                <div style="margin-bottom:14px;">
                    <label class="label-field">Nama Lengkap</label>
                    <input type="text" name="name" class="input-field"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')
                    <p style="color:#FCA5A5; font-size:12px; margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom:20px;">
                    <label class="label-field">Email</label>
                    <input type="email" name="email" class="input-field"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')
                    <p style="color:#FCA5A5; font-size:12px; margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    {{-- TAB: Ubah Password --}}
    <div x-show="tab === 'password'" x-transition class="px-5">
        <div style="background:var(--bg-card); border:1px solid var(--border);
                    border-radius:24px; padding:20px;">
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf @method('PUT')

                <div style="margin-bottom:14px;" x-data="{ show: false }">
                    <label class="label-field">Password Saat Ini</label>
                    <div style="position:relative;">
                        <input :type="show ? 'text' : 'password'"
                               name="current_password" class="input-field"
                               placeholder="••••••••" style="padding-right:48px;">
                        <button type="button" @click="show = !show"
                                style="position:absolute; right:14px; top:50%; transform:translateY(-50%);
                                       background:none; border:none; cursor:pointer; font-size:16px;">
                            <span x-text="show ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                    @error('current_password')
                    <p style="color:#FCA5A5; font-size:12px; margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom:14px;" x-data="{ show: false }">
                    <label class="label-field">Password Baru</label>
                    <div style="position:relative;">
                        <input :type="show ? 'text' : 'password'"
                               name="password" class="input-field"
                               placeholder="Min. 8 karakter" style="padding-right:48px;">
                        <button type="button" @click="show = !show"
                                style="position:absolute; right:14px; top:50%; transform:translateY(-50%);
                                       background:none; border:none; cursor:pointer; font-size:16px;">
                            <span x-text="show ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                </div>

                <div style="margin-bottom:20px;">
                    <label class="label-field">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                           class="input-field" placeholder="Ulangi password baru">
                    @error('password')
                    <p style="color:#FCA5A5; font-size:12px; margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary">🔐 Ubah Password</button>
            </form>
        </div>
    </div>

    {{-- TAB: Pengaturan Akun --}}
    <div x-show="tab === 'akun'" x-transition class="px-5">

        {{-- Logout --}}
        <div style="background:var(--bg-card); border:1px solid var(--border);
                    border-radius:20px; padding:16px; margin-bottom:12px;">
            <p style="font-size:14px; font-weight:600; margin-bottom:4px;">Keluar Akun</p>
            <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">
                Sesi kamu akan diakhiri di perangkat ini.
            </p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        style="width:100%; padding:13px; border-radius:12px; cursor:pointer;
                               background:transparent; border:1px solid var(--border);
                               color:var(--text-main); font-family:'DM Sans',sans-serif;
                               font-size:14px; font-weight:500;">
                    🚪 Logout
                </button>
            </form>
        </div>

        {{-- Hapus Akun --}}
        <div style="background:rgba(239,68,68,0.05); border:1px solid rgba(239,68,68,0.2);
                    border-radius:20px; padding:16px;"
             x-data="{ confirm: false }">
            <p style="font-size:14px; font-weight:600; color:#FCA5A5; margin-bottom:4px;">
                Hapus Akun
            </p>
            <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">
                Semua data, transaksi, dan struk akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
            </p>
            <button @click="confirm = !confirm" type="button"
                    style="width:100%; padding:13px; border-radius:12px; cursor:pointer;
                           background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3);
                           color:#FCA5A5; font-family:'DM Sans',sans-serif;
                           font-size:14px; font-weight:500;">
                🗑️ Hapus Akun Saya
            </button>

            {{-- Konfirmasi --}}
            <div x-show="confirm" x-transition style="margin-top:14px; padding-top:14px;
                 border-top:1px solid rgba(239,68,68,0.2);">
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf @method('DELETE')
                    <p style="font-size:13px; color:#FCA5A5; margin-bottom:10px;">
                        Ketik <strong>HAPUS</strong> untuk konfirmasi:
                    </p>
                    <input type="text" name="confirm_delete" class="input-field"
                           placeholder="HAPUS"
                           style="margin-bottom:10px; border-color:rgba(239,68,68,0.3);">
                    @error('confirm_delete')
                    <p style="color:#FCA5A5; font-size:12px; margin-bottom:8px;">{{ $message }}</p>
                    @enderror
                    <button type="submit"
                            style="width:100%; padding:13px; border-radius:12px; cursor:pointer;
                                   background:#EF4444; border:none; color:#fff;
                                   font-family:'DM Sans',sans-serif;
                                   font-size:14px; font-weight:600;">
                        Konfirmasi Hapus Akun
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection