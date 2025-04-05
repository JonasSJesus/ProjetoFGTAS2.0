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
}