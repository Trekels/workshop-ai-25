<?php

declare(strict_types=1);

error_reporting(E_ERROR | E_PARSE); // Disable warnings and notices
set_time_limit(0); // To default time limit

use League\Container as LeagueContainer;
use Slim\Factory\AppFactory;

const PROJECT_ROOT = __DIR__ . '/..';

require PROJECT_ROOT . '/vendor/autoload.php';
require PROJECT_ROOT . '/config/bootstrap.php';

/** @var LeagueContainer\Container $container */
AppFactory::setContainer($container);
$app = AppFactory::create();

require PROJECT_ROOT . '/config/routes.php';

$app->run();
