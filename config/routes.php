<?php

declare(strict_types=1);

use \Slim\App as SlimApp;
/* @var SlimApp $app */

$app->map(['GET', 'POST'], '/', App\Controller\PromptController::class);
$app->map(['GET'], '/knowledge/items', App\Controller\KnowledgeListController::class);
$app->map(['GET', 'POST'], '/knowledge/item/{id}', App\Controller\EditKnowledgeItemController::class);
$app->map(['GET', 'POST'], '/knowledge/create', App\Controller\EditKnowledgeItemController::class);
