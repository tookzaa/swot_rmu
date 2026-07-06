<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrategicIndicatorAnswer extends Model
{
    protected $table = 'strategic_indicator_answers';

    const UPDATED_AT = null;

    public const TYPE_KEEP = 1;
    public const TYPE_EDITED = 2;

    protected $fillable = [
        'strategic_indicator_id',
        'user_id',
        'respondent_token',
        'answer_type',
        'answer_detail',
    ];

    public function indicator()
    {
        return $this->belongsTo(StrategicIndicator::class, 'strategic_indicator_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
