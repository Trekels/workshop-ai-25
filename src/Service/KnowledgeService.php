<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\KnowledgeItemRepository;

final readonly class KnowledgeService
{
    public function __construct(
        private KnowledgeItemRepository $itemRepository,
    ) {}

    public function getItem(int $id): array
    {
        return $this->itemRepository->findOne($id);
    }

    public function updateItem(int $id, string $content): void
    {
        // TODO get embedding...
        $embedding = [0.1];

        $this->itemRepository->update($id, $content, pack('f*', ...$embedding));
    }

    public function addItem(string $content): void
    {
        // TODO get embedding...
        $embedding = [0.1];

        $this->itemRepository->insert($content, pack('f*', ...$embedding));
    }
}
