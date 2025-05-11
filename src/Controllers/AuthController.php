<?php

namespace Fgtas\Controllers;

use Fgtas\Exceptions\InvalidPasswordException;
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
    private Twig $twig;

    public function __construct(AuthService $authService, Validator $validator, Twig $twig)
    {
        $this->authService = $authService;
        $this->validator = $validator;
        $this->twig = $twig;
    }


    public function loginPage(Request $request, Response $response, array $args): Response
    {
        return $this->twig->render(
            $response, '/views/login.html.twig'
        );
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

        try {
            $this->authService->authenticate($data['email'], $data['senha']);
        } catch (InvalidPasswordException $e) {
            dump($e->getMessage()); // TODO: Flash Message

            return $response;
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