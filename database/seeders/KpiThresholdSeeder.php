<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KpiThreshold;

class KpiThresholdSeeder extends Seeder
{
    public function run(): void
    {
        $thresholds = [
            [
                'name' => 'Kehadiran Minimum',
                'metric' => 'attendance_rate',
                'min_value'=> 80.00,
                'max_value' => 100.00,
                'description' => 'Karyawan wajib hadir minimal 80% dalam sebulan',
            ],
            [
                'name' => 'Ketepatan Waktu',
                'metric' => 'punctuality_score',
                'min_value' => 75.00,
                'max_value'=> 100.00,
                'description' => 'Skor ketepatan waktu minimal 75%',
            ],
        ];

        foreach ($thresholds as $threshold) {
            KpiThreshold::firstOrCreate(
                ['metric' => $threshold['metric']],
                $threshold
            );
        }
    }
}
