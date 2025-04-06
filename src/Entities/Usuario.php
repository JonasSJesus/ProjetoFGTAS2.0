<?php

namespace Fgtas\Entities;

class Usuario
{
    public readonly ?int $id;
    public readonly string $nome;
    public readonly string $email;
    public readonly string $senha;
    public readonly string $cargo; // Atendente ou adm
    public readonly ?string $ativo;

    /**
     * @param int|null $id
     * @param string $nome
     * @param string $email
     * @param string $senha
     * @param string $cargo
     * @param string|null $ativo
     */
    public function __construct(string $nome, string $email, string $senha, string $cargo, ?string $ativo = 'S', ?int $id = null)
    {
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->cargo = $cargo;
        $this->ativo = $ativo;
        $this->id = $id;
    }


}