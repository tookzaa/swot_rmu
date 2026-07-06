<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerSwot extends Model
{
    protected $table = 'answer_swot';

    const UPDATED_AT = null;

    public const TYPE_KEEP = 1;
    public const TYPE_EDITED = 2;

    protected $fillable = [
        'question_swot_id',
        'user_id',
        'respondent_token',
        'answer_type',
        'answer_detail',
    ];

    public function question()
    {
        return $this->belongsTo(QuestionSwot::class, 'question_swot_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
