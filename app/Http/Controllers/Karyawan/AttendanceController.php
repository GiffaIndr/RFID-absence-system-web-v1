<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return redirect()->route('karyawan.dashboard');
        }

        $month = $request->get('month', Carbon::now()->month);
        $year  = $request->get('year',  Carbon::now()->year);

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();

        $summary = [
            'present'       => $attendances->whereIn('status', ['present', 'late'])->count(),
            'absent'        => $attendances->where('status', 'absent')->count(),
            'late'          => $attendances->where('status', 'late')->count(),
            'total_minutes' => $attendances->sum('work_duration'),
        ];

        return view('karyawan.attendance', compact('attendances', 'summary', 'month', 'year'));
    }
}
