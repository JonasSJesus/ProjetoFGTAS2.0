<?php

use Fgtas\Controllers\AuthController;
use Fgtas\Controllers\UsuarioController;
use Fgtas\Middlewares\AuthMiddleware;
use Fgtas\Middlewares\PermissionMiddleware;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

return function (App $app) {
    $app->group('/login', function (Group $group) {
        $group->get('', [AuthController::class, 'loginPage']);
        $group->post('', [AuthController::class, 'login']);
    });

    $app->group('/register', function (Group $group) {
        $group->get('', [AuthController::class, 'registerUserPage']);
        $group->post('', [AuthController::class, 'registerUser']);
    })->add(AuthMiddleware::class)->add(PermissionMiddleware::class);

    $app->get('/logout', [AuthController::class, 'logout']);

    // Testes de rotas
    $app->get('/home', function ($request, $response) {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'home.php');
    });//->add(AuthMiddleware::class);

};
