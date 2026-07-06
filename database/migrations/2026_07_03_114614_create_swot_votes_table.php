<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('swot_votes', function (Blueprint $table) {
            $table->id();
            $table->integer('question_swot_id');
            $table->string('voter_token', 36);
            $table->unsignedTinyInteger('score');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['question_swot_id', 'voter_token']);
            $table->foreign('question_swot_id')->references('id')->on('question_swot')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('swot_votes');
    }
};
