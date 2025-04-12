<?php

namespace Fgtas\Entities;

use DateTime;

class Atendimento
{
    public readonly int $id;
    public readonly DateTime $dataDeRegistro;
    public readonly FormaAtendimento $formaAtendimento;
    public readonly TipoAtendimento $tipoAtendimento;
    public readonly Usuario $usuario;
    public readonly Publico $publico;


    public function __construct(
        DateTime $dataDeRegistro,
        FormaAtendimento $formaAtendimento,
        TipoAtendimento $tipoAtendimento,
        Usuario $usuario,
        Publico $publico
    ) {
        $this->dataDeRegistro = $dataDeRegistro;
        $this->formaAtendimento = $formaAtendimento;
        $this->tipoAtendimento = $tipoAtendimento;
        $this->usuario = $usuario;
        $this->publico = $publico;
    }


}