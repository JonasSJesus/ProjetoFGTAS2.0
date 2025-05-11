<?php

namespace Fgtas\Controllers;

use Exception;
use Fgtas\Services\AtendimentoService;
use Fgtas\Validations\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;
use Slim\Views\Twig;

/**
 * Responsavel por lidar com a comunicação entre o usuario e tudo que for relacionado aos Atendimentos no sistema
 */
class AtendimentoController
{
    private AtendimentoService $atendimentoService;
    private Validator $validator;
    private Twig $twig;

    public function __construct(AtendimentoService $atendimentoService, Validator $validator, Twig $twig)
    {
        $this->atendimentoService = $atendimentoService;
        $this->validator = $validator;
        $this->twig = $twig;
    }


    public function formsAtendimentoPage(Request $request, Response $response): Response
    {
        $userLoggedIn = $_SESSION['user'];

        return $this->twig->render($response, '/views/formulario_de_testes.html.twig', [
            'userName' => $userLoggedIn
        ]);
    }


    public function updateAtendimentoPage(Request $request, Response $response): Response
    {
        $userLoggedIn = $_SESSION['user'];

        return $this->twig->render($response, '/views/editar_atendimento.html.twig', [
            'userName' => $userLoggedIn
        ]);
    }


    public function dashboardPage(Request $request, Response $response): Response
    {
        $atendimentos = $this->atendimentoService->all();

        return $this->twig->render($response, '/views/usuario.html.twig', [
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

        $rules = [
            'identificacaoAtendente' => v::notEmpty(),
            'formaAtendimento' => v::notEmpty(),
            'perfilPublico' => v::notEmpty(),
            'tipoAtendimento' => v::notEmpty(),
        ];

        if (in_array($dataFromRequest['perfilPublico'], ['empregador', 'trabalhador'])) {
            $rules['nomePublico'] = v::notEmpty();
            $rules['contatoPublico'] = v::notEmpty()->email();
            $rules['documentoPublico'] = $dataFromRequest['perfilPublico'] === 'empregador' ? v::cnpj() : v::cpf();
        }

        $this->validator->validate($dataFromRequest, $rules);

        if ($this->validator->failed()) {
            dump($this->validator->getErrors()); // Todo: Flash Messages

            return $response;
//                ->withStatus(302)
//                ->withHeader('Location', '/home');
        }

        try {
            $this->atendimentoService->createAtendimento($dataFromRequest, $_SESSION['user']['id']);
        } catch (Exception $e) {
            dump($e->getMessage()); // Todo: Flash Messages
        }

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/home');
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
        } catch (Exception $e) {
            echo "Error -> " . $e->getMessage(); // Implementar Flash Messages
            return $response;
        }
    }
}