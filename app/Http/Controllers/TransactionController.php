<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Transaction::with(['category', 'receipt'])
            ->where('user_id', $user->id);

        // Filter bulan
        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->whereMonth('transaction_date', $month)
                  ->whereYear('transaction_date', $year);
        } else {
            $query->whereMonth('transaction_date', now()->month)
                  ->whereYear('transaction_date', now()->year);
        }

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%$search%")
                  ->orWhereHas('receipt', fn($r) =>
                      $r->where('store_name', 'like', "%$search%")
                  );
            });
        }

        $transactions = $query->orderByDesc('transaction_date')
                              ->orderByDesc('created_at')
                              ->paginate(15)
                              ->withQueryString();

        // Total filtered
        $totalFiltered = $query->sum('total_amount');

        $categories = Category::all();

        return view('transactions.index', compact(
            'transactions', 'categories', 'totalFiltered'
        ));
    }

    public function show(Transaction $transaction)
    {
        abort_if($transaction->user_id !== Auth::id(), 403);

        $transaction->load(['category', 'receipt', 'items']);

        return view('transactions.show', compact('transaction'));
    }

    public function destroy(Transaction $transaction)
    {
        abort_if($transaction->user_id !== Auth::id(), 403);

        // Hapus receipt & foto juga
        if ($transaction->receipt) {
            \Storage::disk('public')->delete($transaction->receipt->image_path);
            $transaction->receipt->delete();
        }

        $transaction->delete();

        return redirect()->route('transaction.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}