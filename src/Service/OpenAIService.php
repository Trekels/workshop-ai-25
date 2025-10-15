<?php

declare(strict_types=1);

namespace App\Service;

use OpenAI\Client;

final class OpenAIService
{
    private const string MODEL = 'gpt-4o';

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

    // TODO: Embedding method.
}
