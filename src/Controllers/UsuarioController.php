<?php

namespace Fgtas\Controllers;

use Fgtas\Services\UsuarioService;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsuarioController
{
    //private Twig $view;
    private UsuarioService $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        //$this->view = $twig;
        $this->usuarioService = $usuarioService;
    }




    public function login(Request $request, Response $response): Response
    {
        echo "Hello User";

        return $response;
    }
}