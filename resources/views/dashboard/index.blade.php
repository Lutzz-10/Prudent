@extends('layouts.app')
@section('title', 'Dashboard — Prudent')

@section('content')

<script>
    const chartLabels = @json($chartLabels);
    const chartValues = @json($chartValues);
    const categoryData = @json($categoryData);
</script>

<div class="pb-32">

    {{-- ===== HEADER ===== --}}
    <div class="px-5 pt-10 pb-6"
         style="background: linear-gradient(180deg, #1A1A1A 0%, var(--bg-dark) 100%);">

        <div class="flex items-center justify-between mb-6">
            <div>
                <p style="color:var(--text-muted); font-size:13px;">Selamat datang,</p>
                <h1 class="font-display text-2xl">{{ $user->name }}</h1>
            </div>
            <div style="width:42px; height:42px; border-radius:14px;
                        background:var(--amber); display:flex; align-items:center;
                        justify-content:center; font-size:18px; font-weight:700; color:#0F0F0F;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        </div>

        {{-- Total bulan ini --}}
        <div style="background:var(--bg-card); border:1px solid var(--border);
                    border-radius:24px; padding:24px;">
            <p style="font-size:12px; color:var(--text-muted); letter-spacing:0.08em;
                      text-transform:uppercase; margin-bottom:8px;">
                Pengeluaran {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
            </p>
            <div class="flex items-end justify-between">
                <div>
                    <p class="font-display" style="font-size:36px; line-height:1; color:var(--text-main);">
                        Rp {{ number_format($totalThisMonth, 0, ',', '.') }}
                    </p>
                    <div class="flex items-center gap-1 mt-2">
                        @if($changePercent > 0)
                            <span style="background:rgba(239,68,68,0.15); color:#FCA5A5;
                                         font-size:12px; padding:3px 8px; border-radius:20px;">
                                ↑ {{ abs($changePercent) }}% vs bulan lalu
                            </span>
                        @elseif($changePercent < 0)
                            <span style="background:rgba(16,185,129,0.15); color:#6EE7B7;
                                         font-size:12px; padding:3px 8px; border-radius:20px;">
                                ↓ {{ abs($changePercent) }}% vs bulan lalu
                            </span>
                        @else
                            <span style="background:var(--bg-input); color:var(--text-muted);
                                         font-size:12px; padding:3px 8px; border-radius:20px;">
                                — sama seperti bulan lalu
                            </span>
                        @endif
                    </div>
                </div>
                <div style="font-size:40px; opacity:0.3;">📊</div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
<div class="px-5 mb-6">
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px;">

        <a href="{{ url('/scan') }}"
           style="background:var(--bg-card); border:1px solid var(--border);
                  border-radius:18px; padding:16px 12px; text-align:center;
                  text-decoration:none; display:block;"
           onmousedown="this.style.transform='scale(0.95)'"
           onmouseup="this.style.transform='scale(1)'">
            <div style="font-size:26px; margin-bottom:6px;">📸</div>
            <p style="font-size:12px; color:var(--text-muted); font-weight:500;">Scan Struk</p>
        </a>

        <a href="{{ url('/riwayat') }}"
           style="background:var(--bg-card); border:1px solid var(--border);
                  border-radius:18px; padding:16px 12px; text-align:center;
                  text-decoration:none; display:block;"
           onmousedown="this.style.transform='scale(0.95)'"
           onmouseup="this.style.transform='scale(1)'">
            <div style="font-size:26px; margin-bottom:6px;">📋</div>
            <p style="font-size:12px; color:var(--text-muted); font-weight:500;">Riwayat</p>
        </a>

        <a href="{{ url('/budget') }}"
           style="background:var(--bg-card); border:1px solid var(--border);
                  border-radius:18px; padding:16px 12px; text-align:center;
                  text-decoration:none; display:block;"
           onmousedown="this.style.transform='scale(0.95)'"
           onmouseup="this.style.transform='scale(1)'">
            <div style="font-size:26px; margin-bottom:6px;">💰</div>
            <p style="font-size:12px; color:var(--text-muted); font-weight:500;">Budget</p>
        </a>

    </div>
</div>

    {{-- ===== CHART PENGELUARAN 30 HARI ===== --}}
    <div class="px-5 mb-6">
        <div style="background:var(--bg-card); border:1px solid var(--border);
                    border-radius:24px; padding:20px;">
            <div class="flex items-center justify-between mb-4">
                <p style="font-size:14px; font-weight:600;">Tren 30 Hari</p>
                <p style="font-size:12px; color:var(--text-muted);">Pengeluaran harian</p>
            </div>
            <canvas id="lineChart" height="120"></canvas>
        </div>
    </div>

    {{-- ===== KATEGORI BULAN INI ===== --}}
    @if($categoryData->count() > 0)
    <div class="px-5 mb-6">
        <p style="font-size:14px; font-weight:600; margin-bottom:12px;">Per Kategori</p>
        <div class="space-y-3">
            @php $maxCat = $categoryData->max('total'); @endphp
            @foreach($categoryData->sortByDesc('total') as $cat)
            <div style="background:var(--bg-card); border:1px solid var(--border);
                        border-radius:16px; padding:14px 16px;">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span style="font-size:20px;">{{ $cat['icon'] }}</span>
                        <span style="font-size:14px; font-weight:500;">{{ $cat['name'] }}</span>
                    </div>
                    <span style="font-size:14px; font-weight:600; color:var(--text-main);">
                        Rp {{ number_format($cat['total'], 0, ',', '.') }}
                    </span>
                </div>
                {{-- Progress bar --}}
                <div style="height:4px; background:var(--border); border-radius:4px; overflow:hidden;">
                    <div style="height:100%; border-radius:4px;
                                background:{{ $cat['color'] }};
                                width:{{ $maxCat > 0 ? round(($cat['total']/$maxCat)*100) : 0 }}%;
                                transition: width 0.8s ease;">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ===== BUDGET STATUS ===== --}}
    @if($budgets->count() > 0)
    <div class="px-5 mb-6">
        <div class="flex items-center justify-between mb-3">
            <p style="font-size:14px; font-weight:600;">Status Budget</p>
            <a href="#" style="font-size:12px; color:var(--amber); text-decoration:none;">
                Atur Budget
            </a>
        </div>
        <div class="space-y-3">
            @foreach($budgets as $budget)
            @php
                $pct   = $budget->percentage;
                $spent = $budget->spent;
                $color = $pct >= 90 ? '#EF4444' : ($pct >= 70 ? '#F59E0B' : '#10B981');
            @endphp
            <div style="background:var(--bg-card); border:1px solid var(--border);
                        border-radius:16px; padding:14px 16px;">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span>{{ $budget->category->icon ?? '📦' }}</span>
                        <span style="font-size:14px;">{{ $budget->category->name ?? 'Lainnya' }}</span>
                    </div>
                    <span style="font-size:12px; color:var(--text-muted);">
                        Rp {{ number_format($spent, 0, ',', '.') }}
                        / Rp {{ number_format($budget->amount, 0, ',', '.') }}
                    </span>
                </div>
                <div style="height:6px; background:var(--border); border-radius:6px; overflow:hidden;">
                    <div style="height:100%; border-radius:6px; background:{{ $color }};
                                width:{{ $pct }}%; transition: width 0.8s ease;">
                    </div>
                </div>
                @if($pct >= 90)
                <p style="font-size:11px; color:#FCA5A5; margin-top:6px;">
                    ⚠️ Budget hampir habis!
                </p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ===== TRANSAKSI TERBARU ===== --}}
    <div class="px-5">
        <div class="flex items-center justify-between mb-3">
            <p style="font-size:14px; font-weight:600;">Transaksi Terbaru</p>
            <a href="#" style="font-size:12px; color:var(--amber); text-decoration:none;">
                Lihat semua
            </a>
        </div>

        @if($recentTransactions->count() > 0)
        <div class="space-y-3">
            @foreach($recentTransactions as $trx)
            <div style="background:var(--bg-card); border:1px solid var(--border);
                        border-radius:16px; padding:14px 16px;
                        display:flex; align-items:center; gap:12px;">

                {{-- Icon kategori --}}
                <div style="width:44px; height:44px; border-radius:14px; flex-shrink:0;
                            background:{{ $trx->category->color ?? '#A1A1AA' }}22;
                            display:flex; align-items:center; justify-content:center; font-size:22px;">
                    {{ $trx->category->icon ?? '📦' }}
                </div>

                {{-- Info --}}
                <div style="flex:1; min-width:0;">
                    <p style="font-size:14px; font-weight:500; white-space:nowrap;
                              overflow:hidden; text-overflow:ellipsis;">
                        {{ $trx->receipt->store_name ?? ($trx->category->name ?? 'Transaksi') }}
                    </p>
                    <p style="font-size:12px; color:var(--text-muted);">
                        {{ $trx->transaction_date->translatedFormat('d F Y') }}
                        @if($trx->notes)
                            · {{ Str::limit($trx->notes, 20) }}
                        @endif
                    </p>
                </div>

                {{-- Jumlah --}}
                <p style="font-size:15px; font-weight:600; color:var(--text-main);
                          white-space:nowrap; flex-shrink:0;">
                    - Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                </p>
            </div>
            @endforeach
        </div>

        @else
        {{-- Empty state --}}
        <div style="text-align:center; padding:48px 24px;
                    background:var(--bg-card); border:1px solid var(--border);
                    border-radius:24px;">
            <div style="font-size:48px; margin-bottom:12px; opacity:0.4;">🧾</div>
            <p style="font-size:15px; font-weight:500; margin-bottom:6px;">
                Belum ada transaksi
            </p>
            <p style="font-size:13px; color:var(--text-muted); margin-bottom:20px;">
                Scan struk pertamamu sekarang!
            </p>
            <a href="{{ route('receipt.upload') }}"
               style="background:var(--amber); color:#0F0F0F; font-weight:600;
                      padding:12px 24px; border-radius:12px; text-decoration:none;
                      font-size:14px;">
                📸 Scan Sekarang
            </a>
        </div>
        @endif
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ===== Line Chart — Tren 30 Hari =====
    const ctx = document.getElementById('lineChart').getContext('2d');

    // Gradient fill
    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0,   'rgba(245, 158, 11, 0.3)');
    gradient.addColorStop(1,   'rgba(245, 158, 11, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                data:            chartValues,
                borderColor:     '#F59E0B',
                backgroundColor: gradient,
                borderWidth:     2,
                pointRadius:     0,
                pointHoverRadius:5,
                pointHoverBackgroundColor: '#F59E0B',
                fill:            true,
                tension:         0.4,
            }]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1A1A1A',
                    borderColor:     '#2A2A2A',
                    borderWidth:     1,
                    titleColor:      '#6B6B6B',
                    bodyColor:       '#F5F0E8',
                    bodyFont:        { family: 'DM Sans', weight: '600', size: 14 },
                    callbacks: {
                        label: ctx => 'Rp ' + Number(ctx.raw).toLocaleString('id-ID'),
                    }
                },
            },
            scales: {
                x: {
                    grid:  { display: false },
                    ticks: {
                        color:     '#6B6B6B',
                        font:      { size: 10, family: 'DM Sans' },
                        maxTicksLimit: 7,
                    },
                    border: { display: false },
                },
                y: {
                    grid:  { color: '#1F1F1F' },
                    ticks: {
                        color: '#6B6B6B',
                        font:  { size: 10, family: 'DM Sans' },
                        callback: v => 'Rp ' + (v >= 1000000
                            ? (v/1000000).toFixed(1) + 'jt'
                            : (v/1000).toFixed(0) + 'rb'),
                    },
                    border: { display: false },
                }
            }
        }
    });
});
</script>

@endsection