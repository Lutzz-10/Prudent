<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportService
{
    public function getMonthlyReport(int $month, int $year, int $userId): array
    {
        // Total pengeluaran bulan ini
        $total = Transaction::where('user_id', $userId)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('total_amount');

        // Per kategori
        $byCategory = Transaction::with('category')
            ->where('user_id', $userId)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->selectRaw('category_id, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('category_id')
            ->get()
            ->map(fn($t) => [
                'name'       => $t->category->name  ?? 'Lainnya',
                'icon'       => $t->category->icon  ?? '📦',
                'color'      => $t->category->color ?? '#A1A1AA',
                'total'      => $t->total,
                'count'      => $t->count,
                'percentage' => $total > 0 ? round(($t->total / $total) * 100, 1) : 0,
            ])
            ->sortByDesc('total')
            ->values();

        // Per minggu
        $byWeek = Transaction::where('user_id', $userId)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->selectRaw('WEEK(transaction_date, 1) as week, SUM(total_amount) as total')
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        // Transaksi terbesar bulan ini
        $topTransactions = Transaction::with(['category', 'receipt'])
            ->where('user_id', $userId)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        // Rata-rata per hari
        $daysInMonth  = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $avgPerDay    = $total / $daysInMonth;

        // Hari dengan pengeluaran tertinggi
        $highestDay = Transaction::where('user_id', $userId)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->selectRaw('transaction_date, SUM(total_amount) as total')
            ->groupBy('transaction_date')
            ->orderByDesc('total')
            ->first();

        // Budget summary
        $budgets = Budget::with('category')
            ->where('user_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->map(function ($b) {
                $b->spent      = $b->spent;
                $b->percentage = $b->percentage;
                return $b;
            });

        // Perbandingan bulan lalu
        $prevMonth = Carbon::createFromDate($year, $month, 1)->subMonth();
        $totalPrev = Transaction::where('user_id', $userId)
            ->whereMonth('transaction_date', $prevMonth->month)
            ->whereYear('transaction_date',  $prevMonth->year)
            ->sum('total_amount');

        $changePercent = $totalPrev > 0
            ? round((($total - $totalPrev) / $totalPrev) * 100, 1)
            : 0;

        return compact(
            'total', 'byCategory', 'byWeek',
            'topTransactions', 'avgPerDay', 'highestDay',
            'budgets', 'totalPrev', 'changePercent',
            'daysInMonth'
        );
    }
}