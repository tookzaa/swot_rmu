<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionSwot extends Model
{
    protected $table = 'question_swot';

    const UPDATED_AT = null;

    protected $fillable = [
        'question_name',
        'swot_category_id',
    ];

    public function category()
    {
        return $this->belongsTo(SwotCategory::class, 'swot_category_id');
    }

    public function answers()
    {
        return $this->hasMany(AnswerSwot::class, 'question_swot_id');
    }

    public function votes()
    {
        return $this->hasMany(SwotVote::class, 'question_swot_id');
    }
}
