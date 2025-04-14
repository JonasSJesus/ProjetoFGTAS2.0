<?php

namespace Fgtas\Entities;

use Exception;

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


    public function getExtraFields(): CamposPublico
    {
        if (!$this->haveExtraFields()) {
            throw new Exception("Campos extras não disponíveis para este perfil.");
        }
        return $this->informacoesPessoais;
    }

    public function setExtraFields(string $nome, string $documento, string $contato): void
    {
        $extraFields = new CamposPublico($nome, $documento, $contato);
        $this->informacoesPessoais = $extraFields;
    }

    public static function fromArray(array $data): Publico
    {
        $publico = new Publico($data['perfil_cliente']);
        $publico->setId($data['id']);

        if ($publico->perfilCliente == 'Empregador' || $publico->perfilCliente == 'Trabalhador') {
            $publico->setExtraFields($data['nome'], $data['documento'], $data['contato']);
        }

        return $publico;
    }
}
