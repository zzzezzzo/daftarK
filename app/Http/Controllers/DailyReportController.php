<?php

namespace App\Http\Controllers;

use App\Models\CashBox;
use App\Services\DailyCashFlowReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function index(Request $request, DailyCashFlowReportService $reportService)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : Carbon::today();

        $cashBoxId = $request->filled('cash_box_id') ? (int) $request->cash_box_id : null;

        $report = $reportService->build($date, $cashBoxId);
        $cashBoxes = CashBox::orderBy('name')->get(['id', 'name']);

        return view('reports.daily', compact('report', 'date', 'cashBoxes', 'cashBoxId'));
    }
}
