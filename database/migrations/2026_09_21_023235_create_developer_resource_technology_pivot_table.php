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
        if (Schema::hasTable('developer_resource_developer_resource_technology')) {
            return;
        }

        Schema::create('developer_resource_developer_resource_technology', function (Blueprint $table) {
            $table->foreignId('developer_resource_id')->constrained()->cascadeOnDelete();
            $table->foreignId('developer_resource_technology_id')->constrained()->cascadeOnDelete();
            $table->primary(['developer_resource_id', 'developer_resource_technology_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_resource_developer_resource_technology');
    }
};
