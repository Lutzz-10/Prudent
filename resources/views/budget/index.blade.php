@extends('layouts.app')
@section('title', 'Budget — Prudent')

@section('content')

@php
    $monthName = \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
@endphp

<div class="pb-32">

    {{-- Header --}}
    <div class="px-5 pt-10 pb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-display text-2xl mb-1">Budget</h1>
                <p style="color:var(--text-muted); font-size:13px;">
                    Kelola batas pengeluaranmu.
                </p>
            </div>
            {{-- Navigasi bulan --}}
            <div style="display:flex; gap:6px;">
                @php
                    $prevMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
                    $nextMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
                @endphp
                <a href="{{ route('budget.index', ['month'=>$prevMonth->month, 'year'=>$prevMonth->year]) }}"
                   style="width:36px; height:36px; border-radius:10px; background:var(--bg-card);
                          border:1px solid var(--border); display:flex; align-items:center;
                          justify-content:center; text-decoration:none; font-size:16px;">
                    ‹
                </a>
                <div style="padding:6px 12px; border-radius:10px; background:var(--bg-card);
                            border:1px solid var(--border); font-size:13px; font-weight:500;
                            display:flex; align-items:center;">
                    {{ $monthName }}
                </div>
                <a href="{{ route('budget.index', ['month'=>$nextMonth->month, 'year'=>$nextMonth->year]) }}"
                   style="width:36px; height:36px; border-radius:10px; background:var(--bg-card);
                          border:1px solid var(--border); display:flex; align-items:center;
                          justify-content:center; text-decoration:none; font-size:16px;">
                    ›
                </a>
            </div>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="px-5 mb-4">
        <div style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3);
                    color:#6EE7B7; padding:12px 16px; border-radius:12px; font-size:14px;">
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="px-5 mb-4">
        <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3);
                    color:#FCA5A5; padding:12px 16px; border-radius:12px; font-size:14px;">
            {{ $errors->first() }}
        </div>
    </div>
    @endif

    {{-- Overview Card --}}
    <div class="px-5 mb-6">
        <div style="background:var(--bg-card); border:1px solid var(--border);
                    border-radius:24px; padding:20px;">
            <p style="font-size:12px; color:var(--text-muted); letter-spacing:0.08em;
                      text-transform:uppercase; margin-bottom:16px;">
                Ringkasan {{ $monthName }}
            </p>

            {{-- Angka --}}
            <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                <div>
                    <p style="font-size:12px; color:var(--text-muted); margin-bottom:2px;">Terpakai</p>
                    <p class="font-display" style="font-size:24px;
                       color:{{ $totalPct >= 90 ? '#EF4444' : ($totalPct >= 70 ? '#F59E0B' : '#10B981') }};">
                        Rp {{ number_format($totalSpent, 0, ',', '.') }}
                    </p>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:12px; color:var(--text-muted); margin-bottom:2px;">Total Budget</p>
                    <p class="font-display" style="font-size:24px; color:var(--text-main);">
                        Rp {{ number_format($totalBudget, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Progress bar besar --}}
            <div style="height:10px; background:var(--border); border-radius:10px; overflow:hidden; margin-bottom:8px;">
                <div style="height:100%; border-radius:10px; transition: width 0.8s ease;
                            width:{{ $totalPct }}%;
                            background:{{ $totalPct >= 90 ? '#EF4444' : ($totalPct >= 70 ? '#F59E0B' : '#10B981') }};">
                </div>
            </div>

            <div style="display:flex; justify-content:space-between;">
                <p style="font-size:12px; color:var(--text-muted);">{{ $totalPct }}% terpakai</p>
                <p style="font-size:12px; color:var(--text-muted);">
                    Sisa: Rp {{ number_format(max(0, $totalBudget - $totalSpent), 0, ',', '.') }}
                </p>
            </div>

            {{-- Alert over budget --}}
            @if($totalPct >= 100)
            <div style="margin-top:12px; padding:10px 14px; border-radius:10px;
                        background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2);">
                <p style="font-size:13px; color:#FCA5A5; font-weight:500;">
                    🚨 Total pengeluaran melebihi budget bulan ini!
                </p>
            </div>
            @elseif($totalPct >= 80)
            <div style="margin-top:12px; padding:10px 14px; border-radius:10px;
                        background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.2);">
                <p style="font-size:13px; color:#FCD34D; font-weight:500;">
                    ⚠️ Pengeluaran sudah mencapai {{ $totalPct }}% dari total budget!
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- Budget per Kategori --}}
    @if($budgets->count() > 0)
    <div class="px-5 mb-6">
        <p style="font-size:14px; font-weight:600; margin-bottom:12px;">Per Kategori</p>
        <div class="space-y-3">
            @foreach($budgets as $budget)
            @php
                $pct   = $budget->percentage;
                $spent = $budget->spent;
                $sisa  = max(0, $budget->amount - $spent);
                $color = $pct >= 100 ? '#EF4444' : ($pct >= 80 ? '#F59E0B' : '#10B981');
            @endphp

            <div x-data="{ editing: false }"
                 style="background:var(--bg-card); border:1px solid var(--border);
                        border-radius:20px; padding:16px; overflow:hidden;">

                {{-- Info baris atas --}}
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                    <div style="width:40px; height:40px; border-radius:12px; flex-shrink:0;
                                background:{{ $budget->category->color ?? '#A1A1AA' }}22;
                                display:flex; align-items:center; justify-content:center; font-size:20px;">
                        {{ $budget->category->icon ?? '📦' }}
                    </div>
                    <div style="flex:1;">
                        <p style="font-size:14px; font-weight:600;">{{ $budget->category->name }}</p>
                        <p style="font-size:12px; color:var(--text-muted);">
                            Rp {{ number_format($spent, 0, ',', '.') }}
                            / Rp {{ number_format($budget->amount, 0, ',', '.') }}
                        </p>
                    </div>
                    {{-- Actions --}}
                    <div style="display:flex; gap:6px;">
                        <button @click="editing = !editing"
                                style="width:32px; height:32px; border-radius:8px; border:none;
                                       background:rgba(245,158,11,0.1); cursor:pointer; font-size:14px;">
                            ✏️
                        </button>
                        <form method="POST" action="{{ route('budget.destroy', $budget) }}"
                              onsubmit="return confirm('Hapus budget ini?')" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="width:32px; height:32px; border-radius:8px; border:none;
                                           background:rgba(239,68,68,0.1); cursor:pointer; font-size:14px;">
                                🗑️
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div style="height:6px; background:var(--border); border-radius:6px; overflow:hidden; margin-bottom:8px;">
                    <div style="height:100%; border-radius:6px; background:{{ $color }};
                                width:{{ min(100, $pct) }}%; transition: width 0.8s ease;">
                    </div>
                </div>

                {{-- Keterangan --}}
                <div style="display:flex; justify-content:space-between;">
                    <span style="font-size:12px; color:{{ $color }}; font-weight:500;">
                        {{ round($pct) }}% terpakai
                    </span>
                    <span style="font-size:12px; color:var(--text-muted);">
                        @if($pct >= 100)
                            🚨 Over budget!
                        @else
                            Sisa Rp {{ number_format($sisa, 0, ',', '.') }}
                        @endif
                    </span>
                </div>

                {{-- Form edit inline --}}
                <div x-show="editing" x-transition style="margin-top:12px; padding-top:12px;
                     border-top:1px solid var(--border);">
                    <form method="POST" action="{{ route('budget.update', $budget) }}"
                          style="display:flex; gap:8px;">
                        @csrf @method('PUT')
                        <input type="number" name="amount"
                               value="{{ $budget->amount }}"
                               class="input-field" placeholder="Nominal baru"
                               style="flex:1; padding:10px 14px; font-size:14px;" min="1000">
                        <button type="submit" class="btn-primary"
                                style="width:auto; padding:10px 16px; font-size:14px;">
                            Simpan
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Pengeluaran tanpa budget --}}
    @if($unbudgetedSpending->count() > 0)
    <div class="px-5 mb-6">
        <p style="font-size:14px; font-weight:600; margin-bottom:4px;">Belum Dibudget</p>
        <p style="font-size:12px; color:var(--text-muted); margin-bottom:12px;">
            Kategori ini sudah ada pengeluaran tapi belum punya budget.
        </p>
        <div class="space-y-2">
            @foreach($unbudgetedSpending as $item)
            <div style="background:var(--bg-card); border:1px dashed var(--border);
                        border-radius:16px; padding:14px 16px;
                        display:flex; align-items:center; justify-content:space-between; gap:12px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:20px;">{{ $item->category->icon ?? '📦' }}</span>
                    <div>
                        <p style="font-size:14px; font-weight:500;">{{ $item->category->name ?? 'Lainnya' }}</p>
                        <p style="font-size:12px; color:var(--text-muted);">Belum ada budget</p>
                    </div>
                </div>
                <p style="font-size:14px; font-weight:600; color:#FCA5A5; flex-shrink:0;">
                    Rp {{ number_format($item->total, 0, ',', '.') }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Form Tambah Budget --}}
    <div class="px-5">
        <p style="font-size:14px; font-weight:600; margin-bottom:12px;">
            + Tambah Budget Baru
        </p>
        <div style="background:var(--bg-card); border:1px solid var(--border);
                    border-radius:24px; padding:20px;">
            <form method="POST" action="{{ route('budget.store') }}">
                @csrf

                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year"  value="{{ $year }}">

                {{-- Pilih Kategori --}}
                <div style="margin-bottom:14px;">
                    <label class="label-field">Kategori</label>
                    @if($availableCategories->count() > 0)
                    <select name="category_id" class="input-field" required>
                        <option value="">Pilih kategori...</option>
                        @foreach($availableCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @else
                    <div style="padding:14px 16px; border-radius:12px;
                                background:var(--bg-input); border:1px solid var(--border);
                                color:var(--text-muted); font-size:14px;">
                        ✅ Semua kategori sudah punya budget bulan ini.
                    </div>
                    @endif
                </div>

                {{-- Nominal --}}
                <div style="margin-bottom:16px;">
                    <label class="label-field">Nominal Budget</label>
                    <div style="position:relative;">
                        <span style="position:absolute; left:14px; top:50%;
                                     transform:translateY(-50%); color:var(--text-muted);
                                     font-size:14px; font-weight:500;">Rp</span>
                        <input type="number" name="amount" class="input-field"
                               placeholder="500000" min="1000"
                               style="padding-left:40px;"
                               value="{{ old('amount') }}" required>
                    </div>
                </div>

                {{-- Shortcut nominal --}}
                <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px;">
                    @foreach([100000, 250000, 500000, 1000000] as $nominal)
                    <button type="button"
                            onclick="document.querySelector('input[name=amount]').value = {{ $nominal }}"
                            style="padding:6px 12px; border-radius:8px; font-size:12px;
                                   background:var(--bg-input); border:1px solid var(--border);
                                   color:var(--text-muted); cursor:pointer; font-family:'DM Sans',sans-serif;
                                   transition: border-color 0.2s, color 0.2s;"
                            onmouseenter="this.style.borderColor='var(--amber)'; this.style.color='var(--amber)'"
                            onmouseleave="this.style.borderColor='var(--border)'; this.style.color='var(--text-muted)'">
                        {{ 'Rp ' . number_format($nominal, 0, ',', '.') }}
                    </button>
                    @endforeach
                </div>

                @if($availableCategories->count() > 0)
                <button type="submit" class="btn-primary">
                    🎯 Tambah Budget
                </button>
                @endif
            </form>
        </div>
    </div>

</div>
@endsection