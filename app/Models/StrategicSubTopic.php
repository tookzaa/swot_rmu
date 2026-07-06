<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrategicSubTopic extends Model
{
    protected $table = 'strategic_sub_topics';

    const UPDATED_AT = null;

    protected $fillable = [
        'strategic_issue_id',
        'code',
        'name',
        'sort_order',
    ];

    public function issue()
    {
        return $this->belongsTo(StrategicIssue::class, 'strategic_issue_id');
    }

    public function indicators()
    {
        return $this->hasMany(StrategicIndicator::class)->orderBy('sort_order');
    }
}
