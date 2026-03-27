<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user', 'rfidCards')
            ->latest()
            ->paginate(10);

        return view('hrd.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('hrd.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'employee_code'   => 'required|string|unique:employees,employee_code',
            'department'      => 'nullable|string|max:100',
            'position'        => 'nullable|string|max:100',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string',
            'join_date'       => 'nullable|date',
        ]);

        DB::transaction(function () use ($request) {
            // Buat akun user untuk karyawan
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make('password123'), // default password
            ]);
            $user->assignRole('karyawan');

            // Buat data employee
            Employee::create([
                'user_id'       => $user->id,
                'employee_code' => $request->employee_code,
                'name'          => $request->name,
                'department'    => $request->department,
                'position'      => $request->position,
                'phone'         => $request->phone,
                'address'       => $request->address,
                'join_date'     => $request->join_date,
            ]);
        });

        return redirect()->route('hrd.employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan. Password default: password123');
    }

    public function show(Employee $employee)
    {
        $employee->load('user', 'rfidCards', 'attendances', 'kpiScores');
        return view('hrd.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('hrd.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'employee_code' => 'required|string|unique:employees,employee_code,' . $employee->id,
            'department'    => 'nullable|string|max:100',
            'position'      => 'nullable|string|max:100',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'join_date'     => 'nullable|date',
            'status'        => 'required|in:active,inactive',
        ]);

        $employee->update($request->only([
            'name',
            'employee_code',
            'department',
            'position',
            'phone',
            'address',
            'join_date',
            'status',
        ]));

        return redirect()->route('hrd.employees.index')
            ->with('success', 'Data karyawan berhasil diupdate.');
    }

    public function destroy(Employee $employee)
    {
        $employee->user()->delete(); // cascade ke employee juga
        return redirect()->route('hrd.employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}
