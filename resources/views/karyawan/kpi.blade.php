@extends('layouts.karyawan')
@section('title', 'KPI Saya')

@section('content')

    <div class="row g-4">

        {{-- KPI Bulan Ini --}}
        <div class="col-md-4">
            <div class="card table-card p-4">
                <h6 class="fw-semibold mb-4">KPI Bulan Ini</h6>
                <div class="text-center mb-4">
                    <div class="kpi-ring mx-auto mb-3"
                        style="background: {{ $currentKpi->total_score >= 80 ? '#dcfce7' : '#fee2e2' }}">
                        <span style="color: {{ $currentKpi->total_score >= 80 ? '#16a34a' : '#dc2626' }};font-size:2rem">
                            {{ number_format($currentKpi->total_score, 0) }}
                        </span>
                        <small class="fw-normal text-muted" style="font-size:0.75rem">/ 100</small>
                    </div>
                    <span
                        class="badge rounded-pill px-3 py-2 {{ $currentKpi->status === 'valid' ? 'badge-valid' : 'badge-invalid' }}">
                        {{ $currentKpi->status === 'valid' ? 'KPI Valid ✓' : 'KPI Tidak Valid ✗' }}
                    </span>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Kehadiran (60%)</span>
                        <span class="fw-medium">{{ number_format($currentKpi->attendance_score, 1) }}%</span>
                    </div>
                    <div class="progress mb-3" style="height:8px;border-radius:4px">
                        <div class="progress-bar bg-success" style="width:{{ $currentKpi->attendance_score }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Ketepatan Waktu (40%)</span>
                        <span class="fw-medium">{{ number_format($currentKpi->punctuality_score, 1) }}%</span>
                    </div>
                    <div class="progress" style="height:8px;border-radius:4px">
                        <div class="progress-bar bg-primary" style="width:{{ $currentKpi->punctuality_score }}%"></div>
                    </div>
                </div>

                @if (!$currentKpi->tap_out_allowed)
                    <div class="alert alert-danger border-0 rounded-3 py-2 px-3 mt-3">
                        <small><i class="bi bi-lock me-1"></i>
                            Tap-out diblokir karena KPI tidak memenuhi threshold.</small>
                    </div>
                @else
                    <div class="alert alert-success border-0 rounded-3 py-2 px-3 mt-3">
                        <small><i class="bi bi-unlock me-1"></i>
                            Tap-out diizinkan.</small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Riwayat KPI Tahunan --}}
        <div class="col-md-8">
            <div class="card table-card">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 px-4">
                    <h6 class="mb-0 fw-semibold">Riwayat KPI {{ $year }}</h6>
                    <form method="GET" class="d-flex gap-2">
                        <select name="year" class="form-select form-select-sm" style="width:100px"
                            onchange="this.form.submit()">
                            @foreach (range(date('Y') - 1, date('Y')) as $y)
                                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Kehadiran</th>
                                <th>Ketepatan</th>
                                <th>Total Skor</th>
                                <th>Status</th>
                                <th>Tap-out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kpiHistory as $kpi)
                                <tr>
                                    <td class="fw-medium">
                                        {{ \Carbon\Carbon::create()->month($kpi->month)->translatedFormat('F') }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1"
                                                style="height:6px;border-radius:3px;width:60px">
                                                <div class="progress-bar bg-success"
                                                    style="width:{{ $kpi->attendance_score }}%"></div>
                                            </div>
                                            <small>{{ number_format($kpi->attendance_score, 1) }}%</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1"
                                                style="height:6px;border-radius:3px;width:60px">
                                                <div class="progress-bar bg-primary"
                                                    style="width:{{ $kpi->punctuality_score }}%"></div>
                                            </div>
                                            <small>{{ number_format($kpi->punctuality_score, 1) }}%</small>
                                        </div>
                                    </td>
                                    <td class="fw-semibold {{ $kpi->total_score >= 80 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($kpi->total_score, 1) }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge rounded-pill px-3 {{ $kpi->status === 'valid' ? 'badge-valid' : 'badge-invalid' }}">
                                            {{ ucfirst($kpi->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($kpi->tap_out_allowed)
                                            <i class="bi bi-unlock text-success"></i>
                                        @else
                                            <i class="bi bi-lock text-danger"></i>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-graph-up fs-3 d-block mb-2"></i>
                                        Belum ada data KPI untuk tahun {{ $year }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
