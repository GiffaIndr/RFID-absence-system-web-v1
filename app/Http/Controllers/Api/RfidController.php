<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\RfidCard;
use App\Services\KpiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RfidController extends Controller
{
    public function __construct(private KpiService $kpiService) {}

    /**
     * Proses 1.0 DFD — Registrasi kartu RFID baru
     * Arduino kirim UID kartu, HRD nanti assign ke karyawan lewat website
     */
    public function register(Request $request)
    {
        $request->validate(['uid' => 'required|string']);

        $uid  = strtoupper(trim($request->uid));
        $card = RfidCard::where('uid', $uid)->first();

        if ($card) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu sudah terdaftar.',
                'data'    => ['uid' => $uid, 'status' => $card->status],
            ]);
        }

        $card = RfidCard::create([
            'uid'           => $uid,
            'employee_id'   => null,    // belum di-assign ke karyawan
            'status'        => 'active',
            'registered_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kartu berhasil didaftarkan. Silakan assign ke karyawan di website.',
            'data'    => ['uid' => $uid],
        ], 201);
    }

    /**
     * Proses 2.0 DFD — Tap-in atau tap-out otomatis
     * Kalau belum absen hari ini → tap-in
     * Kalau sudah tap-in → tap-out (cek KPI dulu)
     */
    public function checkin(Request $request)
    {
        $request->validate(['uid' => 'required|string']);

        $uid  = strtoupper(trim($request->uid));
        $card = RfidCard::with('employee')->where('uid', $uid)->first();

        // Kartu tidak ditemukan
        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak dikenali.',
                'action'  => 'unknown',
            ], 404);
        }

        // Kartu belum di-assign ke karyawan
        if (!$card->employee_id) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu belum terdaftar ke karyawan manapun.',
                'action'  => 'unassigned',
            ]);
        }

        // Kartu nonaktif
        if ($card->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak aktif.',
                'action'  => 'inactive',
            ]);
        }

        $employee  = $card->employee;
        $today     = Carbon::today();
        $now       = Carbon::now();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        // Belum tap-in hari ini → proses tap-in
        if (!$attendance) {
            // Tentukan status: terlambat jika lewat jam 08:30
            $lateThreshold = Carbon::today()->setTime(8, 30);
            $status        = $now->gt($lateThreshold) ? 'late' : 'present';

            Attendance::create([
                'employee_id'  => $employee->id,
                'rfid_card_id' => $card->id,
                'date'         => $today,
                'tap_in'       => $now,
                'status'       => $status,
            ]);

            return response()->json([
                'success'  => true,
                'action'   => 'tap_in',
                'status'   => $status,
                'message'  => 'Tap-in berhasil. ' . ($status === 'late' ? 'Anda terlambat.' : 'Tepat waktu!'),
                'employee' => $employee->name,
                'time'     => $now->format('H:i:s'),
            ]);
        }

        // Sudah tap-in tapi belum tap-out → proses tap-out
        if ($attendance->tap_in && !$attendance->tap_out) {

            // Hitung KPI dulu (proses 4.0 DFD)
            $kpi = $this->kpiService->calculate($employee->id);

            // Kalau KPI tidak valid → tap-out diblokir (status diblokir dari 4.2 DFD)
            if (!$kpi->tap_out_allowed) {
                return response()->json([
                    'success' => false,
                    'action'  => 'blocked',
                    'message' => 'Tap-out diblokir. KPI tidak memenuhi threshold.',
                    'kpi'     => [
                        'total_score'       => $kpi->total_score,
                        'attendance_score'  => $kpi->attendance_score,
                        'punctuality_score' => $kpi->punctuality_score,
                    ],
                ]);
            }

            // KPI valid → izinkan tap-out
            $workDuration = $attendance->tap_in->diffInMinutes($now);

            $attendance->update([
                'tap_out'       => $now,
                'work_duration' => $workDuration,
            ]);

            return response()->json([
                'success'       => true,
                'action'        => 'tap_out',
                'message'       => 'Tap-out berhasil.',
                'employee'      => $employee->name,
                'time'          => $now->format('H:i:s'),
                'work_duration' => $workDuration . ' menit',
                'kpi_score'     => $kpi->total_score,
            ]);
        }

        // Sudah tap-in dan tap-out keduanya
        return response()->json([
            'success' => false,
            'action'  => 'already_done',
            'message' => 'Absensi hari ini sudah lengkap.',
        ]);
    }

    /**
     * Proses 4.4 DFD — Cek status izin tap-out
     * Arduino bisa poll endpoint ini sebelum membuka pintu
     */
    public function status(string $uid)
    {
        $uid  = strtoupper(trim($uid));
        $card = RfidCard::with('employee')->where('uid', $uid)->first();

        if (!$card || !$card->employee_id) {
            return response()->json([
                'success' => false,
                'allowed' => false,
                'message' => 'Kartu tidak dikenali.',
            ], 404);
        }

        $kpi = $this->kpiService->calculate($card->employee->id);

        return response()->json([
            'success'    => true,
            'allowed'    => $kpi->tap_out_allowed,
            'kpi_score'  => $kpi->total_score,
            'status'     => $kpi->status,
            'employee'   => $card->employee->name,
        ]);
    }
}
