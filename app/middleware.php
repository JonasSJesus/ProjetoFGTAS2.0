<?php

use DI\Container;
use Fgtas\Middlewares\FlashMsgMiddleware;
use Slim\App;
use Slim\Middleware\Session;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app, Container $container) {
    // Este middleware deve ser o primeiro!
    $app->addRoutingMiddleware();

    // Middleware para guardar as flash messages no $_SESSION
    $app->add(FlashMsgMiddleware::class);

    // Middleware para gerenciar sessoes
    $app->add(new Session([
        'name' => $_ENV['SESSION_NAME'],
        'autorefresh' => true,
        'httponly' => true,
        'samesite' => $_ENV['SESSION_SAMESITE'],
        'lifetime' => $_ENV['SESSION_LIFETIME'],
    ]));

    // Twig view Middleware
    $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));




    // TODO: implementar um middleware para regenerar o ID da sessão a cada X minutos
    // Este middleware deve estar por ultimo!
    $app->addErrorMiddleware(
        true, // Deve estar False em produção!!!
        false,
        false);
};
