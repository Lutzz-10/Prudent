<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan {{ $monthName }} — Prudent</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #fff;
            color: #1a1a1a;
            font-size: 13px;
            line-height: 1.5;
        }
        .header {
            background: #0F0F0F;
            color: #F5F0E8;
            padding: 28px 32px;
            margin-bottom: 24px;
        }
        .header h1 { font-size: 28px; margin-bottom: 4px; }
        .header p  { font-size: 13px; color: #6B6B6B; }
        .header .amount {
            font-size: 36px;
            color: #F59E0B;
            margin-top: 12px;
            font-weight: 700;
        }
        .section { padding: 0 32px; margin-bottom: 24px; }
        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6B6B6B;
            margin-bottom: 12px;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 6px;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }
        .stat-card {
            display: table-cell;
            width: 33%;
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 14px;
            text-align: center;
        }
        .stat-card .label { font-size: 11px; color: #6B6B6B; margin-bottom: 4px; }
        .stat-card .value { font-size: 16px; font-weight: 700; color: #1a1a1a; }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6B6B6B;
            padding: 8px 12px;
            background: #f5f5f5;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 13px;
        }
        tr:last-child td { border-bottom: none; }
        .progress-bar-wrap {
            background: #e5e5e5;
            border-radius: 4px;
            height: 6px;
            overflow: hidden;
            margin-top: 4px;
        }
        .progress-bar { height: 100%; border-radius: 4px; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            color: #aaa;
            padding: 24px 32px;
            border-top: 1px solid #e5e5e5;
            margin-top: 16px;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="color:#F59E0B; font-size:13px; margin-bottom:4px;">🧾 PRUDENT</p>
                <h1>Laporan Keuangan</h1>
                <p>{{ $monthName }} · {{ $user->name }}</p>
            </div>
            <div style="text-align:right;">
                <p style="color:#6B6B6B; font-size:11px;">Dicetak pada</p>
                <p style="font-size:12px;">{{ now()->translatedFormat('d F Y') }}</p>
            </div>
        </div>
        <div class="amount">Rp {{ number_format($total, 0, ',', '.') }}</div>
        @if($changePercent != 0)
        <p style="font-size:12px; color:{{ $changePercent > 0 ? '#FCA5A5' : '#6EE7B7' }}; margin-top:4px;">
            {{ $changePercent > 0 ? '↑' : '↓' }} {{ abs($changePercent) }}% dari bulan lalu
            (Rp {{ number_format($totalPrev, 0, ',', '.') }})
        </p>
        @endif
    </div>

    {{-- Statistik --}}
    <div class="section">
        <p class="section-title">Ringkasan</p>
        <table>
            <tr>
                <td style="padding:10px 0; color:#6B6B6B;">Rata-rata per hari</td>
                <td style="text-align:right; font-weight:600;">
                    Rp {{ number_format($avgPerDay, 0, ',', '.') }}
                </td>
            </tr>
            @if($highestDay)
            <tr>
                <td style="padding:10px 0; color:#6B6B6B;">Hari pengeluaran tertinggi</td>
                <td style="text-align:right; font-weight:600;">
                    {{ \Carbon\Carbon::parse($highestDay->transaction_date)->translatedFormat('d F Y') }}
                    (Rp {{ number_format($highestDay->total, 0, ',', '.') }})
                </td>
            </tr>
            @endif
            <tr>
                <td style="padding:10px 0; color:#6B6B6B;">Jumlah hari aktif belanja</td>
                <td style="text-align:right; font-weight:600;">{{ $daysInMonth }} hari</td>
            </tr>
        </table>
    </div>

    {{-- Per Kategori --}}
    @if($byCategory->count() > 0)
    <div class="section">
        <p class="section-title">Pengeluaran per Kategori</p>
        <table>
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th style="text-align:center;">Transaksi</th>
                    <th style="text-align:right;">Total</th>
                    <th style="text-align:right;">%</th>
                </tr>
            </thead>
            <tbody>
                @foreach($byCategory as $cat)
                <tr>
                    <td>{{ $cat['icon'] }} {{ $cat['name'] }}</td>
                    <td style="text-align:center; color:#6B6B6B;">{{ $cat['count'] }}x</td>
                    <td style="text-align:right; font-weight:600;">
                        Rp {{ number_format($cat['total'], 0, ',', '.') }}
                    </td>
                    <td style="text-align:right; color:#F59E0B; font-weight:600;">
                        {{ $cat['percentage'] }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Transaksi Terbesar --}}
    @if($topTransactions->count() > 0)
    <div class="section">
        <p class="section-title">5 Transaksi Terbesar</p>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Toko / Keterangan</th>
                    <th>Tanggal</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topTransactions as $i => $trx)
                <tr>
                    <td style="color:#F59E0B; font-weight:700;">{{ $i + 1 }}</td>
                    <td>{{ $trx->receipt->store_name ?? ($trx->category->name ?? 'Transaksi') }}</td>
                    <td style="color:#6B6B6B;">
                        {{ $trx->transaction_date->translatedFormat('d F Y') }}
                    </td>
                    <td style="text-align:right; font-weight:600;">
                        Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Budget --}}
    @if($budgets->count() > 0)
    <div class="section">
        <p class="section-title">Status Budget</p>
        <table>
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th style="text-align:right;">Budget</th>
                    <th style="text-align:right;">Terpakai</th>
                    <th style="text-align:right;">Sisa</th>
                    <th style="text-align:center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budgets as $budget)
                @php $pct = $budget->percentage; @endphp
                <tr>
                    <td>{{ $budget->category->icon ?? '' }} {{ $budget->category->name }}</td>
                    <td style="text-align:right;">
                        Rp {{ number_format($budget->amount, 0, ',', '.') }}
                    </td>
                    <td style="text-align:right;">
                        Rp {{ number_format($budget->spent, 0, ',', '.') }}
                    </td>
                    <td style="text-align:right;">
                        Rp {{ number_format(max(0, $budget->amount - $budget->spent), 0, ',', '.') }}
                    </td>
                    <td style="text-align:center;">
                        <span style="font-size:11px; font-weight:600;
                                     color:{{ $pct >= 100 ? '#EF4444' : ($pct >= 80 ? '#F59E0B' : '#10B981') }}">
                            {{ round($pct) }}%
                            @if($pct >= 100) OVER @elseif($pct >= 80) WASPADA @else AMAN @endif
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Laporan dibuat otomatis oleh Prudent · {{ now()->translatedFormat('d F Y, H:i') }} WIB
    </div>

</body>
</html>