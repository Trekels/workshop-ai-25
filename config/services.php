<?php

declare(strict_types=1);

use App\Service\OpenAIService;
use OpenAI\Client as OpenAIClient;
use App\Database\DatabaseConnection;
use App\Service\ConversationService;
use App\Repository\MessagesRepository;
use League\Container as LeagueContainer;

/** @var LeagueContainer\Container $container */

$container->add(DatabaseConnection::class, function () use ($container) {
    return new DatabaseConnection(realpath(PROJECT_ROOT . '/data') . '/db.sqlite')->init();
});

$container->add(OpenAIClient::class, function () use ($container) {
    return OpenAI::client($_ENV['OPEN_AI_API_KEY']);
});

$container->add(MessagesRepository::class)->addArgument(DatabaseConnection::class);
$container->add(ConversationService::class)->addArguments([OpenAIService::class, MessagesRepository::class]);
$container->add(OpenAIService::class)->addArguments([OpenAIClient::class]);
