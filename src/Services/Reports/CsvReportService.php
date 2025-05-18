<?php

namespace Fgtas\Services\Reports;

use Fgtas\Entities\Atendimento;

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

        /** @var Atendimento $row */
        foreach ($atendimento as $row) {
            $toArray = [
                "id" => $row->getId(),
                "Forma de Atendimento" => $row->formaAtendimento->forma,
                "Data de Registro" => $row->dataDeRegistro,
                "Nome" => $row->usuario,
                "Tipo de Atendimento" => $row->tipoAtendimento->tipo,
                "Descrição do Tipo de Atendimento" => $row->tipoAtendimento->tipo,
                "Perfil Publico" => $row->publico->perfilCliente
            ];

            fputcsv($output, $toArray);
        }
        fclose($output);

        return $filename;
    }
}