<?php

use Slim\App;
use Slim\Middleware\Session;

return function (App $app) {
    // Middleware para gerenciar sessoes
    $app->add(new Session([
        'name' => 'user_session',
        'autorefresh' => true,
        'httponly' => true,
        'samesite' => '',
        'lifetime' => '1 hour',

    ]));

    // TODO: implementar um middleware para regenerar o ID da sessão a cada X minutos

};
