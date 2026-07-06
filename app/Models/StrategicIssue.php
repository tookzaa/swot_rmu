<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrategicIssue extends Model
{
    protected $table = 'strategic_issues';

    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'sort_order',
    ];

    public function subTopics()
    {
        return $this->hasMany(StrategicSubTopic::class)->orderBy('sort_order');
    }
}
