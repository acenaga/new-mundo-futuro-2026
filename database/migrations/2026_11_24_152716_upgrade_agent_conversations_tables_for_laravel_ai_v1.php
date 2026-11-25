<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Ai\Migrations\AiMigration;

/**
 * Upgrades the conversation tables from the laravel/ai v0 schema (user_id,
 * tool_calls, tool_results) to the v1 schema (polymorphic participant,
 * steps, status), preserving existing conversations and messages.
 *
 * Existing rows are attributed to the configured auth user model. Legacy
 * tool calls and results have no v1 equivalent, so migrated messages start
 * with empty steps.
 */
return new class extends AiMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $participantType = $this->userMorphClass();

        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->string('participant_type')->nullable();
            $table->unsignedBigInteger('participant_id')->nullable();
        });

        DB::table('agent_conversations')->update([
            'participant_type' => $participantType,
            'participant_id' => DB::raw('user_id'),
        ]);

        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'updated_at']);
            $table->dropColumn('user_id');
            $table->index(['participant_type', 'participant_id', 'updated_at'], 'participant_updated_at_index');
        });

        Schema::table('agent_conversation_messages', function (Blueprint $table) {
            $table->string('participant_type')->nullable();
            $table->unsignedBigInteger('participant_id')->nullable();
            $table->longText('steps')->nullable();
            $table->string('status', 25)->nullable();
        });

        DB::table('agent_conversation_messages')->update([
            'participant_type' => $participantType,
            'participant_id' => DB::raw('user_id'),
            'steps' => '[]',
            'status' => 'completed',
        ]);

        Schema::table('agent_conversation_messages', function (Blueprint $table) {
            $table->longText('steps')->nullable(false)->change();
            $table->string('status', 25)->nullable(false)->change();

            $table->dropIndex('conversation_index');
            $table->dropIndex(['user_id']);
            $table->dropColumn(['user_id', 'tool_calls', 'tool_results']);

            $table->index(['conversation_id', 'participant_type', 'participant_id', 'updated_at'], 'conversation_index');
            $table->index(['participant_type', 'participant_id', 'agent'], 'participant_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
        });

        DB::table('agent_conversations')->update(['user_id' => DB::raw('participant_id')]);

        Schema::table('agent_conversations', function (Blueprint $table) {
            $table->dropIndex('participant_updated_at_index');
            $table->dropColumn(['participant_type', 'participant_id']);
            $table->index(['user_id', 'updated_at']);
        });

        Schema::table('agent_conversation_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('tool_calls')->nullable();
            $table->text('tool_results')->nullable();
        });

        DB::table('agent_conversation_messages')->update([
            'user_id' => DB::raw('participant_id'),
            'tool_calls' => '[]',
            'tool_results' => '[]',
        ]);

        Schema::table('agent_conversation_messages', function (Blueprint $table) {
            $table->dropIndex('conversation_index');
            $table->dropIndex('participant_index');
            $table->dropColumn(['participant_type', 'participant_id', 'steps', 'status']);

            $table->index(['conversation_id', 'user_id', 'updated_at'], 'conversation_index');
            $table->index(['user_id']);
        });
    }

    /**
     * Resolve the participant_type value laravel/ai records for the auth user model.
     */
    private function userMorphClass(): string
    {
        $userModel = config('auth.providers.users.model');

        return (new $userModel)->getMorphClass();
    }
};
