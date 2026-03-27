@extends('layouts.hrd')
@section('title', 'Dashboard')

@section('content')

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small mb-1">Total Karyawan</div>
                    <div class="fs-2 fw-semibold text-dark">{{ $stats['total_employees'] }}</div>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small mb-1">Hadir Hari Ini</div>
                    <div class="fs-2 fw-semibold text-success">{{ $stats['present_today'] }}</div>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-person-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small mb-1">Terlambat</div>
                    <div class="fs-2 fw-semibold text-warning">{{ $stats['late_today'] }}</div>
                </div>
                <div class="icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small mb-1">KPI Tidak Valid</div>
                    <div class="fs-2 fw-semibold text-danger">{{ $stats['kpi_invalid'] }}</div>
                </div>
                <div class="icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Absensi -->
<div class="card table-card">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 px-4">
        <div>
            <h6 class="mb-0 fw-semibold">Absensi Hari Ini</h6>
            <small class="text-muted">{{ now()->format('l, d F Y') }}</small>
        </div>
        <a href="{{ route('hrd.attendances.index') }}" class="btn btn-sm btn-outline-primary">
            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Departemen</th>
                    <th>Tap In</th>
                    <th>Tap Out</th>
                    <th>Durasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAttendances as $att)
                <tr>
                    <td>
                        <div class="fw-medium">{{ $att->employee->name }}</div>
                        <small class="text-muted">{{ $att->employee->employee_code }}</small>
                    </td>
                    <td class="text-muted">{{ $att->employee->department ?? '-' }}</td>
                    <td>{{ $att->tap_in?->format('H:i') ?? '-' }}</td>
                    <td>{{ $att->tap_out?->format('H:i') ?? '-' }}</td>
                    <td class="text-muted">
                        {{ $att->work_duration ? floor($att->work_duration/60).'j '.($att->work_duration%60).'m' : '-' }}
                    </td>
                    <td>
                        @php
                            $cls = match($att->status) {
                                'present' => 'badge-present',
                                'late'    => 'badge-late',
                                'absent'  => 'badge-absent',
                                default   => 'badge-blocked',
                            };
                        @endphp
                        <span class="badge rounded-pill px-3 py-2 {{ $cls }}">
                            {{ ucfirst($att->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        Belum ada absensi hari ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
