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

    public function findOne(int $id): ?array
    {
        $stmt = $this->conn->query("SELECT * FROM knowledge_items WHERE id = :id");
        $stmt->bindValue('id', $id);

        return $stmt->fetchAll();
    }

    public function simSearch(string $embeddedQuery): void
    {
        $stmt = $this->conn->query('
            SELECT *, co_sim(:query, embedding) AS similarity
            FROM knowledge_items
            ORDER BY similarity DESC
            LIMIT :k
        ');
        $stmt->bindValue(':query', $embeddedQuery);
        $stmt->bindValue(':k', 5);

        $stmt->fetchAll();
    }

    public function fetchAll(): array
    {
        return $this->conn->query(
            "SELECT * FROM knowledge_items ORDER BY created_at ASC",
        )->fetchAll();
    }
}
