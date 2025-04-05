<?php

namespace Fgtas\Entities;

class TipoAtendimento
{
    public readonly int $id;
    public readonly string $tipo; // apenas valores padrao ("carteira de trabalho, SD, vagas", "programa gaucho de artesanato", "vida centro humanistico", "orientações sobre empreendedorismo", "orientações sobre cursos de qualificação", "informações sobre mercado de trabalho")
    public readonly string $descricao; // nome, CPF/CNPJ e contato (empregador, trabalhador ou "outros")
}