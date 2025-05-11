<?php

namespace Fgtas\Controllers;

use Exception;
use Fgtas\Exceptions\EmailAlreadyExistsException;
use Fgtas\Exceptions\UserNotFoundException;
use Fgtas\Services\UsuarioService;
use Fgtas\Validations\Validator;
use Slim\Views\Twig;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UsuarioController
{
    private UsuarioService $usuarioService;
    private Validator $validator;
    private Twig $twig;

    public function __construct(UsuarioService $usuarioService, Validator $validator, Twig $twig)
    {
        $this->usuarioService = $usuarioService;
        $this->validator = $validator;
        $this->twig = $twig;
    }

    /**
     * Renderiza a página de criação de usuários
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function registerPage(Request $request, Response $response): Response
    {
        return $this->twig->render($response, '/views/cadastrar_usuario.html.twig'
        );
    }

    /**
     * Renderiza a página de Edição de usuários
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function updatePage(Request $request, Response $response, array $args): Response
    {
        try {
            $user = $this->usuarioService->getUser($args['id']);
        } catch (UserNotFoundException $e) {
            dump($e->getMessage()); // TODO: Flash Message ou 404 page?

            return $response;
        }

        return $this->twig->render($response, '/views/editar_usuario.html.twig', [
            'user' => $user
        ]);
    }

    public function adminPage(Request $request, Response $response): Response
    {
        $users = $this->usuarioService->getUser();

        return $this->twig->render($response, '/views/admin.html.twig', [
            'users' => $users
        ]);
    }


    /**
     * Envia os dados do formulário para a camada service.
     * Salva os dados no banco
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $this->validator->validate($data, [
            'nomeUsuario' => v::notEmpty(),
            'emailUsuario' => v::notEmpty()->email(),
            'senhaUsuario' => v::notEmpty()
        ]);

        if ($this->validator->failed()) {
            dump($this->validator->getErrors()); // TODO implementar flash messages!
        }

        try {
            $this->usuarioService->registerUser($data);
        } catch (EmailAlreadyExistsException $e) {
            // Todo Flash Message do erro!!!!
            dump($e->getMessage());

            return $response;
//                ->withHeader('Location', '/register')
//                ->withStatus(302);
        }

        return $response
                ->withHeader('Location', '/register')
                ->withStatus(302);
    }


    /**
     * Atualiza os dados de um usuario existente no banco de dados
     *
     * @throws Exception
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        $this->validator->validate($data, [
            'nomeUsuario' => v::notEmpty(),
            'emailUsuario' => v::notEmpty()->email()
        ]);

        if ($this->validator->failed()) {
            dump($this->validator->getErrors()); // TODO implementar flash messages!
        }

        try {
            $this->usuarioService->update($data, $args['id']);
        } catch (UserNotFoundException $e) {
            dump($e->getMessage());

            return $response
                ->withHeader('Location', '/admin')
                ->withStatus(302);
        }

        return $response
            ->withHeader('Location', '/admin')
            ->withStatus(302);
    }


    public function destroy(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? '';

        try {
            if (!$this->usuarioService->delete($id)) {
                $response->getBody()->write("Erro ao deletar usuario!"); // TODO: Flash message
                return $response;
            }
        } catch (UserNotFoundException $e) {
            dump($e->getMessage()); // TODO: Flash Message

            return $response
                ->withHeader('Location', '/admin')
                ->withStatus(302);
        }

        return $response
            ->withHeader('Location', '/admin')
            ->withStatus(302);
    }
}
