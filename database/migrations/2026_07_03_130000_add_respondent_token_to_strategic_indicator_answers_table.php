<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('strategic_indicator_answers', function (Blueprint $table) {
            $table->string('respondent_token', 36)->nullable()->after('user_id');
            $table->unique(['strategic_indicator_id', 'respondent_token'], 'sia_indicator_respondent_unique');
        });
    }

    public function down(): void
    {
        Schema::table('strategic_indicator_answers', function (Blueprint $table) {
            $table->dropUnique('sia_indicator_respondent_unique');
            $table->dropColumn('respondent_token');
        });
    }
};
