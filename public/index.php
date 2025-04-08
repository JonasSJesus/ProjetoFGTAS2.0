<?php

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require_once "../config/config.php";
require_once ROOT_APP . "/vendor/autoload.php";

// Inicializando .env
$dotenv = Dotenv::createImmutable(ROOT_APP);
$dotenv->load();


// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

// Set up dependencies
$dependencies = require ROOT_APP . '/app/dependencies.php';
$dependencies($containerBuilder);

// Configurando repositories
$repositories = require ROOT_APP . '/app/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();


// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();

// Iniciando Twig
$twig = Twig::create(ROOT_APP . '/resources/views', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

// Add Body Parsing Middleware
//$app->addBodyParsingMiddleware();

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

// Error handler Middleware
$errorMiddleware = $app->addErrorMiddleware(true, false, false);

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

$app->run();
