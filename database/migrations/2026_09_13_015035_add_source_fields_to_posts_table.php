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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('source_url')->nullable()->after('video_url');
            $table->string('source_title')->nullable()->after('source_url');
            $table->string('source_author')->nullable()->after('source_title');
            $table->string('source_site')->nullable()->after('source_author');
            $table->timestamp('source_published_at')->nullable()->after('source_site');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'source_url',
                'source_title',
                'source_author',
                'source_site',
                'source_published_at',
            ]);
        });
    }
};
