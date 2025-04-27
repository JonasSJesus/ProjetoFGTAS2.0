<?php

namespace Fgtas\Services\Reports;

class CsvReportService
{
    public function generate(array $atendimento): string
    {
        $filename = uniqid('relatorio_') . "_" . date("H:i-d_m_Y") .".csv";

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

        return $filename;
    }
}