<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('swot_categories', function (Blueprint $table) {
            $table->tinyInteger('vote_status')->unsigned()->default(1)->after('category_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('swot_categories', function (Blueprint $table) {
            $table->dropColumn('vote_status');
        });
    }
};
