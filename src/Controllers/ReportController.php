<?php

namespace Fgtas\Controllers;

use Dompdf\Dompdf;
use Fgtas\Services\AtendimentoService;
use Fgtas\Services\Reports\CsvReportService;
use Fgtas\Services\Reports\PdfReportService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class ReportController
{
    private AtendimentoService $atendimentoService;
    private PdfReportService $pdfReportService;
    private CsvReportService $csvReportService;
    private Twig $twig;
    private Messages $flash;

    public function __construct(AtendimentoService $atendimentoService, PdfReportService $pdfReportService, CsvReportService $csvReportService, Twig $twig, Messages $flash)
    {
        $this->atendimentoService = $atendimentoService;
        $this->pdfReportService = $pdfReportService;
        $this->csvReportService = $csvReportService;
        $this->twig = $twig;
        $this->flash = $flash;
    }


    /**
     * Renderiza a página de geração de relatórios
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function reportPage(Request $request, Response $response): Response
    {
        return $this->twig->render(
            $response,'/views/relatorio.html.twig');
    }


    /**
     * Gera relatórios.
     * Formatos suportados até o momento:
     * PDF e CSV
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function generateReport(Request $request, Response $response): Response
    {
        $requestData = $request->getParsedBody();
        $atendimento = $this->atendimentoService->all();


        if (!array_key_exists('exp', $requestData)) {
            $this->flash->addMessage('erro', 'Formato Inválido, por favor, escolha entre PDF ou CSV');
            dd($_SESSION);

            return $response
                ->withStatus(302)
                ->withHeader('Location', '/generate-report');
        }


        switch ($requestData['exp']) {
            case 'pdf':
                $this->pdfReportService->generate($atendimento, $requestData); // TODO: Guardar a mensagem de erro como Flash Message

                return $response;
            case 'csv':
                $filename = $this->csvReportService->generate($atendimento, $requestData);

                return $response
                    ->withHeader("Content-Type", "application/csv")
                    ->withHeader("Content-Disposition", "attachment; filename=$filename");
        }

        return $response;
    }
}