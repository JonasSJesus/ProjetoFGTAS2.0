<?php

use Fgtas\Controllers\AuthController;
use Fgtas\Controllers\UsuarioController;
use Fgtas\Middlewares\AuthMiddleware;
use Fgtas\Middlewares\PermissionMiddleware;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

return function (App $app) {
    // Redireciona a rota raiz para home para toda a aplicação
    $app->redirect('/', '/home', 301);

    // Rotas publicas ===============================
    $app->group('', function (Group $group){

        $group->group('/login', function ($g){
            $g->get('', [AuthController::class, 'loginPage']);
            $g->post('', [AuthController::class, 'login']);
        });

        $group->get('/logout', [AuthController::class, 'logout']);

    });


    // Rotas protegidas ==============================
    $app->group('', function (Group $group) {

        $group->group('/update-user/{id}', function ($g) {
            $g->get('', [UsuarioController::class, 'updatePage']);
            $g->post('', [UsuarioController::class, 'update']);
        });

    })->add(AuthMiddleware::class);


    // Rotas de acesso exclusivo de Usuarios Administradores ==============

    $app->group('', function (Group $group) {

        $group->group('/register', function ($g){
            $g->get('', [UsuarioController::class, 'registerPage']);
            $g->post('', [UsuarioController::class, 'store']);
        });

    })->add(PermissionMiddleware::class)->add(AuthMiddleware::class);






    /**
     * ===============================================================
     */


    /**
     * ===============================================================
     */


    /**
     * ===============================================================
     */


    /**
     * =====================Rotas de Teste============================
     */
    $app->get('/home', function ($request, $response) {
        dump($_SERVER);
        $view = Twig::fromRequest($request);

        return $view->render($response, 'home.php');
    })->add(AuthMiddleware::class);

    $app->get('/usuario', function ($vem, $vai){
        $view = Twig::fromRequest($vem);

        return $view->render($vai, 'usuario.php');
    });

    $app->get('/admin',function ($request, $response) {
            $view = Twig::fromRequest($request);

            return $view->render($response, 'admin.php');

    })->add(PermissionMiddleware::class)->add(AuthMiddleware::class);


    $app->get('/register-user', function ($request, $response) {
        dump($_SESSION);
        $view = Twig::fromRequest($request);

        return $view->render($response, 'cadastrar_usuario.php');
    })->add(PermissionMiddleware::class)->add(AuthMiddleware::class);
};
