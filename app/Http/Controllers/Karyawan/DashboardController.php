<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\KpiScore;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $employee = $user->employee ?? null;

        if (!$employee) {
            return view('karyawan.no-employee');
        }

        $today = Carbon::today();
        $month = Carbon::now()->month;
        $year  = Carbon::now()->year;

        // Absensi hari ini
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        // Ringkasan bulan ini
        $monthlyStats = [
            'present' => Attendance::where('employee_id', $employee->id)
                ->whereYear('date', $year)->whereMonth('date', $month)
                ->whereIn('status', ['present', 'late'])->count(),
            'absent'  => Attendance::where('employee_id', $employee->id)
                ->whereYear('date', $year)->whereMonth('date', $month)
                ->where('status', 'absent')->count(),
            'late'    => Attendance::where('employee_id', $employee->id)
                ->whereYear('date', $year)->whereMonth('date', $month)
                ->where('status', 'late')->count(),
        ];

        // KPI bulan ini
        $kpi = KpiScore::where('employee_id', $employee->id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        // 5 absensi terakhir
        $recentAttendances = Attendance::where('employee_id', $employee->id)
            ->orderByDesc('date')
            ->take(5)
            ->get();

        return view('karyawan.dashboard', compact(
            'employee', 'todayAttendance', 'monthlyStats', 'kpi', 'recentAttendances'
        ));
    }
}
