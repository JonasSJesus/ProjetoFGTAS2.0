<?php

namespace Fgtas\Entities;

class Atendimento
{
    private int $id;
    public readonly ?string $dataDeRegistro;
    public readonly ?string $usuario;
    public readonly FormaAtendimento $formaAtendimento;
    public readonly TipoAtendimento $tipoAtendimento;
    public readonly Publico $publico;


    public function __construct(
        FormaAtendimento $formaAtendimento,
        TipoAtendimento $tipoAtendimento,
        Publico $publico,
        ?string $dataDeRegistro = null,
        ?string $usuario = null
    ) {
        $this->formaAtendimento = $formaAtendimento;
        $this->tipoAtendimento = $tipoAtendimento;
        $this->publico = $publico;
        $this->setDataRegistro($dataDeRegistro);
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

    public function setDataRegistro(?string $data = null): void
    {
        if (!empty($data)) {
            $date = strtotime($data);
            $dateFormated = date('d/m/Y', $date);

            $this->dataDeRegistro = $dateFormated;
        }
    }

    public static function make(
        string $tipoAtendimento,
        ?string $descricaoAtendimento,
        string $formaAtendimento,
        string $perfilPublico,
        ?string $nomeUsuario = null,
        ?string $dataDeRegistro = null
    ): Atendimento
    {
        $tipo = new TipoAtendimento($tipoAtendimento, $descricaoAtendimento);
        $forma = new FormaAtendimento($formaAtendimento);
        $publico = new Publico($perfilPublico);

        return new Atendimento($forma, $tipo , $publico, $dataDeRegistro, $nomeUsuario);
    }

    public static function fromArray(array $data): Atendimento
    {
        $tipoAtendimento = new TipoAtendimento($data['tipo'], $data['descricao']);
        $formaAtendimento = new FormaAtendimento($data['forma']);
        $publico = new Publico($data['perfil_cliente']);

        if (in_array($data['perfil_cliente'], ['Empregador', 'Trabalhador'])){
            $publico->setExtraFields(
                $data['nome_publico'],
                $data['documento'],
                $data['contato']
            );
        }

        $atendimento = new Atendimento(
            $formaAtendimento,
            $tipoAtendimento,
            $publico,
            $data['data_de_registro'],
            $data['nome_atendente']
        );
        $atendimento->setId($data['id']);

        return $atendimento;
    }
}
