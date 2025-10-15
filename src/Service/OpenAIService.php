<?php

declare(strict_types=1);

namespace App\Service;

use OpenAI\Client;

final class OpenAIService
{
    private const string MODEL = 'gpt-4o';
    private const string EMBED_MODEL = 'text-embedding-3-small';

    public function __construct(
        private readonly Client $AIClient,
    ) {}

    public function prompt(string $message): string
    {
        return $this->AIClient->responses()->create([
            'model' => self::MODEL,
            'input' => $message,
        ])->outputText;
    }

    public function embed(string $content): array
    {
        $response = $this->AIClient->embeddings()->create([
            'model' => self::EMBED_MODEL,
            'input' => $content,
        ]);

        $embeddings = $response->embeddings;
        return $embeddings[array_key_first($embeddings)]->embedding;
    }
}
