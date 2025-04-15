<?php

namespace Fgtas\Entities;

class TipoAtendimento
{
    private int $id;
    public readonly string $tipo; // apenas valores padrao ("carteira de trabalho, SD, vagas", "programa gaucho de artesanato", "vida centro humanistico", "orientações sobre empreendedorismo", "orientações sobre cursos de qualificação", "informações sobre mercado de trabalho")
    public readonly ?string $descricao; // nome, CPF/CNPJ e contato (empregador, trabalhador ou "outros")

    public function __construct(string $tipo, ?string $descricao = null)
    {
        $this->setTipo($tipo);
        $this->descricao = $descricao;
    }

    private function setTipo(string $tipo): void
    {
        $tipoPermitidos = [
            "carteira de trabalho, SD, vagas",
            "programa gaucho de artesanato",
            "Vida Centro Humanistico",
            "orientações sobre empreendedorismo",
            "orientações sobre cursos de qualificação",
            "informações sobre mercado de trabalho"
        ];

        if (!in_array($tipo, $tipoPermitidos)) {
            throw new \Exception("Tipo de Atendimento não permitido!");
        }

        $this->tipo = $tipo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public static function fromArray(array $data): TipoAtendimento
    {
        return new TipoAtendimento($data['tipo'], $data['descricao']);
    }
}