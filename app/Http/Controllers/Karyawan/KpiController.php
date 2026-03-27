<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\KpiScore;
use App\Services\KpiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function __construct(private KpiService $kpiService) {}

    public function index(Request $request)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return redirect()->route('karyawan.dashboard');
        }

        $year = $request->get('year', Carbon::now()->year);

        // Recalculate KPI bulan ini
        $currentKpi = $this->kpiService->calculate($employee->id);

        // Riwayat KPI sepanjang tahun
        $kpiHistory = KpiScore::where('employee_id', $employee->id)
            ->where('year', $year)
            ->orderBy('month')
            ->get();

        return view('karyawan.kpi', compact('currentKpi', 'kpiHistory', 'year'));
    }
}
