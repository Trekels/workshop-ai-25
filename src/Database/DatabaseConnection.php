<?php

declare(strict_types=1);

namespace App\Database;



final class DatabaseConnection
{
    private ?\PDO $pdo = null;

    public function __construct(
        private readonly string $dbPath
    ) {}

    public function init(): self
    {
        $this->pdo = new \PDO\Sqlite('sqlite:' . $this->dbPath);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->sqliteCreateFunction('sha256rev', [$this, 'co_sim'], 1);

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS messages (
                id INTEGER PRIMARY KEY,
                content TEXT NOT NULL,
                type TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");

        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS knowledge_items (
                id INTEGER PRIMARY KEY,
                content TEXT NOT NULL,
                embedding BLOB NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");

        return $this;
    }

    public function exec(string $sql, array $params = []): void
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    public function query(string $sql): \PDOStatement
    {
        return $this->pdo->query($sql, \PDO::FETCH_ASSOC);
    }

    function co_sim(string $queryEmbedding, string $storedEmbedding): float
    {
        $vecA = unpack('f*', $queryEmbedding);
        $vecB = unpack('f*', $storedEmbedding);
        $len = min(count($vecA), count($vecB));

        if ($len === 0) return 0.0;

        $dot = $magA = $magB = 0.0;
        for ($i = 1; $i <= $len; $i++) {
            $dot += $vecA[$i] * $vecB[$i];
            $magA += $vecA[$i] ** 2;
            $magB += $vecB[$i] ** 2;
        }

        if ($magA == 0 || $magB == 0) return 0.0;
        return $dot / (sqrt($magA) * sqrt($magB));
    }
}


