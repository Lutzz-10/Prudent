<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $month = $request->input('month', now()->month);
        $year  = $request->input('year',  now()->year);

        $budgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->where('month', $month)
            ->where('year',  $year)
            ->get()
            ->map(function ($budget) {
                $budget->spent      = $budget->spent;
                $budget->percentage = $budget->percentage;
                return $budget;
            });

        // Total budget & total spent bulan ini
        $totalBudget = $budgets->sum('amount');
        $totalSpent  = $budgets->sum('spent');
        $totalPct    = $totalBudget > 0
            ? min(100, round(($totalSpent / $totalBudget) * 100, 1))
            : 0;

        // Kategori yang belum punya budget bulan ini
        $usedCategoryIds    = $budgets->pluck('category_id')->toArray();
        $availableCategories = Category::whereNotIn('id', $usedCategoryIds)->get();

        // Pengeluaran per kategori bulan ini (tanpa budget)
        $unbudgetedSpending = Transaction::with('category')
            ->where('user_id', $user->id)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date',  $year)
            ->whereNotIn('category_id', $usedCategoryIds)
            ->selectRaw('category_id, SUM(total_amount) as total')
            ->groupBy('category_id')
            ->get();

        return view('budget.index', compact(
            'budgets',
            'totalBudget',
            'totalSpent',
            'totalPct',
            'availableCategories',
            'unbudgetedSpending',
            'month',
            'year',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:1000',
            'month'       => 'required|integer|between:1,12',
            'year'        => 'required|integer|min:2020',
        ], [
            'amount.min' => 'Budget minimal Rp 1.000.',
        ]);

        // Cek duplikat
        $exists = Budget::where('user_id',     Auth::id())
            ->where('category_id', $request->category_id)
            ->where('month',       $request->month)
            ->where('year',        $request->year)
            ->exists();

        if ($exists) {
            return back()->withErrors(['category_id' => 'Budget kategori ini sudah ada untuk bulan tersebut.']);
        }

        Budget::create([
            'user_id'     => Auth::id(),
            'category_id' => $request->category_id,
            'amount'      => $request->amount,
            'month'       => $request->month,
            'year'        => $request->year,
        ]);

        return back()->with('success', 'Budget berhasil ditambahkan! 🎯');
    }

    public function update(Request $request, Budget $budget)
    {
        abort_if($budget->user_id !== Auth::id(), 403);

        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $budget->update(['amount' => $request->amount]);

        return back()->with('success', 'Budget berhasil diperbarui.');
    }

    public function destroy(Budget $budget)
    {
        abort_if($budget->user_id !== Auth::id(), 403);
        $budget->delete();

        return back()->with('success', 'Budget berhasil dihapus.');
    }
}