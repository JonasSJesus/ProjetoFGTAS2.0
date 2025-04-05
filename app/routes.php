<?php

use Slim\App;
use Slim\Views\Twig;

return function (App $app) {
    $app->get('/home', function ($request, $response) {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'home.php');
    });
};
