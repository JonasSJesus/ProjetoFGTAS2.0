<?php

namespace Fgtas\Controllers;

use Fgtas\Services\AtendimentoService;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Throwable;

class AtendimentoController
{
    private AtendimentoService $atendimentoService;

    public function __construct(AtendimentoService $atendimentoService)
    {
        $this->atendimentoService = $atendimentoService;
    }

    public function formsAtendimento(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        $userLoggedIn = $_SESSION['user'];

        return $view->render($response, 'formulario_de_testes.html.twig', [
            'userName' => $userLoggedIn
        ]);
    }

    public function dashboard(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        $atendimentos = $this->atendimentoService->all();

        return $view->render($response, 'usuario.html.twig', [
            'atendimentos' => $atendimentos
        ]);
    }

    public function store(Request $request, Response $response): Response
    {
        $dataFromRequest = $request->getParsedBody();

        // Validacoes de dados | TODO: mudar isso para uma camada "Validator"
        $dataValidation = v::key('identificacaoAtendente', v::notEmpty()->stringType())
                        ->key('formaAtendimento', v::notEmpty()->stringType())
                        ->key('perfilPublico', v::notEmpty())
                        ->key('tipoAtendimento')
                        ->key('descricao_tipo_atendimento', v::optional(v::notEmpty()));

        if ($dataFromRequest['perfilPublico'] === 'empregador' || $dataFromRequest['perfilPublico'] === 'trabalhador') {

            $publicoValidate = v::key('nomePublico', v::notEmpty())
                            ->key('documentoPublico', v::notEmpty()->cpf())
                            ->key('contatoPublico', v::notEmpty());

            /** @var $publicoValidate v */
            if (!$publicoValidate->isValid($dataFromRequest)) {
                echo "O perfil do publico era {$dataFromRequest['perfilPublico']}, mas faltou os documentos"; // TODO Criar flash messages aqui!

                return $response;
            }
        }

        /** @var $dataValidation v */
        if (!$dataValidation->isValid($dataFromRequest)) {
            dump($dataFromRequest);
            echo "Os testes deram falha"; // TODO criar flash messages aqui!

            return $response;
        }

        try {
            $this->atendimentoService->createAtendimento($dataFromRequest);
        } catch (Throwable $e) {
            echo "Error: " . $e->getMessage();
        }
//        $this->atendimentoService->createAtendimento($dataFromRequest);

        return $response;
    }
}