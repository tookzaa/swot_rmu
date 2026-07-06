<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrategicIndicatorTarget extends Model
{
    protected $table = 'strategic_indicator_targets';

    const UPDATED_AT = null;

    protected $fillable = [
        'strategic_indicator_id',
        'year',
        'target_value',
    ];
}
