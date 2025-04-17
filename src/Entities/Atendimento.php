<?php

namespace Fgtas\Entities;

use DateTime;

class Atendimento
{
    private int $id;
    public readonly ?string $dataDeRegistro;
    public readonly TipoAtendimento $tipoAtendimento;
//    public readonly int $idUsuario;
    public readonly Publico $publico;
    public readonly FormaAtendimento $formaAtendimento;
    public readonly ?string $detalhesAtendimento;
    public readonly ?string $usuario;


    public function __construct(
        FormaAtendimento $formaAtendimento,
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

    public function setDataRegistro(string $data): void
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
        $forma = new FormaAtendimento($formaAtendimento);
        $publico = new Publico($perfilPublico);

        $atendimento = new Atendimento($forma, $tipo , $publico, $dataDeRegistro, $nomeUsuario);

        return $atendimento;
    }

    public static function fromArray(array $data): Atendimento
    {
        $tipoAtendimento = new TipoAtendimento($data['tipo'], $data['descricao_tipo_atendimento']);
        $formaAtendimento = new FormaAtendimento($data['forma']);
        $publico = new Publico($data['perfil_cliente']);

        $atendimento = new Atendimento($formaAtendimento, $tipoAtendimento, $publico, $data['data_de_registro'], $data['nome']);
        $atendimento->setId($data['id']);

        return $atendimento;
    }
}