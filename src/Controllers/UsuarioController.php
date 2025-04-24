<?php

namespace Fgtas\Controllers;

use Exception;
use Fgtas\Database\Connection;
use Fgtas\Services\UsuarioService;
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

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
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
     * @param Request $request
     * @param Response $response
     * @return Response
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
        $dataValidation = v::key('nomeUsuario', v::notEmpty()->stringType())
                        ->key('emailUsuario', v::notEmpty()->email())
                        ->key('senhaUsuario', v::notEmpty());

        if (!$dataValidation->validate($data)) {
            echo "Dados invalidos"; // TODO implementar flash messages depois!

            return $response;
        }

        $this->usuarioService->registerUser($data);

        return $response
                ->withHeader('Location', '/register')
                ->withStatus(302);
    }


    /**
     * Atualiza os dados de um usuario existente no banco de dados
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();


        $dataValidation = v::key('nomeUsuario', v::notEmpty()->stringType())
            ->key('emailUsuario', v::notEmpty()->email());

        if (!$dataValidation->validate($data)) {
            echo "Dados invalidos"; // TODO implementar flash messages depois!

            return $response;
        }

        $id = $args['id'];
        
        $this->usuarioService->update($data, $id);

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
