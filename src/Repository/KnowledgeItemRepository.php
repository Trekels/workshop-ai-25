<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\DatabaseConnection;

final readonly class KnowledgeItemRepository
{
    public function __construct(
        private DatabaseConnection $conn
    ) {}

    public function insert(string $content, string $embedding): void
    {
        $this->conn->exec(
            "INSERT INTO knowledge_items (content, embedding) VALUES (:content, :embedding)",
            ['content' => $content, 'embedding' => $embedding],
        );
    }

    public function update(int $id, string $content, string $embedding): void
    {
        $this->conn->exec(
            "UPDATE knowledge_items SET content = :content, embedding = :embedding WHERE id = :id",
            ['id' => $id, 'content' => $content, 'embedding' => $embedding],
        );

    }

    public function findOne(int $id): ?array
    {
        $stmt = $this->conn->query("SELECT * FROM knowledge_items WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetchAll()[0] ?? null;
    }

    public function simSearch(string $embeddedQuery): array
    {
        return $this->conn->exec(
            "SELECT *, co_sim(:query, embedding) AS similarity FROM knowledge_items LIMIT 5",
            ['query' => $embeddedQuery]
        );
    }

    public function fetchAll(): array
    {
        return $this->conn->query(
            "SELECT * FROM knowledge_items ORDER BY created_at ASC",
        )->fetchAll();
    }
}
