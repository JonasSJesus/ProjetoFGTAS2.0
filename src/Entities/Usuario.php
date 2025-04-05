<?php

namespace Fgtas\Entities;

class Usuario
{
    public readonly int $id;
    public readonly string $nome;
    public readonly string $cargo;
    public readonly string $email;
    public readonly string $senha;
    public readonly bool $ativo;
}