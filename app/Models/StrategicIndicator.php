<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrategicIndicator extends Model
{
    protected $table = 'strategic_indicators';

    const UPDATED_AT = null;

    protected $fillable = [
        'strategic_sub_topic_id',
        'name',
        'sort_order',
    ];

    public function subTopic()
    {
        return $this->belongsTo(StrategicSubTopic::class, 'strategic_sub_topic_id');
    }

    public function targets()
    {
        return $this->hasMany(StrategicIndicatorTarget::class)->orderBy('year');
    }

    public function answers()
    {
        return $this->hasMany(StrategicIndicatorAnswer::class, 'strategic_indicator_id');
    }
}
