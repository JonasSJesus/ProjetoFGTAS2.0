<?php

namespace Fgtas\Controllers;

use Fgtas\Services\AuthService;
use Fgtas\Validations\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;
use Slim\Views\Twig;

class AuthController
{
    private AuthService $authService;
    private Validator $validator;

    public function __construct(AuthService $authService, Validator $validator)
    {
        $this->authService = $authService;
        $this->validator = $validator;
    }


    public function loginPage(Request $request, Response $response, array $args): Response
    {
//        dump($_SESSION);
        $view = Twig::fromRequest($request);

        return $view->render($response, 'login.php');
    }
    
    public function login(Request $request, Response $response): Response
    {
        // email: admin@email.com | senha: admin
        $data = $request->getParsedBody();

        $this->validator->validate($data, [
            'email' => v::notEmpty()->email(),
            'senha' => v::notEmpty()->min(5)
        ]);

        if ($this->validator->failed()) {
            // TODO: cadastrar FlashMessages
            dump($this->validator->getErrors());

            return $response;
//                ->withHeader('Location', '/login')
//                ->withStatus(302);
        }

        if (!$this->authService->authenticate($data['email'], $data['senha'])) {
            // TODO: cadastrar FlashMessages
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