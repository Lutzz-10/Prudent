<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;

// Redirect root ke login
Route::get('/', fn() => redirect()->route('login'));

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login',     [LoginController::class,    'showForm'])->name('login');
    Route::post('/login',    [LoginController::class,    'login']);
    Route::get('/register',  [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::post('/logout',   [LogoutController::class,   'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Receipt / Scan
    Route::get('/scan',                 [ReceiptController::class, 'showUpload'])->name('receipt.upload');
    Route::post('/scan',                [ReceiptController::class, 'process'])->name('receipt.process');
    Route::get('/scan/{receipt}',       [ReceiptController::class, 'showResult'])->name('receipt.result');
    Route::post('/scan/{receipt}/save', [ReceiptController::class, 'save'])->name('receipt.save');

    // Transaksi
    Route::get('/riwayat',                  [TransactionController::class, 'index'])->name('transaction.index');
    Route::get('/riwayat/{transaction}',    [TransactionController::class, 'show'])->name('transaction.show');
    Route::delete('/riwayat/{transaction}', [TransactionController::class, 'destroy'])->name('transaction.destroy');

    // Budget
    Route::get('/budget',             [BudgetController::class, 'index'])->name('budget.index');
    Route::post('/budget',            [BudgetController::class, 'store'])->name('budget.store');
    Route::put('/budget/{budget}',    [BudgetController::class, 'update'])->name('budget.update');
    Route::delete('/budget/{budget}', [BudgetController::class, 'destroy'])->name('budget.destroy');

    // Laporan
    Route::get('/laporan',            [ReportController::class, 'index'])->name('report.index');
    Route::get('/laporan/export-pdf', [ReportController::class, 'exportPdf'])->name('report.pdf');

    // Profil
    Route::get('/profil',          [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profil',          [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profil',       [ProfileController::class, 'destroy'])->name('profile.destroy');
});