<?php

namespace Fgtas\Entities;

class CamposPublico
{
    private int $id;
    public readonly string $nome;
    public readonly string $contato;
    public readonly string $documento;

    public function __construct(string $nome, string $contato, string $documento)
    {
        $this->nome = $nome;
        $this->contato = $contato;
        $this->documento = $documento;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function showAll(): string // Remover metodo em producao
    {
        return "{nome: $this->nome, contato: $this->contato, documento: $this->documento}";
    }
}