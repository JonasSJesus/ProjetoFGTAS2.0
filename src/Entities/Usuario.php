<?php

namespace Fgtas\Entities;

class Usuario
{
    private int $id;
    private string $senha;
    public readonly string $nome;
    public readonly string $email;
    public readonly string $cargo; // Atendente ou adm

    public function __construct(
        string $nome,
        string $email,
        string $cargo,
    ) {
        $this->nome = $nome;
        $this->email = $email;
        $this->cargo = $cargo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setSenha(string $senha): void
    {
        $this->senha = $senha;
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public static function fromArray(array $data): Usuario
    {
        return new Usuario(
            $data['nome'],
            $data['email'],
            $data['cargo']
        );
    }
}