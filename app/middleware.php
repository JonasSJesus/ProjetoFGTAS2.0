<?php

use DI\Container;
use Slim\App;
use Slim\Middleware\Session;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app, Container $container) {
    // Este middleware deve ser o primeiro!
    $app->addRoutingMiddleware();

    // Middleware para gerenciar sessoes
    $app->add(new Session([
        'name' => 'user_session',
        'autorefresh' => true,
        'httponly' => true,
        'samesite' => '',
        'lifetime' => '20 min',
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
