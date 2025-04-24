<?php

namespace Fgtas\Controllers;

use Exception;
use Fgtas\Services\AtendimentoService;
use Fgtas\Validations\AtendimentoValidator as Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Views\Twig;
use Throwable;

/**
 * Responsavel por lidar com a comunicação entre o usuario e tudo que for relacionado aos Atendimentos no sistema
 */
class AtendimentoController
{
    private AtendimentoService $atendimentoService;

    public function __construct(AtendimentoService $atendimentoService)
    {
        $this->atendimentoService = $atendimentoService;
    }


    public function formsAtendimentoPage(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        $userLoggedIn = $_SESSION['user'];

        return $view->render($response, 'formulario_de_testes.html.twig', [
            'userName' => $userLoggedIn
        ]);
    }


    public function updateAtendimentoPage(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        $userLoggedIn = $_SESSION['user'];

        return $view->render($response, 'editar_atendimento.html.twig', [
            'userName' => $userLoggedIn
        ]);
    }


    public function dashboardPage(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        $atendimentos = $this->atendimentoService->all();

        return $view->render($response, 'usuario.html.twig', [
            'atendimentos' => $atendimentos
        ]);
    }




    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function store(Request $request, Response $response): Response
    {
        $dataFromRequest = $request->getParsedBody();
        
        if (!Validator::validate($dataFromRequest)) {
            // TODO: Implementar mensagens de erro em caso de verificacao invalida | Salvar as mensagens com Flash Messages
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/home');
        }

        try {
            $this->atendimentoService->createAtendimento($dataFromRequest);
        } catch (Exception $e) {
            //echo "Error: " . $e->getMessage();
            dump($e->getMessage());
        }

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/usuario');
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];

        try {
            $this->atendimentoService->delete($id);
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/usuario');
        } catch (Throwable $e) {
            echo "Error -> " . $e->getMessage(); // Implementar Flash Messages
            return $response;
        }
    }
}