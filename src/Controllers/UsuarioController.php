<?php

namespace Fgtas\Controllers;

use Exception;
use Fgtas\Services\UsuarioService;
use Slim\Views\Twig;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsuarioController
{
    private UsuarioService $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
//        $this->usuarioRepository = $usuarioRepository;
    }

    /**
     * Renderiza a página de criação de usuários
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function registerPage(Request $request, Response $response): Response
    {
        dump($_SESSION);
        $view = Twig::fromRequest($request);

        return $view->render($response, 'cadastrar_usuario.php');
    }

    /**
     * Renderia a página de Edição de usuários
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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

        $this->usuarioService->create($data);

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
            ->key('emailUsuario', v::notEmpty()->email())
            ->key('senhaUsuario', v::notEmpty());

        if (!$dataValidation->validate($data)) {
            echo "Dados invalidos"; // TODO implementar flash messages depois!

            return $response;
        }

        $id = $args['id'];
        
        $this->usuarioService->update($data, $id);

        return $response;
    }


    public function destroy(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? '';

        if (!$this->usuarioService->delete($id)) {
            $response->getBody()->write("Erro ao deletar usuario!"); // Flash message
            return $response;
        }

        return $response
            ->withHeader('Location', '/register')
            ->withStatus(302);
    }
}
