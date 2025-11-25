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
        $flashValidate = $this
            ->flash
            ->getMessage('atendimento-validate');

        $flashCreateSuccess = $this
            ->flash
            ->getMessage('atendimento-create-success');

        $flashCreateError = $this
            ->flash
            ->getMessage('atendimento-create-error');

        $flashDestroy = $this
            ->flash
            ->getMessage('atendimento-destroy');

        return $this->twig->render($response, '/views/formulario.html.twig', [
            'userName' => $userLogged,
            'validation' => $flashValidate[0] ?? null,
            'createSuccess' => $flashCreateSuccess[0] ?? null,
            'createError' => $flashCreateError[0] ?? null,
            'destroy' => $flashDestroy[0] ?? null
        ]);
    }


    public function updateAtendimentoPage(Request $request, Response $response, array $args): Response
    {
        $userLoggedIn = $_SESSION['user'];
        $atendimento = $this
            ->atendimentoService
            ->get($args['id']);

        $flashValidate = $this
            ->flash
            ->getMessage('atendimento-validate');

        $flashUpdateSuccess = $this
            ->flash
            ->getMessage('atendimento-update-success');

        $flashUpdateError = $this
            ->flash
            ->getMessage('atendimento-update-error');

        return $this->twig->render($response, '/views/editar_atendimento.html.twig', [
            'userName' => $userLoggedIn,
            'atendimento' => $atendimento,
            'validation' => $flashValidate[0],
            'updateSuccess' => $flashUpdateSuccess[0],
            'updateError' => $flashUpdateError[0]
        ]);
    }


    public function dashboardPage(Request $request, Response $response): Response
    {
        $atendimentos = $this->atendimentoService->listAtendimentos();
        $flashReportError = $this->flash->getMessage('report-error');

        return $this->twig->render($response, '/views/dashboard.html.twig', [
            'atendimentos' => $atendimentos,
            'count' => count($atendimentos),
            'reportError' => $flashReportError[0] ?? null
        ]);
    }



    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function store(Request $request, Response $response): Response
    {
        $dataFromRequest = $this->validateForms($request, $response);

        if (!$dataFromRequest) {
            $this->flash->addMessage('atendimento-validate', $this->validator->getErrors());

            return $response
                ->withStatus(302)
                ->withHeader('Location', '/home');
        }

        try {
            $this->atendimentoService->createAtendimento($dataFromRequest, $_SESSION['user']['id']);

            $this->flash->addMessage('atendimento-create-success', "Atendimento criado com sucesso!");
        } catch (DatabaseException $e) {
            $this->flash->addMessage('atendimento-create-error', $e->getMessage());

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
    public function update(Request $request, Response $response, array $args): Response
    {
        $dataFromRequest = $this->validateForms($request, $response);

        if (!$dataFromRequest) {
            $this->flash->addMessage('atendimento-validate', $this->validator->getErrors());

            return $response
                ->withStatus(302)
                ->withHeader('Location', '/update-atendimento/' . $args['id']);
        }

        try {
            $this->atendimentoService->updateAtendimento($dataFromRequest, $args['id']);

            $this->flash->addMessage('atendimento-update-success', "Atendimento atualizado com sucesso!");
        } catch (DatabaseException $e) {
            $this->flash->addMessage('atendimento-update-error', $e->getMessage());
        }

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/update-atendimento/' . $args['id']);
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


    private function validateForms(Request $request, Response $response): array|bool
    {
        $dataFromRequest = $request->getParsedBody();

        $rules = [
            'identificacaoAtendente' => v::notEmpty(),
            'formaAtendimento' => v::notEmpty(),
            'perfilPublico' => v::notEmpty(),
            'tipoAtendimento' => v::notEmpty(),
        ];

        if (in_array($dataFromRequest['perfilPublico'], ['Empregador', 'Trabalhador'])) {
            $rules['nomePublico'] = v::notEmpty();
            $rules['contatoPublico'] = v::notEmpty()->email();
            $rules['documentoPublico'] = $dataFromRequest['perfilPublico'] === 'Empregador' ? v::cnpj() : v::cpf();
        }

        $this->validator->validate($dataFromRequest, $rules);

        if ($this->validator->failed()) {
            return false;
        }

        return $dataFromRequest;
    }
}