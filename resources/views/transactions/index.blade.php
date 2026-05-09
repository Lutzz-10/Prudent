@extends('layouts.app')
@section('title', 'Riwayat — Prudent')

@section('content')
<div class="pb-32">

    {{-- Header --}}
    <div class="px-5 pt-10 pb-4">
        <h1 class="font-display text-2xl mb-1">Riwayat</h1>
        <p style="color:var(--text-muted); font-size:13px;">
            Semua transaksi yang tercatat.
        </p>
    </div>

    {{-- Filter Bar --}}
    <div class="px-5 mb-4" x-data="{ showFilter: false }">
        <form method="GET" action="{{ route('transaction.index') }}" id="filterForm">

            {{-- Search --}}
            <div style="position:relative; margin-bottom:12px;">
                <span style="position:absolute; left:14px; top:50%;
                             transform:translateY(-50%); font-size:16px;">🔍</span>
                <input type="text" name="search" placeholder="Cari toko atau catatan..."
                       value="{{ request('search') }}"
                       class="input-field" style="padding-left:42px;"
                       onchange="document.getElementById('filterForm').submit()">
            </div>

            {{-- Filter chips --}}
            <div style="display:flex; gap:8px; overflow-x:auto; padding-bottom:4px;
                        scrollbar-width:none;">

                {{-- Bulan --}}
                <input type="month" name="month"
                       value="{{ request('month', now()->format('Y-m')) }}"
                       class="input-field"
                       style="min-width:150px; padding:10px 14px; font-size:13px;"
                       onchange="document.getElementById('filterForm').submit()">

                {{-- Kategori --}}
                <select name="category" class="input-field"
                        style="min-width:160px; padding:10px 14px; font-size:13px;"
                        onchange="document.getElementById('filterForm').submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->icon }} {{ $cat->name }}
                    </option>
                    @endforeach
                </select>

                {{-- Reset --}}
                @if(request()->hasAny(['search','category','month']))
                <a href="{{ route('transaction.index') }}"
                   style="white-space:nowrap; padding:10px 16px; border-radius:12px;
                          background:rgba(239,68,68,0.1); color:#FCA5A5;
                          font-size:13px; text-decoration:none; flex-shrink:0;">
                    ✕ Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Summary bar --}}
    <div class="px-5 mb-4">
        <div style="background:var(--bg-card); border:1px solid var(--border);
                    border-radius:16px; padding:14px 16px;
                    display:flex; justify-content:space-between; align-items:center;">
            <div>
                <p style="font-size:12px; color:var(--text-muted);">
                    {{ $transactions->total() }} transaksi
                </p>
            </div>
            <div>
                <p style="font-size:16px; font-weight:600; color:var(--amber);">
                    Rp {{ number_format($transactions->sum('total_amount'), 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- List Transaksi --}}
    <div class="px-5">
        @forelse($transactions->groupBy(fn($t) => $t->transaction_date->format('Y-m-d')) as $date => $group)

        {{-- Tanggal header --}}
        <div style="margin-bottom:8px; margin-top:16px;">
            <p style="font-size:12px; color:var(--text-muted); font-weight:600;
                      letter-spacing:0.06em; text-transform:uppercase;">
                {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
            </p>
        </div>

        {{-- Transaksi dalam tanggal ini --}}
        <div class="space-y-2 mb-2">
            @foreach($group as $trx)
            <a href="{{ route('transaction.show', $trx) }}"
               style="text-decoration:none; display:block;">
                <div style="background:var(--bg-card); border:1px solid var(--border);
                            border-radius:16px; padding:14px 16px;
                            display:flex; align-items:center; gap:12px;
                            transition: border-color 0.2s;"
                     onmouseenter="this.style.borderColor='var(--amber)'"
                     onmouseleave="this.style.borderColor='var(--border)'">

                    {{-- Icon --}}
                    <div style="width:44px; height:44px; border-radius:14px; flex-shrink:0;
                                background:{{ $trx->category->color ?? '#A1A1AA' }}22;
                                display:flex; align-items:center;
                                justify-content:center; font-size:22px;">
                        {{ $trx->category->icon ?? '📦' }}
                    </div>

                    {{-- Info --}}
                    <div style="flex:1; min-width:0;">
                        <p style="font-size:14px; font-weight:500; color:var(--text-main);
                                  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $trx->receipt->store_name ?? ($trx->category->name ?? 'Transaksi') }}
                        </p>
                        <p style="font-size:12px; color:var(--text-muted);">
                            {{ $trx->category->name ?? 'Lainnya' }}
                            @if($trx->notes)
                                · {{ Str::limit($trx->notes, 25) }}
                            @endif
                        </p>
                    </div>

                    {{-- Jumlah & arrow --}}
                    <div style="text-align:right; flex-shrink:0;">
                        <p style="font-size:15px; font-weight:600; color:var(--text-main);">
                            Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                        </p>
                        <p style="font-size:12px; color:var(--text-muted);">›</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        @empty
        {{-- Empty state --}}
        <div style="text-align:center; padding:48px 24px; margin-top:16px;
                    background:var(--bg-card); border:1px solid var(--border);
                    border-radius:24px;">
            <div style="font-size:48px; margin-bottom:12px; opacity:0.4;">🔍</div>
            <p style="font-size:15px; font-weight:500; margin-bottom:6px;">
                Tidak ada transaksi
            </p>
            <p style="color:var(--text-muted); font-size:13px; margin-bottom:20px;">
                Coba ubah filter atau scan struk baru.
            </p>
            <a href="{{ route('receipt.upload') }}"
               style="background:var(--amber); color:#0F0F0F; font-weight:600;
                      padding:12px 24px; border-radius:12px; text-decoration:none; font-size:14px;">
                📸 Scan Sekarang
            </a>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($transactions->hasPages())
        <div style="margin-top:24px; display:flex; justify-content:center; gap:8px;">
            @if($transactions->onFirstPage())
                <span style="padding:10px 16px; border-radius:10px;
                             background:var(--bg-card); color:var(--text-muted);
                             font-size:13px;">← Prev</span>
            @else
                <a href="{{ $transactions->previousPageUrl() }}"
                   style="padding:10px 16px; border-radius:10px;
                          background:var(--bg-card); border:1px solid var(--border);
                          color:var(--text-main); font-size:13px; text-decoration:none;">
                    ← Prev
                </a>
            @endif

            <span style="padding:10px 16px; border-radius:10px;
                         background:var(--amber); color:#0F0F0F;
                         font-size:13px; font-weight:600;">
                {{ $transactions->currentPage() }} / {{ $transactions->lastPage() }}
            </span>

            @if($transactions->hasMorePages())
                <a href="{{ $transactions->nextPageUrl() }}"
                   style="padding:10px 16px; border-radius:10px;
                          background:var(--bg-card); border:1px solid var(--border);
                          color:var(--text-main); font-size:13px; text-decoration:none;">
                    Next →
                </a>
            @else
                <span style="padding:10px 16px; border-radius:10px;
                             background:var(--bg-card); color:var(--text-muted);
                             font-size:13px;">Next →</span>
            @endif
        </div>
        @endif

    </div>
</div>
@endsection