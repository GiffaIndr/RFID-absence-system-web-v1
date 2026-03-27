<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\KpiScore;
use App\Models\KpiThreshold;
use App\Services\KpiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function __construct(private KpiService $kpiService) {}

    public function index(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year  = $request->get('year', Carbon::now()->year);

        $kpiScores = KpiScore::with('employee')
            ->where('year', $year)
            ->where('month', $month)
            ->orderBy('total_score', 'desc')
            ->paginate(15);

        $thresholds = KpiThreshold::where('is_active', true)->get();

        return view('hrd.kpi.index', compact('kpiScores', 'thresholds', 'month', 'year'));
    }

    public function show(Employee $employee, Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);

        $kpiScores = KpiScore::where('employee_id', $employee->id)
            ->where('year', $year)
            ->orderBy('month')
            ->get();

        // Recalculate KPI bulan ini
        $currentKpi = $this->kpiService->calculate($employee->id);

        return view('hrd.kpi.show', compact('employee', 'kpiScores', 'currentKpi', 'year'));
    }

    public function updateThreshold(Request $request)
    {
        $request->validate([
            'thresholds'               => 'required|array',
            'thresholds.*.id'          => 'required|exists:kpi_thresholds,id',
            'thresholds.*.min_value'   => 'required|numeric|min:0|max:100',
        ]);

        foreach ($request->thresholds as $data) {
            KpiThreshold::find($data['id'])->update([
                'min_value' => $data['min_value'],
            ]);
        }

        return redirect()->route('hrd.kpi.index')
            ->with('success', 'Threshold KPI berhasil diupdate.');
    }
}
