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
        if (Schema::hasTable('developer_resources')) {
            return;
        }

        Schema::create('developer_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('developer_resource_category_id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('external_url')->unique();
            $table->string('summary', 500);
            $table->text('why_use_it');
            $table->text('when_not_to_use_it');
            $table->string('pricing');
            $table->text('free_tier_details');
            $table->boolean('no_card_required')->default(false);
            $table->string('logo_path')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('draft');
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['developer_resource_category_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_resources');
    }
};
