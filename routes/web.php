<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HRD\DashboardController as HRDDashboard;
use App\Http\Controllers\HRD\EmployeeController;
use App\Http\Controllers\HRD\RfidCardController;
use App\Http\Controllers\HRD\AttendanceController;
use App\Http\Controllers\HRD\KpiController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboard;
use App\Http\Controllers\Karyawan\AttendanceController as KaryawanAttendance;
use App\Http\Controllers\Karyawan\KpiController as KaryawanKpi;
use App\Http\Controllers\Karyawan\ProfileController as KaryawanProfile;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('hrd')) {
            return redirect()->route('hrd.dashboard');
        }
        return redirect()->route('karyawan.dashboard');
    })->name('dashboard');

    // ── Route HRD ──────────────────────────────────────────
    Route::middleware('role:hrd')->prefix('hrd')->name('hrd.')->group(function () {
        Route::get('/dashboard', [HRDDashboard::class, 'index'])->name('dashboard');

        // Kelola karyawan
        Route::resource('employees', EmployeeController::class);

        // Kelola kartu RFID
        Route::resource('rfid-cards', RfidCardController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::post('rfid-cards/{id}/assign', [RfidCardController::class, 'assign'])->name('rfid-cards.assign');

        // Monitor absensi
        Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
        Route::get('attendances/{employee}', [AttendanceController::class, 'show'])->name('attendances.show');

        // KPI
        Route::get('kpi', [KpiController::class, 'index'])->name('kpi.index');
        Route::get('kpi/{employee}', [KpiController::class, 'show'])->name('kpi.show');
        Route::post('kpi/thresholds', [KpiController::class, 'updateThreshold'])->name('kpi.threshold.update');
    });

    // ── Route Karyawan ─────────────────────────────────────
    Route::middleware('role:karyawan')->prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/dashboard',   [KaryawanDashboard::class,  'index'])->name('dashboard');
        Route::get('/attendance',  [KaryawanAttendance::class, 'index'])->name('attendance');
        Route::get('/kpi',         [KaryawanKpi::class,        'index'])->name('kpi');
        Route::get('/profile',     [KaryawanProfile::class,    'index'])->name('profile');
        Route::put('/profile',     [KaryawanProfile::class,    'update'])->name('profile.update');
    });
});

require __DIR__ . '/auth.php';
