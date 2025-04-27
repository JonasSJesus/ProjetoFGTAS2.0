<?php

namespace Fgtas\Services\Reports;

use DI\Container;
use Dompdf\Dompdf;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PdfReportService
{
    private Twig $twig;
    private Dompdf $dompdf;

    public function __construct(Container $container)
    {
        $this->twig = $container->get(Twig::class);
        $this->dompdf = $container->get(Dompdf::class);
    }

    public function generate(array $atendimento, array $filters): void
    {
        $twigEnv = $this->twig->getEnvironment();
        try {
            $template = $twigEnv->render('/templates/reports/reportTemplate.html.twig', [
                'atendimentos' => $atendimento
            ]);
        }catch (LoaderError|RuntimeError|SyntaxError $e) {
            echo $e->getMessage(); // Flash Messages
            return;
        }

        $filename = uniqid('relatorio_') . "_" . date("H:i-d_m_Y") .".pdf";

        $this->dompdf->loadHtml($template);
        $this->dompdf->setPaper('A4', 'landscape');
        $this->dompdf->render();

        $this->dompdf->stream($filename); // TODO: Deixar que o controller lide com o download do arquivo usando Headers.
        // return $filename; // TODO: Implementar...
    }
}