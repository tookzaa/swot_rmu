<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SwotCategory extends Model
{
    protected $table = 'swot_categories';

    public $timestamps = false;

    public const VOTE_CLOSED = 1;
    public const VOTE_OPEN = 2;

    protected $fillable = [
        'code',
        'category_name',
        'vote_status',
    ];

    public function isVotingOpen(): bool
    {
        return $this->vote_status == self::VOTE_OPEN;
    }

    public function questions()
    {
        return $this->hasMany(QuestionSwot::class, 'swot_category_id');
    }
}
