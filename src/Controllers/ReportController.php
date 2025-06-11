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
     *  Gera relatórios.
     *  Formatos suportados até o momento:
     *  PDF e CSV
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generateReport(Request $request, Response $response): Response
    {
        $requestData = $request->getParsedBody();

        if (!array_key_exists('exp', $requestData) && empty($requestData['exp'])) {
            $this->flash->addMessage('report-error', 'Erro ao gerar relatório, por favor marque uma opção entre PDF ou CSV');

            return $response
                ->withStatus(302)
                ->withHeader('Location', '/dashboard');
        }

        $atendimento = $this->atendimentoService->listAtendimentos($requestData);

        if (!$atendimento) {
            $this->flash->addMessage('report-error', 'Nenhum atendimento encontrado. Tente trocar os filtros. caso o problema persista, entre em contato com um superior');

            return $response
                ->withStatus(302)
                ->withHeader('Location', '/dashboard');
        }

        switch ($requestData['exp']) {
            case 'pdf':
                $this->pdfReportService->generate($atendimento); // TODO: Guardar a mensagem de erro como Flash Message

                return $response;
            case 'csv':
                $filename = $this->csvReportService->generate($atendimento);

                return $response
                    ->withHeader("Content-Type", "application/csv")
                    ->withHeader("Content-Disposition", "attachment; filename=$filename");
        }

        return $response;
    }
}