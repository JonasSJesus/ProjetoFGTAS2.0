<?php

namespace Fgtas\Controllers;

use Dompdf\Dompdf;
use Fgtas\Entities\Atendimento;
use Fgtas\Services\AtendimentoService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ReportController
{
    private AtendimentoService $atendimentoService;

    public function __construct(AtendimentoService $atendimentoService)
    {
        $this->atendimentoService = $atendimentoService;
    }


    public function reportPage(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);
        $atendimentos = $this->atendimentoService->all();

        return $view->render($response, 'relatorio.html.twig', [
            'atendimentos' => $atendimentos
        ]);
    }

    public function generateReport(Request $request, Response $response): Response
    {
        $requestData = $request->getParsedBody();
        $atendimento = $this->atendimentoService->all();

        switch ($requestData['exp']) {
            case 'pdf':
                $this->generatePDF($response, $atendimento, $requestData);
                break;
            case 'csv':
                $response = $this->generateCSV($response, $atendimento, $requestData);
                break;
            default:
                echo "Formato invalido"; // Flash Message.
                break;
        }

        return $response;
    }

    private function generatePDF(Response $response, array $atendimento, array $filters): Response
    {
        $filename = uniqid('relatorio_') . "_" . date("H:i-d_m_Y") .".pdf";
//        dd($filename);

        $dompdf = new Dompdf();

        ob_start();
        require_once ROOT_APP . '/resources/templates/reports/reportTemplate.html.twig'; // TODO: Tirar o require. Carregar a template usando twig
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream($filename);

        return $response;
    }

    private function generateCSV(Response $response, array $atendimento, array $filters): Response
    {
        $filename = uniqid('relatorio_') . "_" . date("H:i-d_m_Y") .".csv";
//        dd($filename);

        $dateFrom = '';
        $dateTo = '';
        if(isset($atendimento['dateInicio'])){
            $dateFrom = date('Y-m-d H:i:s', strtotime());
        }
        if(isset($atendimento['dateFim'])){
            $dateTo = date('Y-m-d H:i:s', strtotime());
        }

        $header = ['id Atendimento',
            'Forma de Atendimento',
            'Data de Registro',
            'Nome',
            'Tipo de Atendimento',
            'Descrição do Tipo de Atendimento',
            'Perfil Publico'
        ];

        $output = fopen('php://output', 'w');
        fputcsv($output, $header);
        foreach ($atendimento as $row) {
            fputcsv($output, $row->toArray());
        }
        fclose($output);

        return $response
            ->withHeader("Content-Type", "application/csv")
            ->withHeader("Content-Disposition", "attachment; filename=$filename");
    }
}