<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SwotVote extends Model
{
    protected $table = 'swot_votes';

    const UPDATED_AT = null;

    protected $fillable = [
        'question_swot_id',
        'voter_token',
        'score',
    ];

    public function question()
    {
        return $this->belongsTo(QuestionSwot::class, 'question_swot_id');
    }
}
