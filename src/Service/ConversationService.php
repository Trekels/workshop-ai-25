<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\KnowledgeItemRepository;
use App\Repository\MessagesRepository;

final readonly class ConversationService
{
    public function __construct(
        private OpenAIService $AIService,
        private MessagesRepository $messagesRepository,
        private KnowledgeItemRepository $knowledgeItemRepository,
    ) {}

    public function loadConversation(): array
    {
        return $this->messagesRepository->fetchAll();
    }

    public function handleUserPrompt(string $prompt): void
    {
        $promptEmbed = $this->AIService->embed($prompt);
        $topHits = $this->knowledgeItemRepository->simSearch(pack('f*', ...$promptEmbed));

        $this->messagesRepository->insert($prompt, 'user');

        $query = $prompt;

        $query .= `
            ----------------------------------------------------
            Extra information possibly relevant to the question:

        `;

        foreach ($topHits as $topHit) {
            $query .= $topHit['content'];
        }

        $this->messagesRepository->insert($this->AIService->prompt($query), 'system');
    }
}
