<?php

namespace Fgtas\Entities;

class CamposPublico
{
    private int $id;
    public readonly string $nome;
    public readonly string $documento;
    public readonly string $contato;

    public function __construct(string $nome, string $documento, string $contato)
    {
        $this->nome = $nome;
        $this->documento = $documento;
        $this->contato = $contato;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}