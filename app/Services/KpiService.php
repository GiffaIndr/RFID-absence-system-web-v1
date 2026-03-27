<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\KpiScore;
use App\Models\KpiThreshold;
use Carbon\Carbon;

class KpiService
{
    /**
     * Hitung & simpan KPI karyawan untuk bulan ini (proses 4.1 DFD)
     */
    public function calculate(int $employeeId): KpiScore
    {
        $now   = Carbon::now();
        $year  = $now->year;
        $month = $now->month;

        // Ambil semua absensi bulan ini
        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $totalWorkDays   = $this->getWorkDaysInMonth($year, $month);
        $totalPresent    = $attendances->whereIn('status', ['present', 'late'])->count();
        $totalLate       = $attendances->where('status', 'late')->count();
        $totalOnTime     = $totalPresent - $totalLate;

        // Hitung skor kehadiran (attendance_score)
        $attendanceScore = $totalWorkDays > 0
            ? round(($totalPresent / $totalWorkDays) * 100, 2)
            : 0;

        // Hitung skor ketepatan waktu (punctuality_score)
        $punctualityScore = $totalPresent > 0
            ? round(($totalOnTime / $totalPresent) * 100, 2)
            : 0;

        // Total skor KPI = rata-rata keduanya (bisa dikustomisasi bobotnya)
        $totalScore = round(($attendanceScore * 0.6) + ($punctualityScore * 0.4), 2);

        // Validasi threshold (proses 4.2 DFD)
        $isValid       = $this->validateThreshold($attendanceScore, $punctualityScore);
        $tapOutAllowed = $isValid;

        // Simpan atau update KPI (proses 4.3 DFD)
        $kpi = KpiScore::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'year'        => $year,
                'month'       => $month,
            ],
            [
                'attendance_score'  => $attendanceScore,
                'punctuality_score' => $punctualityScore,
                'total_score'       => $totalScore,
                'status'            => $isValid ? 'valid' : 'invalid',
                'tap_out_allowed'   => $tapOutAllowed,
                'calculated_at'     => now(),
            ]
        );

        return $kpi;
    }

    /**
     * Validasi apakah skor memenuhi semua threshold (proses 4.2 DFD)
     */
    public function validateThreshold(float $attendanceScore, float $punctualityScore): bool
    {
        $thresholds = KpiThreshold::where('is_active', true)->get();

        foreach ($thresholds as $threshold) {
            $score = match ($threshold->metric) {
                'attendance_rate'   => $attendanceScore,
                'punctuality_score' => $punctualityScore,
                default             => 100,
            };

            if ($score < $threshold->min_value) {
                return false; // KPI tidak valid → tap-out diblokir
            }
        }

        return true;
    }

    /**
     * Hitung hari kerja dalam sebulan (Senin–Jumat)
     */
    private function getWorkDaysInMonth(int $year, int $month): int
    {
        $start   = Carbon::create($year, $month, 1);
        $end     = $start->copy()->endOfMonth();
        $workDays = 0;

        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $workDays++;
            }
            $start->addDay();
        }

        return $workDays;
    }
}
