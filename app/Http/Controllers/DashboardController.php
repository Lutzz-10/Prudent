<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $now   = Carbon::now();
        $month = $now->month;
        $year  = $now->year;

        // Total pengeluaran bulan ini
        $totalThisMonth = Transaction::where('user_id', $user->id)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('total_amount');

        // Total bulan lalu (untuk perbandingan)
        $lastMonth = $now->copy()->subMonth();
        $totalLastMonth = Transaction::where('user_id', $user->id)
            ->whereMonth('transaction_date', $lastMonth->month)
            ->whereYear('transaction_date', $lastMonth->year)
            ->sum('total_amount');

        // Hitung persentase perubahan
        $changePercent = 0;
        if ($totalLastMonth > 0) {
            $changePercent = round((($totalThisMonth - $totalLastMonth) / $totalLastMonth) * 100, 1);
        }

        // 5 transaksi terbaru
        $recentTransactions = Transaction::with(['category', 'receipt'])
            ->where('user_id', $user->id)
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Data chart — pengeluaran per hari (30 hari terakhir)
        $dailyData = Transaction::where('user_id', $user->id)
            ->whereBetween('transaction_date', [
                $now->copy()->subDays(29)->toDateString(),
                $now->toDateString(),
            ])
            ->selectRaw('DATE(transaction_date) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Isi hari yang kosong dengan 0
        $chartLabels = [];
        $chartValues = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->toDateString();
            $chartLabels[] = Carbon::parse($date)->format('d/m');
            $chartValues[] = $dailyData[$date] ?? 0;
        }

        // Pengeluaran per kategori bulan ini (untuk pie chart)
        $categoryData = Transaction::with('category')
            ->where('user_id', $user->id)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->selectRaw('category_id, SUM(total_amount) as total')
            ->groupBy('category_id')
            ->get()
            ->map(fn($t) => [
                'name'  => $t->category->name  ?? 'Lainnya',
                'icon'  => $t->category->icon  ?? '📦',
                'color' => $t->category->color ?? '#A1A1AA',
                'total' => $t->total,
            ]);

        // Budget bulan ini
        $budgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        return view('dashboard.index', compact(
            'user',
            'totalThisMonth',
            'totalLastMonth',
            'changePercent',
            'recentTransactions',
            'chartLabels',
            'chartValues',
            'categoryData',
            'budgets',
            'month',
            'year',
        ));
    }
}