<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'employee_code', 'name', 'department',
        'position', 'phone', 'address', 'join_date', 'status',
    ];

    protected $casts = [
        'join_date' => 'date',
    ];

    // Relasi ke User (login)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke kartu RFID
    public function rfidCards()
    {
        return $this->hasMany(RfidCard::class);
    }

    // Relasi ke absensi
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Relasi ke KPI
    public function kpiScores()
    {
        return $this->hasMany(KpiScore::class);
    }

    // Relasi ke laporan harian
    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }
}
