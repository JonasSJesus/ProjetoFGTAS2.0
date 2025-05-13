<?php

namespace Fgtas\Controllers;

use Exception;
use Fgtas\Exceptions\EmailAlreadyExistsException;
use Fgtas\Exceptions\UserNotFoundException;
use Fgtas\Services\UsuarioService;
use Fgtas\Validations\Validator;
use Slim\Exception\HttpNotFoundException;
use Slim\Flash\Messages;
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
    private Messages $flash;

    public function __construct(UsuarioService $usuarioService, Validator $validator, Twig $twig, Messages $flash)
    {
        $this->usuarioService = $usuarioService;
        $this->validator = $validator;
        $this->twig = $twig;
        $this->flash = $flash;
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
        dump($this->flash->getMessages()); // TODO: Printar os erros no template

        return $this->twig->render($response, '/views/cadastrar_usuario.html.twig', [
            'validation' => $this->flash->getMessage('usuario-validate')[0],
            'userExists' => $this->flash->getMessage('usuario-exists')[0],
            'update' => $this->flash->getMessage('usuario-update')[0],
            'userNotFound' => $this->flash->getMessage('usuario-not-found')[0],
            'userDestroyError' => $this->flash->getMessage('usuario-destroy-error')[0],
        ]);
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
            dump($e->getMessage()); // TODO: Flash Message ou 404 page? ---por enquanto 404

            throw new HttpNotFoundException($request);
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
            $this->flash->addMessage('usuario-validate', $this->validator->getErrors());

            return $response
                ->withHeader('Location', '/register')
                ->withStatus(302);
        }

        try {
            $this->usuarioService->registerUser($data);
        } catch (EmailAlreadyExistsException $e) {
            $this->flash->addMessage('usuario-exists', $e->getMessage());

            return $response
                ->withHeader('Location', '/register')
                ->withStatus(302);
        }

        // TODO: Flash Message de sucesso ao criar um usuario.
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
            $this->flash->addMessage('usuario-update' ,$this->validator->getErrors());

            return $response
                ->withHeader('Location', '/admin') // TODO: mudar o endereco
                ->withStatus(302);
        }

        try {
            $this->usuarioService->update($data, $args['id']);
        } catch (UserNotFoundException $e) {
            $this->flash->addMessage('usuario-not-found', $e->getMessage());

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
                $this->flash->addMessage('usuario-destroy-error', "Erro ao deletar usuario!");

                return $response;
            }
        } catch (UserNotFoundException $e) {
            $this->flash->addMessage('user-not-found', $e->getMessage());

            return $response
                ->withHeader('Location', '/admin')
                ->withStatus(302);
        }

        return $response
            ->withHeader('Location', '/admin')
            ->withStatus(302);
    }
}
