<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date      = $request->get('date', Carbon::today()->toDateString());
        $employees = Employee::where('status', 'active')->get();

        $attendances = Attendance::with('employee')
            ->whereDate('date', $date)
            ->latest()
            ->paginate(15);

        return view('hrd.attendances.index', compact('attendances', 'date', 'employees'));
    }

    public function show(Employee $employee, Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year  = $request->get('year', Carbon::now()->year);

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();

        $summary = [
            'present' => $attendances->whereIn('status', ['present', 'late'])->count(),
            'absent'  => $attendances->where('status', 'absent')->count(),
            'late'    => $attendances->where('status', 'late')->count(),
        ];

        return view('hrd.attendances.show', compact('employee', 'attendances', 'summary', 'month', 'year'));
    }
}
