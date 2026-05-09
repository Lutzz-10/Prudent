@extends('layouts.app')
@section('title', 'Scan Struk — Prudent')

@section('content')
<div class="px-5 pt-8 pb-32">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="font-display text-3xl mb-1">Scan Struk</h1>
        <p style="color:var(--text-muted); font-size:14px;">
            Foto struk belanjamu, biarkan AI yang membaca.
        </p>
    </div>

    {{-- Error --}}
    @if($errors->any())
    <div class="mb-5 px-4 py-3 rounded-xl"
         style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); color:#FCA5A5; font-size:14px;">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('receipt.process') }}" enctype="multipart/form-data"
          x-data="{
              preview: null,
              loading: false,
              handleFile(e) {
                  const file = e.target.files[0];
                  if (!file) return;
                  const reader = new FileReader();
                  reader.onload = (ev) => this.preview = ev.target.result;
                  reader.readAsDataURL(file);
              }
          }"
          @submit="loading = true">
        @csrf

        {{-- Drop Zone --}}
        <div class="mb-6">
            <label for="receipt_image" class="block cursor-pointer">
                {{-- Preview --}}
                <template x-if="preview">
                    <div style="position:relative; border-radius:20px; overflow:hidden; border:2px solid var(--amber);">
                        <img :src="preview" alt="Preview" style="width:100%; max-height:400px; object-fit:cover; display:block;">
                        <div style="position:absolute; bottom:0; left:0; right:0;
                                    background:linear-gradient(transparent, rgba(0,0,0,0.7));
                                    padding:16px; font-size:13px; color:#F5F0E8;">
                            ✅ Foto siap di-scan. Ketuk untuk ganti.
                        </div>
                    </div>
                </template>

                {{-- Empty state --}}
                <template x-if="!preview">
                    <div style="border: 2px dashed var(--border); border-radius:20px;
                                padding: 48px 24px; text-align:center;
                                background: var(--bg-card); transition: border-color 0.2s;"
                         @mouseenter="$el.style.borderColor='var(--amber)'"
                         @mouseleave="$el.style.borderColor='var(--border)'">
                        <div style="font-size:48px; margin-bottom:12px;">📸</div>
                        <p style="font-weight:600; font-size:16px; margin-bottom:6px;">
                            Foto Struk Belanja
                        </p>
                        <p style="color:var(--text-muted); font-size:13px; line-height:1.5;">
                            Ketuk untuk ambil foto atau<br>pilih dari galeri
                        </p>
                        <div style="margin-top:16px; display:inline-block;
                                    background:var(--bg-input); border:1px solid var(--border);
                                    border-radius:10px; padding:8px 20px;
                                    font-size:13px; color:var(--amber);">
                            Pilih Foto
                        </div>
                    </div>
                </template>
            </label>

            {{-- Input file hidden --}}
            <input type="file" id="receipt_image" name="receipt_image"
                   accept="image/*" capture="environment"
                   class="hidden" @change="handleFile($event)">
        </div>

        {{-- Tips --}}
        <div class="mb-8 p-4 rounded-2xl" style="background:var(--bg-card); border:1px solid var(--border);">
            <p style="font-size:12px; color:var(--text-muted); font-weight:600;
                      letter-spacing:0.08em; text-transform:uppercase; margin-bottom:10px;">
                Tips foto terbaik
            </p>
            <div class="space-y-2">
                @foreach(['📋 Pastikan seluruh struk terlihat', '💡 Foto di tempat yang terang', '📐 Hindari foto miring atau blur', '🔍 Teks harus bisa terbaca jelas'] as $tip)
                <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-muted);">
                    {{ $tip }}
                </div>
                @endforeach
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-primary"
                :disabled="!preview || loading"
                :style="(!preview || loading) ? 'opacity:0.5; cursor:not-allowed;' : ''">
            <span x-show="!loading">🔍 Scan Struk Sekarang</span>
            <span x-show="loading">⏳ Sedang menganalisis...</span>
        </button>
    </form>
</div>
@endsection