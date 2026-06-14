<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('type')->default('article')->after('id');
            $table->index('type');
        });

        // Backfill: mark existing tutorials based on category slug
        DB::table('posts')
            ->whereIn('category_id', function ($query) {
                $query->select('id')
                    ->from('categories')
                    ->where('slug', 'tutoriales');
            })
            ->update(['type' => 'tutorial']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }
};
