<?php

namespace Fgtas\Controllers;

use Fgtas\Exceptions\InvalidPasswordException;
use Fgtas\Services\AuthService;
use Fgtas\Validations\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;
use Slim\Flash\Messages;
use Slim\Views\Twig;

class AuthController
{
    private AuthService $authService;
    private Validator $validator;
    private Twig $twig;
    private Messages $flash;

    public function __construct(AuthService $authService, Validator $validator, Twig $twig, Messages $flash)
    {
        $this->authService = $authService;
        $this->validator = $validator;
        $this->twig = $twig;
        $this->flash = $flash;
    }


    public function loginPage(Request $request, Response $response, array $args): Response
    {
        $flashValidation = $this->flash->getMessage('error-validate');
        $flashAuthentication = $this->flash->getMessage('error-auth');

        return $this->twig->render(
            $response, '/views/login.html.twig', [
                'flashValidate' => $flashValidation[0] ?? null,
                'flashAuth' => $flashAuthentication[0] ?? null
            ]);
    }
    
    public function login(Request $request, Response $response): Response
    {
        // email: admin@email.com | senha: admin
        $data = $request->getParsedBody();

        $this->validator->validate($data, [
            'email' => v::notEmpty()->email(),
            'senha' => v::notEmpty()->length(5)
        ]);

        if ($this->validator->failed()) {
            $this->flash->addMessage('error-validate', $this->validator->getErrors()); // TODO: Talvez nao faca sentido printar os erros daqui...

            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }

        try {
            $this->authService->authenticate($data['email'], $data['senha']);
        } catch (InvalidPasswordException $e) {
            $this->flash->addMessage('error-auth', $e->getMessage());

            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }

        return $response
            ->withHeader('Location', '/dashboard')
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