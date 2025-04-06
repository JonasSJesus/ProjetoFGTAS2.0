<?php

namespace Fgtas\Controllers;

use Fgtas\Services\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class AuthController
{
    private AuthService $authService;

    /**
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    public function loginPage(Request $request, Response $response, array $args): Response
    {
        dump($_SESSION);
        $view = Twig::fromRequest($request);

        return $view->render($response, 'login.php');
    }
    
    public function login(Request $request, Response $response): Response
    {
        // email: admin | senha: admin
        $data = $request->getParsedBody();
        $email = filter_var($data['email']);
        $password = filter_var($data['senha']);

        if (!$email){
            // TODO: cadastrar FlashMessages
            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }

        if (!$this->authService->authenticate($email, $password)) {
            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }

        return $response
            ->withHeader('Location', '/home')
            ->withStatus(302);
    }

    public function logout(Request $request, Response $response): Response
    {
        $this->authService->destroySession();
        return $response
            ->withHeader('Location', '/login')
            ->withStatus(302);
    }
}