<?php

use App\Ai\Agents\ArticleDraftReviewerAgent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Messages\UserMessage;
use Laravel\Ai\Storage\DatabaseConversationStore;

it('stores conversations and messages for a participant using the laravel ai v1 schema', function () {
    $user = User::factory()->create();
    $store = new DatabaseConversationStore;

    $conversationId = $store->storeConversation($user->getMorphClass(), $user->getKey(), 'Draft review');
    $messageId = $store->storeUserMessage(
        $conversationId,
        $user->getMorphClass(),
        $user->getKey(),
        ArticleDraftReviewerAgent::class,
        new UserMessage('Review this draft.'),
    );

    expect(DB::table('agent_conversations')->find($conversationId))
        ->participant_type->toBe($user->getMorphClass())
        ->participant_id->toEqual($user->getKey())
        ->title->toBe('Draft review');

    expect(DB::table('agent_conversation_messages')->find($messageId))
        ->participant_type->toBe($user->getMorphClass())
        ->participant_id->toEqual($user->getKey())
        ->steps->toBe('[]')
        ->status->toBe('completed');

    expect($store->latestConversationId($user->getMorphClass(), $user->getKey(), ArticleDraftReviewerAgent::class))
        ->toBe($conversationId);
});
