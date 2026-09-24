<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Constraint names are explicit because the auto-generated ones exceed
     * MySQL's 64-character identifier limit. The table is dropped first so a
     * previous partial run (table created, constraints failed) is repaired.
     */
    public function up(): void
    {
        Schema::dropIfExists('developer_resource_developer_resource_technology');

        Schema::create('developer_resource_developer_resource_technology', function (Blueprint $table) {
            $table->foreignId('developer_resource_id')
                ->constrained(indexName: 'dr_technology_pivot_resource_foreign')
                ->cascadeOnDelete();
            $table->foreignId('developer_resource_technology_id')
                ->constrained(indexName: 'dr_technology_pivot_technology_foreign')
                ->cascadeOnDelete();
            $table->primary(['developer_resource_id', 'developer_resource_technology_id'], 'dr_technology_pivot_primary');
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
