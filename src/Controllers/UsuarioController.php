<?php

namespace Fgtas\Controllers;

use Exception;
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

    public function __construct(UsuarioService $usuarioService, Validator $validator)
    {
        $this->usuarioService = $usuarioService;
        $this->validator = $validator;
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
        $view = Twig::fromRequest($request);

        return $view->render($response, 'cadastrar_usuario.html.twig');
    }

    /**
     * Renderia a página de Edição de usuários
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function updatePage(Request $request, Response $response, array $args): Response
    {
        $view = Twig::fromRequest($request);
        $user = $this->usuarioService->getUser($args);

        return $view->render($response, 'editar_usuario.html.twig', [
            'user' => $user
        ]);
    }

    public function adminPage(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        $users = $this->usuarioService->getUser();

        return $view->render($response, 'admin.html.twig', [
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

        $this->usuarioService->registerUser($data);

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

        $this->usuarioService->update($data, $args['id']);

        return $response
            ->withHeader('Location', '/admin')
            ->withStatus(302);
    }


    public function destroy(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? '';

        if (!$this->usuarioService->delete($id)) {
            $response->getBody()->write("Erro ao deletar usuario!"); // Flash message
            return $response;
        }

        return $response
            ->withHeader('Location', '/admin')
            ->withStatus(302);
    }
}
