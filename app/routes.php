<?php

use Fgtas\Controllers\AtendimentoController;
use Fgtas\Controllers\AuthController;
use Fgtas\Controllers\ReportController;
use Fgtas\Controllers\UsuarioController;
use Fgtas\Middlewares\AuthMiddleware;
use Fgtas\Middlewares\PermissionMiddleware;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

return function (App $app) {
    // Redireciona a rota raiz para dashboard para toda a aplicação
    $app->redirect('/', '/dashboard', 301);


    /**
     * ============================================================================+
     *                                                                             |
     * Rotas Publicas                                                              |
     *                                                                             |
     * ============================================================================+
     */

    $app->group('', function (Group $group){

        $group->group('/login', function ($g){
            $g->get('', [AuthController::class, 'loginPage']);
            $g->post('', [AuthController::class, 'login']);
        });

        $group->get('/logout', [AuthController::class, 'logout'])->setName("logout");

    });


    /**
     * ============================================================================+
     *                                                                             |
     * Rotas Protegias                                                             |
     *                                                                             |
     * ============================================================================+
     */

    $app->group('', function (Group $group) {

        $group->group('/update-user/{id}', function ($g) {
            $g->get('', [UsuarioController::class, 'updatePage'])->setName('user.update');
            $g->post('', [UsuarioController::class, 'update'])->setName('user.update.submit');
        });

        $group->group('/home', function ($g) {
            $g->get('', [AtendimentoController::class, 'formsAtendimentoPage'])->setName('atendimento.home');
            $g->post('', [AtendimentoController::class, 'store']);
        });

        $group->group('/update-atendimento/{id}', function ($g) {
            $g->get('', [AtendimentoController::class, 'updateAtendimentoPage'])->setName('atendimento.update');
            $g->post('', [AtendimentoController::class, 'update']);
        });

        $group->group('/generate-report', function ($g) {
            $g->post('', [ReportController::class, 'generateReport'])->setName('report.generate');
        });

//        $group->get('/relatorio', [AtendimentoController::class, 'relatorioPage']);
        $group->get('/dashboard', [AtendimentoController::class, 'dashboardPage'])->setName('dashboard.user');
        $group->get('/delete-atendimento/{id}', [AtendimentoController::class, 'destroy'])->setName('atendimento.delete');

    })->add(AuthMiddleware::class);



    /**
     * ============================================================================+
     *                                                                             |
     * Rotas de acesso exclusivo de Usuarios Administradores                       |
     *                                                                             |
     * ============================================================================+
     */

    $app->group('', function (Group $group) {

        $group->group('/register', function ($g){
            $g->get('', [UsuarioController::class, 'registerPage'])->setName('user.register.form');
            $g->post('', [UsuarioController::class, 'store']);
        });

        $group->group('/admin', function ($g) {
            $g->get('', [UsuarioController::class, 'adminPage'])->setName('dashboard.admin');
            $g->post('', [UsuarioController::class, '']);

        });

        $group->get('/delete-user/{id}', [UsuarioController::class, 'destroy'])->setName('user.delete');

    })->add(PermissionMiddleware::class)->add(AuthMiddleware::class);



    $app->get('/test-form', function ($request, $response) {
        $view = Twig::fromRequest($request);

        return $view->render($response, "formulario_de_testes.html.twig");
    });

};
