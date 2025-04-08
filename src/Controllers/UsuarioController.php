<?php

namespace Fgtas\Controllers;

use Exception;
use Fgtas\Entities\Usuario;
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

    /** métodos para devolver paginas na resposta */
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
    public function registerUserPage(Request $request, Response $response): Response
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
    public function userUpdatePage(Request $request, Response $response, array $args): Response
    {
        dump($_SESSION);
        $view = Twig::fromRequest($request);

        return $view->render($response, 'editar_usuario.php');
    }

    public function adminPage(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'admin');
    }


    /**
     * =====================================================================================================================
     */

    /** Métodos com ações */
    /**
     * Envia os dados do formulário para a camada service.
     * Salva os dados no banco
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function registerUser(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (!isset($data['nomeUsuario']) || !isset($data['emailUsuario']) || !isset($data['senhaUsuario'])) {
            $response->getBody()->write('Todos os principais campos estão vazios, corrija isso!'); // Flash Message
            return $response;
        }

        $this->usuarioService->create($data);

//        TODO Descomentar este codigo em producao! (ou quando o sistema de flash messages estiver implementado)
//        por enquanto, estou usando o sistema de Error Handler do proprio slim para printar meus erros na tela
//        quando a aplicação estiver em produção, vou usar flash messages para lidar com as mensagens de erro
//        try {
//            $this->usuarioService->create($data);
//        } catch (Exception $e) {
//            $response->getBody()->write('Erro ao cadastrar usuario no sistema:' . $e->getMessage());
//            return $response;
//        }
        return $response
                ->withHeader('Location', '/register')
                ->withStatus(302);
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function updateUser(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $id = $args['id'];

        if (!isset($data['nomeUsuario']) || !isset($data['emailUsuario']) || !isset($data['senhaUsuario'])) {
            $response->getBody()->write('Nem todos os principais campos foram preenchidos, corrija isso!'); // Flash Message
            return $response;
        }

        $this->usuarioService->update($data, $id);

        return $response;
    }

}