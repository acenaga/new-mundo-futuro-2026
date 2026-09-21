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
        Schema::table('developer_resources', function (Blueprint $table) {
            $table->foreign('developer_resource_category_id')
                ->references('id')
                ->on('developer_resource_categories')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('developer_resources', function (Blueprint $table) {
            $table->dropForeign(['developer_resource_category_id']);
        });
    }
};
