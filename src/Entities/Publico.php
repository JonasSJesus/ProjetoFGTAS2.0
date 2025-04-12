<?php

namespace Fgtas\Entities;

class Publico
{
    private int $id;
    public readonly string $perfilCliente; // (empregador, trabalhador, fgtas, ads, interessados mercado de trabalho, outras agencias, outra(personalizado))
    private ?CamposPublico $camposPublico = null;

    public function __construct(string $perfilCliente)
    {
        $this->perfilCliente = $perfilCliente;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function haveExtraInfo(): bool
    {
        return $this->camposPublico instanceof CamposPublico;
    }

    public function getCamposPublico(): ?CamposPublico
    {
        return $this->camposPublico;
    }

    public function setCamposPublico(?CamposPublico $camposPublico): void
    {
        $this->camposPublico = $camposPublico;
    }

    public static function fromArray(array $data): Publico
    {
        $publico = new Publico($data['perfil_cliente']);
        $publico->setId($data['id']);

        return $publico;
    }
}
