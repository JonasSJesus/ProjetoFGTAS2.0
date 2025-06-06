<?php

namespace Fgtas\Controllers;

use Exception;
use Fgtas\Exceptions\DatabaseException;
use Fgtas\Services\AtendimentoService;
use Fgtas\Validations\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;
use Slim\Flash\Messages;
use Slim\Views\Twig;

/**
 * Responsavel por lidar com a comunicação entre o usuario e tudo que for relacionado aos Atendimentos no sistema
 */
class AtendimentoController
{
    private AtendimentoService $atendimentoService;
    private Validator $validator;
    private Twig $twig;
    private Messages $flash;

    public function __construct(AtendimentoService $atendimentoService, Validator $validator, Twig $twig, Messages $flash)
    {
        $this->atendimentoService = $atendimentoService;
        $this->validator = $validator;
        $this->twig = $twig;
        $this->flash = $flash;
    }


    public function formsAtendimentoPage(Request $request, Response $response): Response
    {
        $userLogged = $_SESSION['user'];
        $flashValidate = $this->flash->getMessage('atendimento-validate');
        $flashCreate = $this->flash->getMessage('atendimento-create');
        $flashDestroy = $this->flash->getMessage('atendimento-destroy');

        return $this->twig->render($response, '/views/formulario.html.twig', [
            'userName' => $userLogged,
            'validation' => $flashValidate[0],
            'create' => $flashCreate[0],
            'destroy' => $flashDestroy[0]
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

        return $this->twig->render($response, '/views/dashboard.html.twig', [
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
//        dd($dataFromRequest);

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
            $this->flash->addMessage('atendimento-validate', $this->validator->getErrors());

            return $response
                ->withStatus(302)
                ->withHeader('Location', '/home');
        }

        try {
            $this->atendimentoService->createAtendimento($dataFromRequest, $_SESSION['user']['id']);
        } catch (DatabaseException $e) {
            $this->flash->addMessage('atendimento-create', $e->getMessage());

            return $response
                ->withStatus(302)
                ->withHeader('Location', '/home');
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
                ->withHeader('Location', '/dashboard');
        } catch (Exception $e) {
            $this->flash->addMessage('atendimento-destroy', $e->getMessage()); // TODO: Implementar a visualizacao na pagina dashboard

            return $response
                ->withStatus(302)
                ->withHeader('Location', '/dashboard');
        }
    }
}