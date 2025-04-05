<?php

namespace Fgtas\Entities;

class FormaAtendimento
{
    public readonly int $id;
    public readonly string $forma; // apenas atributos padrao (presencial, whatsapp, ligação telefônica, e-mail, redes socias, teams, outra(personalizado))
}