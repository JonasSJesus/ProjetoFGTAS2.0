<?php

use Slim\App;
use Slim\Middleware\Session;

return function (App $app) {
    // Middleware para gerenciar sessoes
    $app->add(new Session([
        'name' => 'user_session',
        'autorefresh' => true,
        'httponly' => true,
        'samesite' => ''
    ]));
};
