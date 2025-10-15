<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\DatabaseConnection;

final readonly class MessagesRepository
{
    public function __construct(
        private DatabaseConnection $conn
    ) {}

    public function insert(string $content, string $type): void
    {
        $this->conn->exec(
            "INSERT INTO messages (content, type) VALUES (:content, :type)",
            ['content' => $content, 'type' => $type],
        );
    }

    public function fetchAll(): array
    {
        return $this->conn->query(
            "SELECT * FROM messages ORDER BY created_at ASC",
        )->fetchAll();
    }
}
