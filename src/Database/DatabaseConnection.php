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
        $this->pdo->sqliteCreateFunction('co_sim', [$this, 'co_sim'], 2);

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

    public function exec(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function query(string $sql): \PDOStatement
    {
        return $this->pdo->query($sql);
    }

    function co_sim(?string $query, ?string $storedEmbedding): float
    {
        if ($query === null || $storedEmbedding === null) return 0.0;

        $vecA = unpack('f*', $query);
        $vecB = unpack('f*', $storedEmbedding);

        if (!$vecA || !$vecB) return 0.0;

        $len = min(count($vecA), count($vecB));
        if ($len === 0) return 0.0;

        $vecA = array_slice($vecA, 1, $len);
        $vecB = array_slice($vecB, 1, $len);

        $dot = $magA = $magB = 0.0;

        foreach ($vecA as $i => $aVal) {
            $bVal = $vecB[$i];
            $dot += $aVal * $bVal;
            $magA += $aVal ** 2;
            $magB += $bVal ** 2;
        }

        return ($magA === 0.0 || $magB === 0.0) ? 0.0 : $dot / (sqrt($magA) * sqrt($magB));
    }

}


