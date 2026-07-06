<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SwotCategory extends Model
{
    protected $table = 'swot_categories';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'category_name',
    ];

    public function questions()
    {
        return $this->hasMany(QuestionSwot::class, 'swot_category_id');
    }
}
