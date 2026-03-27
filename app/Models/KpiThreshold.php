<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiThreshold extends Model
{
    protected $fillable = [
        'name', 'metric', 'min_value', 'max_value',
        'is_active', 'description',
    ];
}
