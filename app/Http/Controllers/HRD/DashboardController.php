<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\KpiScore;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $month = Carbon::now()->month;
        $year  = Carbon::now()->year;

        $stats = [
            'total_employees'  => Employee::where('status', 'active')->count(),
            'present_today'    => Attendance::whereDate('date', $today)
                ->whereIn('status', ['present', 'late'])->count(),
            'absent_today'     => Attendance::whereDate('date', $today)
                ->where('status', 'absent')->count(),
            'late_today'       => Attendance::whereDate('date', $today)
                ->where('status', 'late')->count(),
            'kpi_invalid'      => KpiScore::where('year', $year)
                ->where('month', $month)
                ->where('status', 'invalid')->count(),
        ];

        $recentAttendances = Attendance::with('employee')
            ->whereDate('date', $today)
            ->latest()
            ->take(10)
            ->get();

        return view('hrd.dashboard', compact('stats', 'recentAttendances'));
    }
}
