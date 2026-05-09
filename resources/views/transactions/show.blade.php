@extends('layouts.app')
@section('title', 'Detail Transaksi — Prudent')

@section('content')
<div class="pb-32">

    {{-- Header --}}
    <div class="px-5 pt-10 pb-6">
        <a href="{{ route('transaction.index') }}"
           style="color:var(--text-muted); font-size:13px; text-decoration:none;">
            ← Riwayat
        </a>
        <h1 class="font-display text-2xl mt-2">Detail Transaksi</h1>
    </div>

    {{-- Foto Struk --}}
    @if($transaction->receipt && $transaction->receipt->image_path)
    <div class="px-5 mb-6">
        <div style="border-radius:20px; overflow:hidden; border:1px solid var(--border);">
            <img src="{{ $transaction->receipt->image_url }}"
                 alt="Struk" style="width:100%; max-height:280px; object-fit:cover;">
        </div>
    </div>
    @endif

    {{-- Info Utama --}}
    <div class="px-5 mb-4">
        <div style="background:var(--bg-card); border:1px solid var(--border);
                    border-radius:24px; padding:20px;">

            {{-- Total besar --}}
            <div style="text-align:center; padding-bottom:16px;
                        border-bottom:1px solid var(--border); margin-bottom:16px;">
                <p style="font-size:12px; color:var(--text-muted); margin-bottom:6px;
                          letter-spacing:0.08em; text-transform:uppercase;">Total Belanja</p>
                <p class="font-display" style="font-size:40px; color:var(--amber);">
                    Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                </p>
            </div>

            {{-- Detail rows --}}
            <div class="space-y-3">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:13px; color:var(--text-muted);">Toko</span>
                    <span style="font-size:14px; font-weight:500;">
                        {{ $transaction->receipt->store_name ?? '—' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:13px; color:var(--text-muted);">Tanggal</span>
                    <span style="font-size:14px; font-weight:500;">
                        {{ $transaction->transaction_date->translatedFormat('d F Y') }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:13px; color:var(--text-muted);">Kategori</span>
                    <span style="font-size:14px; font-weight:500;">
                        {{ $transaction->category->icon ?? '' }}
                        {{ $transaction->category->name ?? 'Lainnya' }}
                    </span>
                </div>
                @if($transaction->notes)
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:13px; color:var(--text-muted);">Catatan</span>
                    <span style="font-size:14px;">{{ $transaction->notes }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Item List --}}
    @if($transaction->items->count() > 0)
    <div class="px-5 mb-4">
        <p style="font-size:14px; font-weight:600; margin-bottom:12px;">
            Item Belanja ({{ $transaction->items->count() }})
        </p>
        <div style="background:var(--bg-card); border:1px solid var(--border); border-radius:20px;
                    overflow:hidden;">
            @foreach($transaction->items as $i => $item)
            <div style="padding:14px 16px;
                        {{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}
                        display:flex; justify-content:space-between; align-items:center; gap:12px;">
                <div style="flex:1; min-width:0;">
                    <p style="font-size:14px; font-weight:500; color:var(--text-main);">
                        {{ $item->item_name }}
                    </p>
                    <p style="font-size:12px; color:var(--text-muted);">
                        {{ $item->qty }}x
                        Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                    </p>
                </div>
                <p style="font-size:14px; font-weight:600; flex-shrink:0;">
                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </p>
            </div>
            @endforeach

            {{-- Total row --}}
            <div style="padding:14px 16px; background:rgba(245,158,11,0.06);
                        border-top:1px solid var(--border);
                        display:flex; justify-content:space-between; align-items:center;">
                <p style="font-size:14px; font-weight:600;">Total</p>
                <p style="font-size:16px; font-weight:700; color:var(--amber);">
                    Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Hapus Transaksi --}}
    <div class="px-5 mt-6">
        <form method="POST" action="{{ route('transaction.destroy', $transaction) }}"
              onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    style="width:100%; padding:14px; border-radius:12px; cursor:pointer;
                           background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2);
                           color:#FCA5A5; font-family:'DM Sans',sans-serif;
                           font-size:14px; font-weight:500;
                           transition: background 0.2s;">
                🗑️ Hapus Transaksi
            </button>
        </form>
    </div>

</div>
@endsection