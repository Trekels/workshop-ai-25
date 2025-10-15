<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\MessagesRepository;

final readonly class ConversationService
{
    public function __construct(
        private OpenAIService $AIService,
        private MessagesRepository $messagesRepository,
    ) {}

    public function loadConversation(): array
    {
        return $this->messagesRepository->fetchAll();
    }

    public function handleUserPrompt(string $prompt): void
    {
        $this->messagesRepository->insert($prompt, 'user');

        // TODO: Enrich prompt with local knowledge.

        $this->messagesRepository->insert($this->AIService->prompt($prompt), 'system');
    }
}
