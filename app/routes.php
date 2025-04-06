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

    $app->get('/logout', [AuthController::class, 'logout']);

    $app->group('/register', function (Group $group) {
        $group->get('', [AuthController::class, 'registerUserPage']);
        $group->post('', [AuthController::class, 'registerUser']);
    })->add(PermissionMiddleware::class)->add(AuthMiddleware::class);


    /**
     * =====================Rotas de Teste ============================
     */
    $app->get('/home', function ($request, $response) {
        dump($_SESSION);
        $view = Twig::fromRequest($request);

        return $view->render($response, 'home.php');
    })->add(AuthMiddleware::class);

    $app->get('/admin', function ($request, $response) {
        dump($_SESSION);
        $view = Twig::fromRequest($request);

        return $view->render($response, 'admin.php');
    });

};
