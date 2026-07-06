<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('answer_swot', function (Blueprint $table) {
            $table->string('respondent_token', 36)->nullable()->after('user_id');
            $table->unique(['question_swot_id', 'respondent_token']);
        });
    }

    public function down(): void
    {
        Schema::table('answer_swot', function (Blueprint $table) {
            $table->dropUnique(['question_swot_id', 'respondent_token']);
            $table->dropColumn('respondent_token');
        });
    }
};
