<?php

namespace Fgtas\Entities;

use DateTime;

class Atendimento
{
    private int $id;
    public readonly ?string $dataDeRegistro;
    public readonly ?string $usuario;
    public readonly string $formaAtendimento;
    public readonly ?string $detalhesAtendimento;
    public readonly TipoAtendimento $tipoAtendimento;
    public readonly Publico $publico;


    public function __construct(
        string $formaAtendimento,
        TipoAtendimento $tipoAtendimento,
//        int $idUsuario,
        Publico $publico,
        string $dataDeRegistro = null,
        string $usuario = null
    ) {
        $this->formaAtendimento = $formaAtendimento;
        $this->tipoAtendimento = $tipoAtendimento;
//        $this->idUsuario = $idUsuario;
        $this->publico = $publico;
        $this->dataDeRegistro = $dataDeRegistro;
//        $this->setDataRegistro($dataDeRegistro);
        $this->usuario = $usuario;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setDataRegistro(string $data)
    {

    }

    public static function createAtendimentoInstance(
        string $tipoAtendimento,
        string $descricaoAtendimento,
        string $formaAtendimento,
        string $perfilPublico,
        string $dataDeRegistro,
        string $nomeUsuario
    ) {
        $tipo = new TipoAtendimento($tipoAtendimento, $descricaoAtendimento);
        $publico = new Publico($perfilPublico);

        return new Atendimento($formaAtendimento, $tipo , $publico, $dataDeRegistro, $nomeUsuario);
    }

    public static function fromArray(array $data): Atendimento
    {
        $tipoAtendimento = new TipoAtendimento($data['tipo'], $data['descricao']);
        $publico = new Publico($data['perfil_cliente']);

        $atendimento = new Atendimento(
            $data['forma_atendimento'], 
            $tipoAtendimento, 
            $publico,
            $data['data_de_registro'], 
            $data['nome']
        );
        $atendimento->setId($data['id']);

        return $atendimento;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'forma_atendimento' => $this->formaAtendimento,
            'data_registro' => $this->dataDeRegistro,
            'usuario' => $this->usuario,
            'tipo_atendimento' => $this->tipoAtendimento->tipo,
            'descricao_tipo_atendimento' => $this->tipoAtendimento->descricao,
            'perfil_publico' => $this->publico->perfilCliente
        ];
    }
}