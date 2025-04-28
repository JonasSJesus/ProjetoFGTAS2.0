<?php

use DI\Container;
use Slim\App;
use Slim\Middleware\Session;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app, Container $container) {
    // Middleware para gerenciar sessoes
    $app->add(new Session([
        'name' => 'user_session',
        'autorefresh' => true,
        'httponly' => true,
        'samesite' => '',
        'lifetime' => '20 min',
    ]));

    $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));

    $app->addErrorMiddleware(true, false, false);



    // TODO: implementar um middleware para regenerar o ID da sessão a cada X minutos
};
