<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function index(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year',  now()->year);

        $data  = $this->reportService->getMonthlyReport($month, $year, Auth::id());
        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        // Data chart mingguan untuk JS
        $weekLabels  = $data['byWeek']->pluck('week')->map(fn($w) => 'Minggu ' . $w)->toArray();
        $weekValues  = $data['byWeek']->pluck('total')->toArray();

        // Data chart kategori untuk JS
        $catLabels   = $data['byCategory']->pluck('name')->toArray();
        $catValues   = $data['byCategory']->pluck('total')->toArray();
        $catColors   = $data['byCategory']->pluck('color')->toArray();

        return view('reports.index', array_merge($data, compact(
            'month', 'year', 'monthName',
            'weekLabels', 'weekValues',
            'catLabels', 'catValues', 'catColors'
        )));
    }

    public function exportPdf(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year',  now()->year);

        $data      = $this->reportService->getMonthlyReport($month, $year, Auth::id());
        $user      = Auth::user();
        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        $pdf = Pdf::loadView('reports.pdf', array_merge($data, compact(
            'user', 'month', 'year', 'monthName'
        )))->setPaper('a4', 'portrait');

        $filename = 'prudent-laporan-' . strtolower(str_replace(' ', '-', $monthName)) . '.pdf';

        return $pdf->download($filename);
    }
}