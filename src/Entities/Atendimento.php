<?php

namespace Fgtas\Entities;

use DateTime;

class Atendimento
{
    private int $id;
    public readonly ?DateTime $dataDeRegistro;
    public readonly TipoAtendimento $tipoAtendimento;
    public readonly Usuario $usuario;
    public readonly Publico $publico;
    public readonly FormaAtendimento $formaAtendimento;
    public readonly string $detalhesAtendimento;


    public function __construct(
        FormaAtendimento $formaAtendimento,
        TipoAtendimento $tipoAtendimento,
        Usuario $usuario,
        Publico $publico,
        DateTime $dataDeRegistro = null
    ) {
        $this->formaAtendimento = $formaAtendimento;
        $this->tipoAtendimento = $tipoAtendimento;
        $this->usuario = $usuario;
        $this->publico = $publico;
        $this->dataDeRegistro = $dataDeRegistro;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setDataRegistro(string $data): void
    {

    }
}