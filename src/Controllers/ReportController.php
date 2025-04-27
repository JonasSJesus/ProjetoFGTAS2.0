<?php

namespace Fgtas\Controllers;

use Dompdf\Dompdf;
use Fgtas\Services\AtendimentoService;
use Fgtas\Services\Reports\CsvReportService;
use Fgtas\Services\Reports\PdfReportService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
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

    public function __construct(AtendimentoService $atendimentoService, PdfReportService $pdfReportService, CsvReportService $csvReportService)
    {
        $this->atendimentoService = $atendimentoService;
        $this->pdfReportService = $pdfReportService;
        $this->csvReportService = $csvReportService;
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
        $view = Twig::fromRequest($request);
        $atendimentos = $this->atendimentoService->all();

        return $view->render($response, 'relatorio.html.twig', [
            'atendimentos' => $atendimentos
        ]);
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

        switch ($requestData['exp']) {
            case 'pdf':
                $this->pdfReportService->generate($atendimento, $requestData);

                return $response;
            case 'csv':
                $filename = $this->csvReportService->generate($atendimento, $requestData);

                return $response
                    ->withHeader("Content-Type", "application/csv")
                    ->withHeader("Content-Disposition", "attachment; filename=$filename");
            default:
                echo "Formato invalido"; // Flash Message.
                break;
        }

        return $response;
    }
}