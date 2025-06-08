<?php

namespace Fgtas\Entities;

class Publico
{
    private int $id;
    public readonly string $perfilCliente; // (empregador, trabalhador, fgtas, ads, interessados mercado de trabalho, outras agencias, outra(personalizado))
    private ?CamposPublico $informacoesPessoais = null;

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

    public function haveExtraFields(): bool
    {
        return $this->informacoesPessoais instanceof CamposPublico;
    }


    public function getExtraFields(): ?CamposPublico
    {
        if (!$this->haveExtraFields()) {
            return null;
        }
        return $this->informacoesPessoais;
    }

    public function setExtraFields(string $nome, string $documento, string $contato): void
    {
        // TODO: Verificar se o usuario precisa de campos extras (se for empregador ou trabalhador)
        $this->informacoesPessoais = new CamposPublico($nome, $documento, $contato);
    }

    public static function fromArray(array $data): Publico
    {
        $publico = new Publico($data['perfil_cliente']);
        $publico->setId($data['id']);

        if ($publico->perfilCliente == 'empregador' || $publico->perfilCliente == 'trabalhador') {
            $publico->setExtraFields($data['nome'], $data['documento'], $data['contato']);
        }

        return $publico;
    }
}
