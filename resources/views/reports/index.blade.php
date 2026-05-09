@extends('layouts.app')
@section('title', 'Laporan — Prudent')

@section('content')

<script>
    const weekLabels = @json($weekLabels);
    const weekValues = @json($weekValues);
    const catLabels  = @json($catLabels);
    const catValues  = @json($catValues);
    const catColors  = @json($catColors);
</script>

<div class="pb-32">

    {{-- Header --}}
    <div class="px-5 pt-10 pb-4">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <h1 class="font-display text-2xl mb-1">Laporan</h1>
                <p style="color:var(--text-muted); font-size:13px;">
                    Analisis keuangan bulananmu.
                </p>
            </div>
            {{-- Export PDF --}}
            <a href="{{ route('report.pdf', ['month'=>$month, 'year'=>$year]) }}"
               style="display:flex; align-items:center; gap:6px; padding:10px 14px;
                      background:var(--amber); color:#0F0F0F; border-radius:12px;
                      text-decoration:none; font-size:13px; font-weight:600;
                      white-space:nowrap;">
                📥 PDF
            </a>
        </div>
    </div>

    {{-- Navigasi Bulan --}}
    <div class="px-5 mb-6">
        @php
            $prev = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
            $next = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
        @endphp
        <div style="display:flex; gap:8px; align-items:center;">
            <a href="{{ route('report.index', ['month'=>$prev->month, 'year'=>$prev->year]) }}"
               style="padding:10px 14px; border-radius:12px; background:var(--bg-card);
                      border:1px solid var(--border); text-decoration:none; font-size:14px;">‹</a>
            <div style="flex:1; text-align:center; padding:10px;
                        background:var(--bg-card); border:1px solid var(--border);
                        border-radius:12px; font-weight:600; font-size:14px;">
                {{ $monthName }}
            </div>
            <a href="{{ route('report.index', ['month'=>$next->month, 'year'=>$next->year]) }}"
               style="padding:10px 14px; border-radius:12px; background:var(--bg-card);
                      border:1px solid var(--border); text-decoration:none; font-size:14px;">›</a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="px-5 mb-6">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">

            {{-- Total --}}
            <div style="background:var(--bg-card); border:1px solid var(--border);
                        border-radius:20px; padding:16px; grid-column:span 2;">
                <p style="font-size:12px; color:var(--text-muted); margin-bottom:4px;
                          letter-spacing:0.06em; text-transform:uppercase;">Total Pengeluaran</p>
                <p class="font-display" style="font-size:34px; color:var(--amber);">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </p>
                <div style="margin-top:6px;">
                    @if($changePercent > 0)
                    <span style="font-size:12px; background:rgba(239,68,68,0.15);
                                 color:#FCA5A5; padding:3px 10px; border-radius:20px;">
                        ↑ {{ abs($changePercent) }}% dari bulan lalu
                    </span>
                    @elseif($changePercent < 0)
                    <span style="font-size:12px; background:rgba(16,185,129,0.15);
                                 color:#6EE7B7; padding:3px 10px; border-radius:20px;">
                        ↓ {{ abs($changePercent) }}% dari bulan lalu
                    </span>
                    @else
                    <span style="font-size:12px; background:var(--bg-input);
                                 color:var(--text-muted); padding:3px 10px; border-radius:20px;">
                        Sama seperti bulan lalu
                    </span>
                    @endif
                </div>
            </div>

            {{-- Rata-rata per hari --}}
            <div style="background:var(--bg-card); border:1px solid var(--border);
                        border-radius:20px; padding:16px;">
                <p style="font-size:11px; color:var(--text-muted); margin-bottom:6px;
                          text-transform:uppercase; letter-spacing:0.06em;">Rata-rata/Hari</p>
                <p class="font-display" style="font-size:18px;">
                    Rp {{ number_format($avgPerDay, 0, ',', '.') }}
                </p>
            </div>

            {{-- Hari tertinggi --}}
            <div style="background:var(--bg-card); border:1px solid var(--border);
                        border-radius:20px; padding:16px;">
                <p style="font-size:11px; color:var(--text-muted); margin-bottom:6px;
                          text-transform:uppercase; letter-spacing:0.06em;">Hari Terboros</p>
                @if($highestDay)
                <p class="font-display" style="font-size:18px;">
                    {{ \Carbon\Carbon::parse($highestDay->transaction_date)->translatedFormat('d F') }}
                </p>
                <p style="font-size:11px; color:var(--text-muted);">
                    Rp {{ number_format($highestDay->total, 0, ',', '.') }}
                </p>
                @else
                <p style="font-size:14px; color:var(--text-muted);">—</p>
                @endif
            </div>

        </div>
    </div>

    {{-- Chart Mingguan --}}
    @if(count($weekValues) > 0)
    <div class="px-5 mb-6">
        <div style="background:var(--bg-card); border:1px solid var(--border);
                    border-radius:24px; padding:20px;">
            <p style="font-size:14px; font-weight:600; margin-bottom:16px;">
                Pengeluaran per Minggu
            </p>
            <canvas id="weekChart" height="140"></canvas>
        </div>
    </div>
    @endif

    {{-- Chart Kategori (Donut) --}}
    @if(count($catValues) > 0)
    <div class="px-5 mb-6">
        <div style="background:var(--bg-card); border:1px solid var(--border);
                    border-radius:24px; padding:20px;">
            <p style="font-size:14px; font-weight:600; margin-bottom:16px;">
                Komposisi Kategori
            </p>
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="flex-shrink:0; width:140px; height:140px;">
                    <canvas id="donutChart"></canvas>
                </div>
                <div style="flex:1; min-width:0;">
                    @foreach($byCategory as $cat)
                    <div style="display:flex; justify-content:space-between;
                                align-items:center; margin-bottom:8px;">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <div style="width:8px; height:8px; border-radius:50%;
                                        background:{{ $cat['color'] }}; flex-shrink:0;"></div>
                            <span style="font-size:12px; color:var(--text-muted);">
                                {{ Str::limit($cat['name'], 14) }}
                            </span>
                        </div>
                        <span style="font-size:12px; font-weight:600;">
                            {{ $cat['percentage'] }}%
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Per Kategori Detail --}}
    @if($byCategory->count() > 0)
    <div class="px-5 mb-6">
        <p style="font-size:14px; font-weight:600; margin-bottom:12px;">Detail per Kategori</p>
        <div class="space-y-3">
            @foreach($byCategory as $cat)
            <div style="background:var(--bg-card); border:1px solid var(--border);
                        border-radius:16px; padding:14px 16px;">
                <div style="display:flex; justify-content:space-between;
                            align-items:center; margin-bottom:8px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:20px;">{{ $cat['icon'] }}</span>
                        <div>
                            <p style="font-size:14px; font-weight:500;">{{ $cat['name'] }}</p>
                            <p style="font-size:12px; color:var(--text-muted);">
                                {{ $cat['count'] }} transaksi
                            </p>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <p style="font-size:14px; font-weight:600;">
                            Rp {{ number_format($cat['total'], 0, ',', '.') }}
                        </p>
                        <p style="font-size:12px; color:var(--text-muted);">
                            {{ $cat['percentage'] }}%
                        </p>
                    </div>
                </div>
                <div style="height:4px; background:var(--border); border-radius:4px; overflow:hidden;">
                    <div style="height:100%; border-radius:4px;
                                background:{{ $cat['color'] }};
                                width:{{ $cat['percentage'] }}%;
                                transition:width 0.8s ease;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Top 5 Transaksi Terbesar --}}
    @if($topTransactions->count() > 0)
    <div class="px-5 mb-6">
        <p style="font-size:14px; font-weight:600; margin-bottom:12px;">
            Transaksi Terbesar
        </p>
        <div class="space-y-2">
            @foreach($topTransactions as $i => $trx)
            <a href="{{ route('transaction.show', $trx) }}"
               style="text-decoration:none; display:flex; align-items:center; gap:12px;
                      background:var(--bg-card); border:1px solid var(--border);
                      border-radius:16px; padding:14px 16px;">
                <div style="width:28px; height:28px; border-radius:8px; flex-shrink:0;
                            background:{{ $i === 0 ? 'var(--amber)' : 'var(--bg-input)' }};
                            display:flex; align-items:center; justify-content:center;
                            font-size:13px; font-weight:700;
                            color:{{ $i === 0 ? '#0F0F0F' : 'var(--text-muted)' }};">
                    {{ $i + 1 }}
                </div>
                <div style="flex:1; min-width:0;">
                    <p style="font-size:14px; font-weight:500; white-space:nowrap;
                              overflow:hidden; text-overflow:ellipsis;">
                        {{ $trx->receipt->store_name ?? ($trx->category->name ?? 'Transaksi') }}
                    </p>
                    <p style="font-size:12px; color:var(--text-muted);">
                        {{ $trx->transaction_date->translatedFormat('d F') }}
                        · {{ $trx->category->icon ?? '' }} {{ $trx->category->name ?? '' }}
                    </p>
                </div>
                <p style="font-size:14px; font-weight:600; flex-shrink:0;">
                    Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                </p>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Budget Status --}}
    @if($budgets->count() > 0)
    <div class="px-5">
        <p style="font-size:14px; font-weight:600; margin-bottom:12px;">Status Budget</p>
        <div class="space-y-2">
            @foreach($budgets as $budget)
            @php
                $pct   = $budget->percentage;
                $color = $pct >= 100 ? '#EF4444' : ($pct >= 80 ? '#F59E0B' : '#10B981');
            @endphp
            <div style="background:var(--bg-card); border:1px solid var(--border);
                        border-radius:16px; padding:14px 16px;">
                <div style="display:flex; justify-content:space-between;
                            align-items:center; margin-bottom:8px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span>{{ $budget->category->icon ?? '📦' }}</span>
                        <span style="font-size:14px;">{{ $budget->category->name }}</span>
                    </div>
                    <span style="font-size:13px; color:{{ $color }}; font-weight:600;">
                        {{ round($pct) }}%
                        @if($pct >= 100) 🚨 @elseif($pct >= 80) ⚠️ @endif
                    </span>
                </div>
                <div style="height:5px; background:var(--border); border-radius:5px; overflow:hidden;">
                    <div style="height:100%; border-radius:5px; background:{{ $color }};
                                width:{{ min(100, $pct) }}%;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Bar chart mingguan
    @if(count($weekValues) > 0)
    new Chart(document.getElementById('weekChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: weekLabels,
            datasets: [{
                data:            weekValues,
                backgroundColor: 'rgba(245,158,11,0.7)',
                borderColor:     '#F59E0B',
                borderWidth:     0,
                borderRadius:    8,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1A1A1A',
                    borderColor:     '#2A2A2A',
                    borderWidth:     1,
                    bodyColor:       '#F5F0E8',
                    callbacks: {
                        label: ctx => 'Rp ' + Number(ctx.raw).toLocaleString('id-ID'),
                    }
                }
            },
            scales: {
                x: {
                    grid:   { display: false },
                    ticks:  { color: '#6B6B6B', font: { size: 11 } },
                    border: { display: false },
                },
                y: {
                    grid:   { color: '#1F1F1F' },
                    ticks:  {
                        color: '#6B6B6B', font: { size: 10 },
                        callback: v => 'Rp ' + (v >= 1000000
                            ? (v/1000000).toFixed(1) + 'jt'
                            : (v/1000).toFixed(0) + 'rb'),
                    },
                    border: { display: false },
                }
            }
        }
    });
    @endif

    // Donut chart kategori
    @if(count($catValues) > 0)
    new Chart(document.getElementById('donutChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels:   catLabels,
            datasets: [{
                data:            catValues,
                backgroundColor: catColors,
                borderWidth:     0,
                hoverOffset:     4,
            }]
        },
        options: {
            responsive:  true,
            cutout:      '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1A1A1A',
                    borderColor:     '#2A2A2A',
                    borderWidth:     1,
                    bodyColor:       '#F5F0E8',
                    callbacks: {
                        label: ctx => ' Rp ' + Number(ctx.raw).toLocaleString('id-ID'),
                    }
                }
            }
        }
    });
    @endif
});
</script>

@endsection